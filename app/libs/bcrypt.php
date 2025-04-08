<?php

namespace App\Libs;
use Illuminate\Support\Facades\Hash;
class Bcrypt
{
    public function hash(string $value): string {
        return bcrypt($value);
    }

    public function compare(string $value, string $hashedValue): bool {
        return Hash::check($value, $hashedValue);
    }
}
