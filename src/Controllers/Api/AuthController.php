<?php

namespace Leryr\Mymillionpesoproject\Controllers\Api;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Repositories\QuestRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;
use Leryr\Mymillionpesoproject\Services\AchievementService;
use Leryr\Mymillionpesoproject\Services\AuthService;

class AuthController
{
    private const AVATAR_UPLOAD_DIR = __DIR__ . '/../../../public/uploads/avatars/';
    private const ALLOWED_AVATAR_TYPES = ['image/jpeg', 'image/png', 'image/jpg'];

    public function __construct(
        private AuthService $auth,
        private UserRepository $users,
        private QuestRepository $quests,
        private AchievementService $achievements
    ) {
    }

    public function register(Request $request): void
    {
        $result = $this->auth->register(
            trim((string) $request->input('username', '')),
            trim((string) $request->input('email', '')),
            (string) $request->input('password', '')
        );

        if (!$result['success']) {
            JsonResponse::error('Registration failed', 422, $result['errors']);
        }

        JsonResponse::success(['user_id' => $result['user_id']], 'Account created! You may now log in.', 201);
    }

    public function login(Request $request): void
    {
        $usernameOrEmail = trim((string) $request->input('username_or_email', ''));
        $password = (string) $request->input('password', '');

        if ($usernameOrEmail === '' || $password === '') {
            JsonResponse::error('Username/email and password are required', 422);
        }

        $result = $this->auth->login($usernameOrEmail, $password);

        if (!$result) {
            JsonResponse::error('Invalid username/email or password', 401);
        }

        JsonResponse::success($result, 'Welcome back, adventurer.');
    }

    public function refresh(Request $request): void
    {
        $token = (string) $request->input('refresh_token', '');

        if ($token === '') {
            JsonResponse::error('refresh_token is required', 422);
        }

        $result = $this->auth->refresh($token);

        if (!$result) {
            JsonResponse::error('Invalid or expired refresh token', 401);
        }

        JsonResponse::success($result, 'Token refreshed');
    }

    public function logout(Request $request): void
    {
        $token = (string) $request->input('refresh_token', '');

        if ($token !== '') {
            $this->auth->logout($token);
        }

        JsonResponse::success(null, 'Logged out');
    }

    public function me(Request $request): void
    {
        $user = $this->users->findById($request->user['id']);

        if (!$user) {
            JsonResponse::error('User not found', 404);
        }

        unset($user['password']);
        JsonResponse::success($user);
    }

    public function stats(Request $request): void
    {
        $user = $this->users->findById($request->user['id']);

        if (!$user) {
            JsonResponse::error('User not found', 404);
        }

        JsonResponse::success([
            'id' => $user['id'],
            'username' => $user['username'],
            'level' => (int) $user['level'],
            'xp' => (int) $user['xp'],
            'coins' => (int) $user['coins'],
            'required_xp' => (int) $user['level'] * 100,
            'profile_image' => $user['profile_image'],
            'completed_quests' => $this->quests->countCompletedByUser($user['id']),
        ]);
    }

    public function achievements(Request $request): void
    {
        JsonResponse::success($this->achievements->listForUser($request->user['id']));
    }

    public function updateAvatar(Request $request): void
    {
        $file = $request->file('avatar');

        if (!$file) {
            JsonResponse::error('No avatar file provided', 422);
        }

        if (!in_array($file['type'], self::ALLOWED_AVATAR_TYPES, true)) {
            JsonResponse::error('Only JPG and PNG files are allowed', 422);
        }

        if (!is_dir(self::AVATAR_UPLOAD_DIR)) {
            mkdir(self::AVATAR_UPLOAD_DIR, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'avatar_' . $request->user['id'] . '_' . time() . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], self::AVATAR_UPLOAD_DIR . $filename)) {
            JsonResponse::error('Failed to save avatar', 500);
        }

        $this->users->updateProfileImage($request->user['id'], $filename);

        JsonResponse::success(['profile_image' => $filename], 'Avatar updated');
    }
}
