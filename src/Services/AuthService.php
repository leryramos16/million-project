<?php

namespace Leryr\Mymillionpesoproject\Services;

use Leryr\Mymillionpesoproject\Repositories\AuthTokenRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;
use Leryr\Mymillionpesoproject\Support\Jwt;
use Leryr\Mymillionpesoproject\Support\Validator;

class AuthService
{
    public function __construct(
        private UserRepository $users,
        private AuthTokenRepository $tokens
    ) {
    }

    public function register(string $username, string $email, string $password): array
    {
        $validator = new Validator();
        $data = ['username' => $username, 'email' => $email, 'password' => $password];
        $validator->require($data, 'username')
            ->minLength($data, 'username', 5)
            ->require($data, 'email')
            ->email($data, 'email')
            ->require($data, 'password')
            ->minLength($data, 'password', 8);

        $errors = $validator->errors();

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors['username'] = 'Username must contain only letters, numbers, underscores or dashes';
        }
        if ($this->users->findByUsername($username)) {
            $errors['username'] = 'Username already taken';
        }
        if ($this->users->findByEmail($email)) {
            $errors['email'] = 'Email already registered';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->users->create($username, $email, $hashed);

        return ['success' => true, 'user_id' => $userId];
    }

    public function login(string $usernameOrEmail, string $password): ?array
    {
        $user = $this->users->findByUsernameOrEmail($usernameOrEmail);

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $this->issueTokens($user);
    }

    public function refresh(string $refreshToken): ?array
    {
        $hash = Jwt::hashRefreshToken($refreshToken);
        $record = $this->tokens->findValid($hash);

        if (!$record) {
            return null;
        }

        $user = $this->users->findById((int) $record['user_id']);

        if (!$user) {
            return null;
        }

        $this->tokens->revoke($hash); // rotate

        return $this->issueTokens($user);
    }

    public function logout(string $refreshToken): void
    {
        $this->tokens->revoke(Jwt::hashRefreshToken($refreshToken));
    }

    private function issueTokens(array $user): array
    {
        $accessToken = Jwt::issueAccessToken((int) $user['id'], $user['role']);
        $refreshToken = Jwt::newRefreshToken();

        $this->tokens->store((int) $user['id'], Jwt::hashRefreshToken($refreshToken), Jwt::REFRESH_TTL);

        unset($user['password']);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user,
        ];
    }
}
