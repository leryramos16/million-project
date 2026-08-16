<?php

namespace Leryr\Mymillionpesoproject\Http;

use Leryr\Mymillionpesoproject\Controllers\Api\AdminController;
use Leryr\Mymillionpesoproject\Controllers\Api\AuthController;
use Leryr\Mymillionpesoproject\Controllers\Api\QuestController;
use Leryr\Mymillionpesoproject\Http\Middleware\AdminMiddleware;
use Leryr\Mymillionpesoproject\Http\Middleware\AuthMiddleware;
use Leryr\Mymillionpesoproject\Repositories\AchievementRepository;
use Leryr\Mymillionpesoproject\Repositories\AuthTokenRepository;
use Leryr\Mymillionpesoproject\Repositories\QuestRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;
use Leryr\Mymillionpesoproject\Services\AchievementService;
use Leryr\Mymillionpesoproject\Services\AuthService;
use Leryr\Mymillionpesoproject\Services\QuestService;
use PDO;

class ApiRouter
{
    /** @var array<int, array{0:string,1:string,2:array,3:?callable}> */
    private array $routes = [];

    public function __construct(private PDO $db)
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $users = new UserRepository($this->db);
        $quests = new QuestRepository($this->db);
        $achievementsRepo = new AchievementRepository($this->db);
        $tokens = new AuthTokenRepository($this->db);

        $achievementService = new AchievementService($achievementsRepo, $users);
        $authService = new AuthService($users, $tokens);
        $questService = new QuestService($quests, $users, $achievementService);

        $auth = new AuthController($authService, $users, $quests, $achievementService);
        $questController = new QuestController($questService);
        $admin = new AdminController($questService, $users);

        $requireAuth = [AuthMiddleware::class, 'authenticate'];
        $requireAdmin = [AdminMiddleware::class, 'requireAdmin'];

        // Auth
        $this->post('/v1/auth/register', [$auth, 'register']);
        $this->post('/v1/auth/login', [$auth, 'login']);
        $this->post('/v1/auth/refresh', [$auth, 'refresh']);
        $this->post('/v1/auth/logout', [$auth, 'logout']);
        $this->get('/v1/me', [$auth, 'me'], $requireAuth);
        $this->get('/v1/me/stats', [$auth, 'stats'], $requireAuth);
        $this->get('/v1/me/achievements', [$auth, 'achievements'], $requireAuth);

        // Quests
        $this->get('/v1/quests', [$questController, 'index']);
        $this->get('/v1/quests/mine', [$questController, 'mine'], $requireAuth);
        $this->get('/v1/quests/accepted', [$questController, 'accepted'], $requireAuth);
        $this->get('/v1/quests/{id}', [$questController, 'show']);
        $this->post('/v1/quests', [$questController, 'store'], $requireAuth);
        $this->post('/v1/quests/{id}/accept', [$questController, 'accept'], $requireAuth);
        $this->post('/v1/quests/{id}/complete', [$questController, 'complete'], $requireAuth);

        // Admin
        $this->get('/v1/admin/quests/pending', [$admin, 'pendingQuests'], $requireAdmin);
        $this->put('/v1/admin/quests/{id}', [$admin, 'updateQuest'], $requireAdmin);
        $this->post('/v1/admin/quests/{id}/publish', [$admin, 'publishQuest'], $requireAdmin);
        $this->get('/v1/admin/stats', [$admin, 'stats'], $requireAdmin);
        $this->get('/v1/admin/users', [$admin, 'users'], $requireAdmin);
    }

    private function get(string $pattern, array $handler, $middleware = null): void
    {
        $this->routes[] = ['GET', $pattern, $handler, $middleware];
    }

    private function post(string $pattern, array $handler, $middleware = null): void
    {
        $this->routes[] = ['POST', $pattern, $handler, $middleware];
    }

    private function put(string $pattern, array $handler, $middleware = null): void
    {
        $this->routes[] = ['PUT', $pattern, $handler, $middleware];
    }

    public function dispatch(string $method, string $path): void
    {
        $request = new Request();

        foreach ($this->routes as [$routeMethod, $pattern, $handler, $middleware]) {
            if ($routeMethod !== $method) {
                continue;
            }

            $regex = '#^' . preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (!preg_match($regex, $path, $matches)) {
                continue;
            }

            if ($middleware) {
                call_user_func($middleware, $request);
            }

            $params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

            [$controller, $action] = $handler;
            $controller->$action($request, $params);
            return;
        }

        JsonResponse::error('Not found', 404);
    }
}
