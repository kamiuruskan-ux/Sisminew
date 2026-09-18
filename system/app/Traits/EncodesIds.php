<?php

namespace App\Traits;

use Hashids\Hashids;

trait EncodesIds
{
    protected static ?Hashids $hashids = null;

    protected function getHashids(): Hashids
    {
        if (!self::$hashids) {
            self::$hashids = new Hashids(config('hashids.connections.main.salt'), (int) config('hashids.connections.main.length'));
        }
        return self::$hashids;
    }

    protected function encodeId($id): string
    {
        return $this->getHashids()->encode($id);
    }

    protected function decodeId(string $encoded): int
    {
        $decoded = $this->getHashids()->decode($encoded);
        return $decoded[0] ?? 0;
    }

    public static function encodeIdStatic($id): string
    {
        $hashids = new Hashids(config('hashids.connections.main.salt'), (int) config('hashids.connections.main.length'));
        return $hashids->encode($id);
    }

    public static function decodeIdStatic(string $encoded): int
    {
        $hashids = new Hashids(config('hashids.connections.main.salt'), (int) config('hashids.connections.main.length'));
        $decoded = $hashids->decode($encoded);
        return $decoded[0] ?? 0;
    }
    
    public static function encodeMultiple(array $ids): array
    {
        $hashids = new Hashids(config('hashids.connections.main.salt'), (int) config('hashids.connections.main.length'));
        $encoded = [];
        foreach ($ids as $key => $id) {
            $encoded[$key] = is_numeric($id) ? $hashids->encode($id) : $id;
        }
        return $encoded;
    }
}
