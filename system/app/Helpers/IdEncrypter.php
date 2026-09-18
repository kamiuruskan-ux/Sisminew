<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class IdEncrypter
{
    private static $alphabet = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    
    /**
     * Encrypt an ID to URL-safe string (like X0O2rKgMVG)
     */
    public static function encrypt($id): string
    {
        $id = (int) $id;
        // Add salt/obfuscation
        $salt = rand(1000, 9999);
        $combined = ($salt * 1000000) + $id;
        
        // Convert to base62
        $result = '';
        while ($combined > 0) {
            $result = self::$alphabet[$combined % 62] . $result;
            $combined = intdiv($combined, 62);
        }
        
        // Pad to ensure minimum length
        return str_pad($result, 10, self::$alphabet[0], STR_PAD_LEFT);
    }

    /**
     * Decrypt an ID from URL-safe string
     */
    public static function decrypt(string $encrypted): int
    {
        try {
            // Convert from base62
            $combined = 0;
            $encrypted = ltrim($encrypted, self::$alphabet[0]);
            
            for ($i = 0; $i < strlen($encrypted); $i++) {
                $char = $encrypted[$i];
                $pos = strpos(self::$alphabet, $char);
                if ($pos === false) {
                    throw new \Exception('Invalid character');
                }
                $combined = $combined * 62 + $pos;
            }
            
            // Extract original ID
            $id = $combined % 1000000;
            return (int) $id;
        } catch (\Exception $e) {
            abort(404, 'Invalid ID');
        }
    }

    /**
     * Generate a simple URL-friendly hash for ID
     */
    public static function encode($id): string
    {
        return self::encrypt($id);
    }

    /**
     * Decode a hash back to ID
     */
    public static function decode(string $encoded): int
    {
        return self::decrypt($encoded);
    }
}
