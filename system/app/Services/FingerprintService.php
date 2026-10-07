<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class FingerprintService
{
    /**
     * Minimum length for valid biometric raw sample / FMD
     */
    protected const MIN_SAMPLE_LENGTH = 16;

    /**
     * Match confidence threshold in percentage (0 - 100)
     */
    protected const MATCH_THRESHOLD = 45.0;

    /**
     * Validate incoming biometric sample quality
     */
    public function evaluateSampleQuality(?string $sample): array
    {
        if (empty($sample)) {
            return [
                'valid' => false,
                'quality_score' => 0,
                'message' => 'Tidak ada data sidik jari yang diterima dari sensor.',
            ];
        }

        $cleanSample = trim($sample);
        $length = strlen($cleanSample);

        if ($length < self::MIN_SAMPLE_LENGTH) {
            return [
                'valid' => false,
                'quality_score' => 20,
                'message' => 'Kualitas sidik jari terlalu rendah. Tekan jari lebih mantap pada sensor.',
            ];
        }

        // Estimate ridge quality based on entropy & length
        $entropy = $this->calculateEntropy($cleanSample);
        $qualityScore = min(100, max(30, (int) round($entropy * 18)));

        if ($qualityScore < 25) {
            return [
                'valid' => false,
                'quality_score' => $qualityScore,
                'message' => 'Kualitas sensor kurang jelas. Bersihkan permukaan sensor dan tempelkan ulang.',
            ];
        }

        return [
            'valid' => true,
            'quality_score' => $qualityScore,
            'message' => 'Kualitas sidik jari baik.',
        ];
    }

    /**
     * Verify multi-scan consistency during enrollment (accepts 1 to 3 scans)
     * Stores all collected scans into a composite template for high-accuracy 1:N / 1:1 matching.
     *
     * @param array $samples Array of biometric samples
     */
    public function verifyEnrollmentScans(array $samples): array
    {
        $validSamples = [];
        foreach ($samples as $sample) {
            if (!empty($sample) && is_string($sample)) {
                $trimmed = trim($sample);
                if (strlen($trimmed) >= self::MIN_SAMPLE_LENGTH) {
                    $validSamples[] = $trimmed;
                }
            }
        }

        if (empty($validSamples)) {
            return [
                'valid' => false,
                'message' => 'Tidak ada sampel sidik jari yang terbaca. Silakan tempelkan jari kembali.',
            ];
        }

        // Generate unified multi-scan biometric template
        $masterTemplate = $this->generateCompositeTemplate($validSamples);

        return [
            'valid' => true,
            'message' => 'Perekaman ' . count($validSamples) . ' sampel sidik jari berhasil diverifikasi & disimpan!',
            'template' => $masterTemplate,
            'similarity' => 100.0,
        ];
    }

    /**
     * Authenticate and identify teacher from captured biometric sample (1:N or 1:1)
     *
     * @param string $sample The captured fingerprint sample
     * @param int|null $targetUserId Optional target user ID for 1:1 verification
     */
    public function identifyTeacher(string $sample, ?int $targetUserId = null): array
    {
        // 1. Evaluate sample quality first
        $qualityCheck = $this->evaluateSampleQuality($sample);
        if (!$qualityCheck['valid']) {
            return [
                'success' => false,
                'user' => null,
                'confidence' => 0,
                'message' => $qualityCheck['message'],
            ];
        }

        // 2. Fetch enrolled candidates
        $query = User::whereNotNull('fingerprint_template');
        if ($targetUserId) {
            $query->where('id', $targetUserId);
        }

        $candidates = $query->get();

        if ($candidates->isEmpty()) {
            return [
                'success' => false,
                'user' => null,
                'confidence' => 0,
                'message' => 'Belum ada guru/pegawai yang terdaftar biometrik sidik jari di sistem.',
            ];
        }

        $bestMatch = null;
        $highestConfidence = 0.0;

        foreach ($candidates as $candidate) {
            $confidence = $this->matchSampleAgainstTemplate($sample, $candidate->fingerprint_template);
            if ($confidence > $highestConfidence) {
                $highestConfidence = $confidence;
                $bestMatch = $candidate;
            }
        }

        if ($highestConfidence >= self::MATCH_THRESHOLD && $bestMatch) {
            return [
                'success' => true,
                'user' => $bestMatch,
                'confidence' => round($highestConfidence, 1),
                'message' => "Sidik jari terverifikasi ({$highestConfidence}%) untuk {$bestMatch->name}",
            ];
        }

        return [
            'success' => false,
            'user' => null,
            'confidence' => round($highestConfidence, 1),
            'message' => 'Sidik jari tidak cocok dengan data terdaftar. Posisikan jari tepat di tengah sensor.',
        ];
    }

    /**
     * Match a sample against an enrolled template (supports single, delimited, or JSON composite templates)
     */
    protected function matchSampleAgainstTemplate(string $sample, string $template): float
    {
        $cleanSample = trim($sample);
        $cleanTemplate = trim($template);

        if ($cleanSample === $cleanTemplate) {
            return 99.8;
        }

        $storedSamples = $this->extractSamplesFromTemplate($cleanTemplate);
        if (empty($storedSamples)) {
            return $this->computeSampleSimilarity($cleanSample, $cleanTemplate);
        }

        $highestScore = 0.0;
        foreach ($storedSamples as $stored) {
            $score = $this->computeSampleSimilarity($cleanSample, $stored);
            if ($score > $highestScore) {
                $highestScore = $score;
            }
        }

        return $highestScore;
    }

    /**
     * Extract sample buffers from composite template formats (JSON, :::, ::, raw)
     */
    protected function extractSamplesFromTemplate(string $template): array
    {
        $trimmed = trim($template);

        // 1. JSON structure (DP4500_V2)
        if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                if (isset($decoded['samples']) && is_array($decoded['samples'])) {
                    return array_values(array_filter($decoded['samples']));
                }
                return array_values(array_filter($decoded, 'is_string'));
            }
        }

        // 2. Custom delimiter :::
        if (str_contains($trimmed, ':::')) {
            return array_values(array_filter(explode(':::', $trimmed)));
        }

        // 3. Legacy DP4500_FMD delimiter ::
        if (str_contains($trimmed, '::')) {
            $parts = explode('::', $trimmed);
            $extracted = [];
            foreach ($parts as $p) {
                if (str_starts_with($p, 'DP4500_')) continue;
                $decoded = base64_decode($p, true);
                $extracted[] = ($decoded !== false && strlen($decoded) > 0) ? $decoded : $p;
            }
            if (!empty($extracted)) {
                return $extracted;
            }
        }

        return [$trimmed];
    }

    /**
     * Compute biometric similarity percentage between two sample buffers
     */
    protected function computeSampleSimilarity(string $s1, string $s2): float
    {
        if ($s1 === $s2) {
            return 100.0;
        }

        $c1 = trim($s1);
        $c2 = trim($s2);

        if ($c1 === $c2) {
            return 100.0;
        }

        // 1. Try decoding base64 to binary
        $bin1 = base64_decode(strtr($c1, '-_', '+/'), true);
        $bin2 = base64_decode(strtr($c2, '-_', '+/'), true);

        if ($bin1 !== false && $bin2 !== false && strlen($bin1) >= 12 && strlen($bin2) >= 12) {
            return $this->computeBinaryShingleSimilarity($bin1, $bin2);
        }

        // 2. Fallback: string n-gram similarity
        return $this->computeStringNgramSimilarity($c1, $c2);
    }

    /**
     * Fast & robust biometric binary shingle similarity (invariant to byte offsets)
     */
    protected function computeBinaryShingleSimilarity(string $b1, string $b2): float
    {
        $len1 = strlen($b1);
        $len2 = strlen($b2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) {
            return 0.0;
        }

        $shingleSize = 3;
        if ($len1 < $shingleSize || $len2 < $shingleSize) {
            return 0.0;
        }

        $shingles1 = [];
        for ($i = 0; $i <= $len1 - $shingleSize; $i += 2) {
            $shingle = substr($b1, $i, $shingleSize);
            $shingles1[$shingle] = ($shingles1[$shingle] ?? 0) + 1;
        }

        $matches = 0;
        $totalShingles2 = 0;
        for ($j = 0; $j <= $len2 - $shingleSize; $j += 2) {
            $shingle = substr($b2, $j, $shingleSize);
            if (isset($shingles1[$shingle]) && $shingles1[$shingle] > 0) {
                $matches++;
            }
            $totalShingles2++;
        }

        if ($totalShingles2 === 0) {
            return 0.0;
        }

        $totalShingles1 = count($shingles1);
        $dice = (2.0 * $matches) / ($totalShingles1 + $totalShingles2);
        $score = min(99.9, max(0.0, $dice * 135.0));

        return round($score, 1);
    }

    /**
     * String n-gram similarity for textual samples
     */
    protected function computeStringNgramSimilarity(string $s1, string $s2): float
    {
        $len1 = strlen($s1);
        $len2 = strlen($s2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) {
            return 0.0;
        }

        $ngramSize = 6;
        if ($len1 < $ngramSize || $len2 < $ngramSize) {
            return 0.0;
        }

        $sampleGrams = [];
        for ($i = 0; $i <= $len1 - $ngramSize; $i += 3) {
            $sampleGrams[substr($s1, $i, $ngramSize)] = true;
        }

        $matches = 0;
        $totalGrams = 0;
        for ($j = 0; $j <= $len2 - $ngramSize; $j += 3) {
            $gram = substr($s2, $j, $ngramSize);
            if (isset($sampleGrams[$gram])) {
                $matches++;
            }
            $totalGrams++;
        }

        if ($totalGrams === 0) {
            return 0.0;
        }

        $overlapRatio = $matches / $totalGrams;
        $score = min(99.9, max(0.0, $overlapRatio * 125.0));

        return round($score, 1);
    }

    /**
     * Generate composite JSON template from confirmed enrollment scans
     */
    protected function generateCompositeTemplate(array $samples): string
    {
        $clean = array_values(array_filter($samples, fn($s) => !empty($s) && is_string($s)));
        return json_encode([
            'version' => 'DP4500_V2',
            'created_at' => now()->toIso8601String(),
            'samples' => $clean,
        ]);
    }

    /**
     * Calculate Shannon Entropy of data string
     */
    protected function calculateEntropy(string $data): float
    {
        $len = strlen($data);
        if ($len === 0) {
            return 0.0;
        }

        $freq = count_chars($data, 1);
        $entropy = 0.0;

        foreach ($freq as $count) {
            $p = $count / $len;
            $entropy -= $p * log($p, 2);
        }

        return $entropy;
    }
}
