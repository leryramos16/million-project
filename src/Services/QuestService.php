<?php

namespace Leryr\Mymillionpesoproject\Services;

use Leryr\Mymillionpesoproject\Repositories\QuestRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;

class QuestService
{
    private const ALLOWED_TYPES = ['main_quests', 'side_quests', 'events'];
    private const ALLOWED_DIFFICULTIES = ['easy', 'medium', 'hard', 'legendary'];
    private const UPLOAD_DIR = __DIR__ . '/../../public/uploads/payments/';

    public function __construct(
        private QuestRepository $quests,
        private UserRepository $users,
        private AchievementService $achievements
    ) {
    }

    public function listApproved(?string $type, int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = max(1, $limit);
        $type = in_array($type, self::ALLOWED_TYPES, true) ? $type : null;
        $offset = ($page - 1) * $limit;

        $total = $this->quests->countApproved($type);

        return [
            'data' => $this->quests->findApproved($type, $limit, $offset),
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit),
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function find(int $id): ?array
    {
        return $this->quests->findById($id);
    }

    public function create(int $userId, array $data, ?array $file): array
    {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');

        if ($title === '' || $description === '') {
            return ['success' => false, 'message' => 'Title and description are required'];
        }

        if (!$file) {
            return ['success' => false, 'message' => 'Payment proof is required'];
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!in_array($file['type'], $allowedTypes, true)) {
            return ['success' => false, 'message' => 'Only JPG and PNG files are allowed'];
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0777, true);
        }

        $fileName = time() . '_' . basename($file['name']);
        $targetPath = self::UPLOAD_DIR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Failed to upload payment proof'];
        }

        $type = in_array($data['type'] ?? null, self::ALLOWED_TYPES, true) ? $data['type'] : 'side_quests';
        $difficulty = in_array($data['difficulty'] ?? null, self::ALLOWED_DIFFICULTIES, true)
            ? $data['difficulty']
            : 'easy';

        $questId = $this->quests->create([
            'title' => $title,
            'description' => $description,
            'payment_proof' => $fileName,
            'xp_reward' => (int) ($data['xp_reward'] ?? 0),
            'coins_reward' => (int) ($data['coins_reward'] ?? 0),
            'type' => $type,
            'difficulty' => $difficulty,
            'status' => 'pending',
            'created_by' => $userId,
        ]);

        return ['success' => true, 'quest_id' => $questId];
    }

    public function accept(int $questId, int $userId): bool
    {
        return $this->quests->accept($questId, $userId);
    }

    /** Owner marks an accepted quest as done: rewards the acceptor, checks achievements. */
    public function complete(int $questId, int $ownerId): array
    {
        $quest = $this->quests->findAcceptedForCompletion($questId, $ownerId);

        if (!$quest) {
            return ['success' => false];
        }

        $acceptedUserId = (int) $quest['accepted_by'];
        $user = $this->users->findById($acceptedUserId);

        if (!$user) {
            return ['success' => false];
        }

        [$newLevel, $newXp] = XpCalculator::applyReward(
            (int) $user['level'],
            (int) $user['xp'],
            (int) $quest['xp_reward']
        );
        $newCoins = (int) $user['coins'] + (int) $quest['coins_reward'];

        $this->users->updateStats($acceptedUserId, $newXp, $newCoins, $newLevel);
        $this->quests->markCompleted($questId);

        $completedCount = $this->quests->countCompletedByUser($acceptedUserId);
        $newAchievements = $this->achievements->checkAndUnlockForCompletedQuests($acceptedUserId, $completedCount);

        return ['success' => true, 'new_achievements' => $newAchievements];
    }

    public function myRequests(int $userId, string $status, int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;

        $total = $this->quests->countMyRequests($userId, $status);

        return [
            'data' => $this->quests->getMyRequests($userId, $status, $limit, $offset),
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit),
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function acceptedByUser(int $userId): array
    {
        return $this->quests->getAcceptedByUser($userId);
    }

    public function adminPending(int $page, int $limit): array
    {
        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;
        $total = $this->quests->countPending();

        return [
            'data' => $this->quests->getPending($limit, $offset),
            'total' => $total,
            'total_pages' => (int) ceil($total / $limit),
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function adminUpdate(int $id, array $data): bool
    {
        $type = in_array($data['type'] ?? null, self::ALLOWED_TYPES, true) ? $data['type'] : 'main_quests';
        $difficulty = in_array($data['difficulty'] ?? null, self::ALLOWED_DIFFICULTIES, true)
            ? $data['difficulty']
            : 'easy';

        return $this->quests->update([
            'id' => $id,
            'title' => trim($data['title'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'xp_reward' => (int) ($data['xp_reward'] ?? 0),
            'coins_reward' => (int) ($data['coins_reward'] ?? 0),
            'type' => $type,
            'difficulty' => $difficulty,
        ]);
    }

    public function adminPublish(int $id): bool
    {
        return $this->quests->publish($id);
    }

    public function adminStats(): array
    {
        return [
            'pending_quests' => $this->quests->countPending(),
            'total_users' => $this->users->count(),
            'completed_today' => $this->quests->countCompletedToday(),
        ];
    }
}
