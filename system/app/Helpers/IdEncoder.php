<?php

if (!function_exists('encode_id')) {
    /**
     * Encode an ID using hashids
     */
    function encode_id($id): string
    {
        $hashids = new Hashids\Hashids(config('hashids.connections.main.salt'), (int) config('hashids.connections.main.length'));
        return $hashids->encode($id);
    }
}

if (!function_exists('decode_id')) {
    /**
     * Decode an encoded ID using hashids with fallback for raw numeric ID
     */
    function decode_id($encoded): int
    {
        if (empty($encoded)) {
            return 0;
        }

        try {
            $salt = config('hashids.connections.main.salt', 'sekolah_lrv_salt_2026');
            $length = (int) config('hashids.connections.main.length', 10);
            $hashids = new Hashids\Hashids($salt, $length);
            $decoded = $hashids->decode((string)$encoded);
            
            if (!empty($decoded) && isset($decoded[0]) && is_numeric($decoded[0]) && (int)$decoded[0] > 0) {
                // If it encodes back to the same hashid, it is a valid hashid
                if ($hashids->encode($decoded[0]) === (string)$encoded) {
                    return (int)$decoded[0];
                }
            }
        } catch (\Throwable $e) {
            // Silently fallback to numeric check
        }

        // Fallback for raw integer/numeric ID
        if (is_numeric($encoded) && (int)$encoded > 0) {
            return (int)$encoded;
        }

        return 0;
    }
}

if (!function_exists('encrypt_id')) {
    /**
     * Encrypt an ID to obfuscated URL-safe hash/string
     */
    function encrypt_id($id): string
    {
        return encode_id($id);
    }
}

if (!function_exists('decrypt_id')) {
    /**
     * Decrypt an obfuscated ID back to integer ID
     */
    function decrypt_id($encoded): int
    {
        return decode_id($encoded);
    }
}

if (!function_exists('render_student_card')) {
    /**
     * Render Front & Back Student ID Card Template HTML View.
     */
    function render_student_card($student, bool $showLabel = true)
    {
        return view('components.student-card', compact('student', 'showLabel'))->render();
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format any number into standard Indonesian Rupiah format without decimals.
     * Example: format_rupiah(15000) -> "Rp 15.000"
     *          format_rupiah(15000, false) -> "15.000"
     */
    function format_rupiah($amount, bool $withPrefix = true): string
    {
        $num = (float)($amount ?? 0);
        $formatted = number_format($num, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('save_uploaded_public_file')) {
    /**
     * Save an uploaded file directly to img/ or doc/ with automatic dual-directory sync.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $subfolder Relative subfolder, e.g. 'img/gallery', 'img/blog', 'doc/materials', 'img/questions'
     * @param string|null $customFileName Optional custom filename
     * @return string Relative path saved, e.g. 'img/gallery/17123456_abc.jpg'
     */
    function save_uploaded_public_file($file, string $subfolder, ?string $customFileName = null): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $filename = $customFileName ?? (time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $ext);
        $subfolder = trim($subfolder, '/');
        
        $targetDir = public_path($subfolder);
        if (!\Illuminate\Support\Facades\File::isDirectory($targetDir)) {
            \Illuminate\Support\Facades\File::makeDirectory($targetDir, 0755, true, true);
        }
        
        $file->move($targetDir, $filename);
        
        return $subfolder . '/' . $filename;
    }
}

if (!function_exists('get_public_file_url')) {
    /**
     * Get accessible URL for a public file directly from web root (img/ or doc/).
     */
    function get_public_file_url(?string $path, string $defaultFolder = 'img'): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return $path;
        }
        
        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        if (\Illuminate\Support\Str::startsWith($cleanPath, 'system/public/')) {
            $cleanPath = substr($cleanPath, 14);
        }

        if (!\Illuminate\Support\Str::startsWith($cleanPath, ['img/', 'doc/'])) {
            $folder = trim(str_replace('\\', '/', $defaultFolder), '/');
            if (\Illuminate\Support\Str::startsWith($folder, ['img/', 'doc/'])) {
                $prefix = \Illuminate\Support\Str::startsWith($folder, 'doc/') ? 'doc/' : 'img/';
                $subfolder = preg_replace('/^(img|doc)\//', '', $folder);
                if (!empty($subfolder) && \Illuminate\Support\Str::startsWith($cleanPath, $subfolder . '/')) {
                    $cleanPath = $prefix . $cleanPath;
                } else {
                    $cleanPath = $folder . '/' . $cleanPath;
                }
            } else {
                $cleanPath = ($folder !== '' ? $folder : 'img') . '/' . $cleanPath;
            }
        }
        
        $cleanPath = preg_replace('#^img/+img/#', 'img/', $cleanPath);
        $cleanPath = preg_replace('#^doc/+doc/#', 'doc/', $cleanPath);
        $isSecure = false;
        try {
            $isSecure = request() && (request()->isSecure() || strtolower((string)request()->header('x-forwarded-proto')) === 'https');
        } catch (\Throwable $e) {}

        return $isSecure ? secure_url($cleanPath) : url($cleanPath);
    }
}

if (!function_exists('delete_public_file')) {
    /**
     * Delete a public file directly from public web root (img/ or doc/).
     */
    function delete_public_file(?string $path, ?string $defaultFolder = null): void
    {
        if (empty($path) || \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }
        $cleanPath = ltrim(str_replace('\\', '/', $path), '/');
        $filename = basename($cleanPath);
        
        $subfolders = [
            'img',
            'doc',
            'img/sliders',
            'img/gallery',
            'img/blog',
            'img/avatars',
            'img/books/covers',
            'img/canteen/items',
            'img/canteen/stalls',
            'img/cards',
            'img/extracurriculars',
            'img/financial_proofs',
            'img/questions',
            'img/savings/proofs',
            'img/spmb',
            'img/spp/proofs',
            'img/students',
            'img/teacher_attendances',
            'doc/books/pdf',
            'doc/materials',
            'doc/submissions',
            'doc/bk_documents',
            'doc/teacher_attendances',
            'doc/financial_proofs',
            'doc/spmb/documents',
        ];

        if (!empty($defaultFolder)) {
            array_unshift($subfolders, trim(str_replace('\\', '/', $defaultFolder), '/'));
        }

        $pathsToDelete = [
            public_path($cleanPath),
        ];

        foreach ($subfolders as $sub) {
            $pathsToDelete[] = public_path($sub . '/' . $filename);
            $pathsToDelete[] = public_path($sub . '/' . $cleanPath);
        }
        
        foreach (array_unique($pathsToDelete) as $p) {
            if (\Illuminate\Support\Facades\File::exists($p)) {
                @\Illuminate\Support\Facades\File::delete($p);
            }
        }

        try {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
            \Illuminate\Support\Facades\Storage::disk('public')->delete($filename);
            \Illuminate\Support\Facades\Storage::disk('doc')->delete($cleanPath);
            \Illuminate\Support\Facades\Storage::disk('doc')->delete($filename);
            if (!empty($defaultFolder)) {
                $df = trim($defaultFolder, '/');
                \Illuminate\Support\Facades\Storage::disk('public')->delete($df . '/' . $filename);
                \Illuminate\Support\Facades\Storage::disk('doc')->delete($df . '/' . $filename);
            }
        } catch (\Throwable $e) {}
    }
}
