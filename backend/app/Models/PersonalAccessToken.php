<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use MongoDB\Laravel\Eloquent\DocumentModel;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use DocumentModel;

    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';
    protected $primaryKey = '_id';
    protected $keyType = 'string';

    /**
     * Find the token instance matching the given token.
     *
     * @param  string  $token
     * @return static|null
     */
    public static function findToken($token)
    {
        if (str_contains($token, '|')) {
            [$id, $token] = explode('|', $token, 2);

            if ($instance = static::find($id)) {
                return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
            }
        }

        return static::where('token', hash('sha256', $token))->first();
    }
}
