<?php

namespace App\Services;

use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(array $data)
    {
        $user = $this->authRepository->create($data);
        
        // Send email verification notification
        $user->sendEmailVerificationNotification();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'message' => 'Registration successful! Please check your email to verify your account.',
        ];
    }

    public function login(array $data)
    {
        $user = $this->authRepository->findByEmail($data['email']);

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        // Add verification status (users can still login, but we track verification)
        if (!$user->hasVerifiedEmail()) {
            $response['email_verified'] = false;
            $response['message'] = 'Please verify your email address. Check your inbox for the verification link.';
        } else {
            $response['email_verified'] = true;
        }

        return $response;
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();
    }

    public function resendVerificationEmail($user)
    {
        if ($user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Email already verified.'],
            ]);
        }

        $user->sendEmailVerificationNotification();

        return [
            'message' => 'Verification email has been sent. Please check your inbox.',
        ];
    }

    public function sendPasswordResetLink(array $data)
    {
        $user = $this->authRepository->findByEmail($data['email']);

        if (!$user) {
            // Don't reveal if email exists for security
            return [
                'message' => 'If that email address exists in our system, we have sent a password reset link.',
            ];
        }

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            ['email' => $data['email']]
        );

        return [
            'message' => 'If that email address exists in our system, we have sent a password reset link.',
            'status' => $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT,
        ];
    }

    public function resetPassword(array $data)
    {
        $status = \Illuminate\Support\Facades\Password::reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return [
                'message' => 'Password has been reset successfully.',
            ];
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    public function updateProfile($user, array $data)
    {
        $updateData = [];

        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }

        // Email update would require verification, so we'll skip it for now
        // or implement email change verification separately

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        return $user->fresh();
    }

    public function changePassword($user, array $data)
    {
        // Validate current password
        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update password
        $user->password = Hash::make($data['password']);
        $user->save();

        return [
            'message' => 'Password changed successfully.',
        ];
    }
}
