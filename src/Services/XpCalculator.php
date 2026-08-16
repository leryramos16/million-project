<?php

namespace Leryr\Mymillionpesoproject\Services;

class XpCalculator
{
    /**
     * Level N requires N*100 XP to clear. Returns [newLevel, newXp] after applying a reward,
     * carrying overflow XP into as many level-ups as it covers.
     */
    public static function applyReward(int $level, int $xp, int $xpReward): array
    {
        $newXp = $xp + $xpReward;
        $newLevel = $level;

        while ($newXp >= ($newLevel * 100)) {
            $newXp -= ($newLevel * 100);
            $newLevel++;
        }

        return [$newLevel, $newXp];
    }
}
