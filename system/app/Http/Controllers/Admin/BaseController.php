<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\EncodesIds;

class BaseController extends Controller
{
    use EncodesIds;
    
    /**
     * Encode an ID for use in URLs
     */
    protected function encodeId($id): string
    {
        return $this->getHashids()->encode($id);
    }
    
    /**
     * Decode an ID from URL
     */
    protected function decodeId(string $encoded): int
    {
        $decoded = $this->getHashids()->decode($encoded);
        return $decoded[0] ?? 0;
    }
}
