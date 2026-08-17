<?php

namespace Leryr\Mymillionpesoproject\Services;

use Leryr\Mymillionpesoproject\Repositories\AchievementRepository;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;

class AchievementService
{
    public function __construct(
        private AchievementRepository $achievements,
        private UserRepository $users
    ) {
    }

    /**
     * Checks every requirement_type this user has progress in against their
     * current stats, unlocking anything newly earned.
     *
     * @param array<string,int> $progress e.g. ['completed_quests' => 5, 'quests_posted' => 2, 'level_reached' => 3]
     * @return array newly unlocked achievement rows
     */
    public function checkAndUnlock(int $userId, array $progress): array
    {
        $newlyUnlocked = [];

        foreach ($progress as $requirementType => $achievedValue) {
            $candidates = $this->achievements->findByRequirementType($requirementType, $achievedValue);

            foreach ($candidates as $achievement) {
                if ($this->achievements->hasUnlocked($userId, (int) $achievement['id'])) {
                    continue;
                }

                $this->achievements->unlock($userId, (int) $achievement['id']);
                $newlyUnlocked[] = $achievement;

                $this->applyBonus($userId, (int) $achievement['xp_bonus'], (int) $achievement['coins_bonus']);
            }
        }

        return $newlyUnlocked;
    }

    private function applyBonus(int $userId, int $xpBonus, int $coinsBonus): void
    {
        if ($xpBonus <= 0 && $coinsBonus <= 0) {
            return;
        }

        $user = $this->users->findById($userId);

        if (!$user) {
            return;
        }

        [$newLevel, $newXp] = XpCalculator::applyReward((int) $user['level'], (int) $user['xp'], $xpBonus);
        $this->users->updateStats($userId, $newXp, (int) $user['coins'] + $coinsBonus, $newLevel);
    }

    /** Full achievement list annotated with the current user's unlock state. */
    public function listForUser(int $userId): array
    {
        $all = $this->achievements->all();
        $unlocked = $this->achievements->unlockedByUser($userId);
        $unlockedIds = array_column($unlocked, 'unlocked_at', 'id');

        return array_map(function ($achievement) use ($unlockedIds) {
            $achievement['unlocked'] = isset($unlockedIds[$achievement['id']]);
            $achievement['unlocked_at'] = $unlockedIds[$achievement['id']] ?? null;
            return $achievement;
        }, $all);
    }
}
