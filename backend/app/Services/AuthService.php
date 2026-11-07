<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Authenticate an admin and issue an API token.
     *
     * @param  array{email: string, password: string}  $credentials
     * @return array{admin: Admin, token: string}
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $admin = Admin::where('email', strtolower(trim($credentials['email'])))->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if ($admin->status === 'inactive') {
            throw ValidationException::withMessages([
                'email' => ['This account has been deactivated. Please contact an administrator.'],
            ]);
        }

        // Generate personal access token
        $token = $admin->createToken('admin-token', [$admin->role])->plainTextToken;

        $this->auditLogService->log(
            admin: $admin,
            action: 'LOGIN',
            resourceType: 'Admin',
            resourceId: (string) $admin->_id,
            description: "Admin {$admin->email} logged in successfully."
        );

        return [
            'admin' => $admin,
            'token' => $token,
        ];
    }

    /**
     * Invalidate the authenticated admin's current access token.
     */
    public function logout(Admin $admin): void
    {
        $admin->currentAccessToken()?->delete();

        $this->auditLogService->log(
            admin: $admin,
            action: 'LOGOUT',
            resourceType: 'Admin',
            resourceId: (string) $admin->_id,
            description: "Admin {$admin->email} logged out."
        );
    }
}
