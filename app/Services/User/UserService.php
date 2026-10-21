<?php

namespace App\Services\User;

use App\Models\User;
use RuntimeException;

class UserService
{
    /**
     * Create a user account. Password must be plain text; the User model hashes it.
     *
     * @param  array{
     *     f_name: string,
     *     l_name: string,
     *     email: string,
     *     password: string,
     *     organization_id?: string|null,
     *     username?: string|null
     * }  $attributes
     */
    public function create(array $attributes): User
    {
        $email = strtolower(trim((string) ($attributes['email'] ?? '')));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException(__('site.email_required'));
        }

        $row = [
            'f_name' => $attributes['f_name'],
            'l_name' => $attributes['l_name'],
            'email' => $email,
            'password' => $attributes['password'],
            'organization_id' => $attributes['organization_id'] ?? null,
        ];

        $username = isset($attributes['username']) ? trim((string) $attributes['username']) : '';
        if ($username !== '') {
            $row['username'] = $username;
        }

        return User::query()->create($row);
    }
}
