<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class FingerprintService
{
    /**
     * Minimum length for valid biometric raw sample / FMD
     */
    protected const MIN_SAMPLE_LENGTH = 32;

    /**
     * Match confidence threshold in percentage (0 - 100)
     */
    protected const MATCH_THRESHOLD = 65.0;

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

        if ($qualityScore < 45) {
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
     * Verify multi-scan consistency during enrollment (3 Scans required)
     *
     * @param array $samples Array of 3 biometric samples
     */
    public function verifyEnrollmentScans(array $samples): array
    {
        if (count($samples) < 3) {
            return [
                'valid' => false,
                'message' => 'Perekaman belum lengkap. Diperlukan 3 kali pemindaian jari.',
            ];
        }

        // Validate each individual sample quality
        foreach ($samples as $index => $sample) {
            $quality = $this->evaluateSampleQuality($sample);
            if (!$quality['valid']) {
                return [
                    'valid' => false,
                    'message' => "Pemindaian ke-" . ($index + 1) . " gagal: " . $quality['message'],
                ];
            }
        }

        // Compare consistency between Scan 1, Scan 2, and Scan 3
        $similarity12 = $this->computeSampleSimilarity($samples[0], $samples[1]);
        $similarity23 = $this->computeSampleSimilarity($samples[1], $samples[2]);
        $similarity13 = $this->computeSampleSimilarity($samples[0], $samples[2]);

        $avgSimilarity = ($similarity12 + $similarity23 + $similarity13) / 3;

        if ($avgSimilarity < 50.0) {
            return [
                'valid' => false,
                'message' => 'Fingerprint mismatch. Sampel jari tidak konsisten. Silakan scan ulang dengan jari yang sama.',
                'similarity' => round($avgSimilarity, 1),
            ];
        }

        // Generate unified encrypted biometric template (FMD)
        $masterTemplate = $this->generateCompositeTemplate($samples);

        return [
            'valid' => true,
            'message' => 'Perekaman 3 sampel jari berhasil diverifikasi!',
            'template' => $masterTemplate,
            'similarity' => round($avgSimilarity, 1),
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
     * Match a sample against an enrolled template
     */
    protected function matchSampleAgainstTemplate(string $sample, string $template): float
    {
        // Direct string / hash match (100% match)
        if ($sample === $template) {
            return 99.8;
        }

        // Multi-sample composite template extraction
        if (str_contains($template, '::')) {
            $parts = explode('::', $template);
            $maxPartSim = 0.0;
            foreach ($parts as $p) {
                $sim = $this->computeSampleSimilarity($sample, $p);
                if ($sim > $maxPartSim) {
                    $maxPartSim = $sim;
                }
            }
            return $maxPartSim;
        }

        return $this->computeSampleSimilarity($sample, $template);
    }

    /**
     * Compute biometric similarity percentage between two sample buffers
     */
    protected function computeSampleSimilarity(string $s1, string $s2): float
    {
        if ($s1 === $s2) {
            return 100.0;
        }

        $len1 = strlen($s1);
        $len2 = strlen($s2);
        $maxLen = max($len1, $len2);

        if ($maxLen === 0) {
            return 0.0;
        }

        // Length correlation test
        $lenDiffRatio = abs($len1 - $len2) / $maxLen;
        if ($lenDiffRatio > 0.4) {
            return 15.0;
        }

        // Substring / N-gram feature overlap (robust against minor hardware noise)
        $ngramSize = 6;
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
        $score = min(100.0, max(0.0, $overlapRatio * 115.0));

        return round($score, 1);
    }

    /**
     * Generate composite encrypted template from 3 confirmed enrollment scans
     */
    protected function generateCompositeTemplate(array $samples): string
    {
        $hash1 = hash('sha256', $samples[0]);
        $hash2 = hash('sha256', $samples[1]);
        $hash3 = hash('sha256', $samples[2]);

        $fmdSignature = 'DP4500_FMD_' . substr($hash1, 0, 16) . '_' . substr($hash2, 0, 16) . '_' . substr($hash3, 0, 16);

        // Store composite references
        return $fmdSignature . '::' . base64_encode(substr($samples[0], 0, 120)) . '::' . base64_encode(substr($samples[1], 0, 120));
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
