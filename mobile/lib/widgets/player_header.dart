import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';
import '../core/utils/player_title.dart';
import '../data/models/user.dart';
import 'coin_chip.dart';
import 'user_avatar.dart';
import 'xp_bar.dart';

class PlayerHeader extends StatelessWidget {
  const PlayerHeader({super.key, required this.stats, this.onAvatarTap, this.avatarUploading = false});

  final PlayerStats stats;
  final VoidCallback? onAvatarTap;
  final bool avatarUploading;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 8, 16, 4),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          GestureDetector(
            onTap: onAvatarTap,
            child: Stack(
              clipBehavior: Clip.none,
              children: [
                UserAvatar(url: stats.avatarUrl, radius: 24, username: stats.username),
                if (avatarUploading)
                  Positioned.fill(
                    child: DecoratedBox(
                      decoration: BoxDecoration(color: Colors.black.withValues(alpha: 0.5), shape: BoxShape.circle),
                      child: const Center(
                        child: SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2)),
                      ),
                    ),
                  )
                else if (onAvatarTap != null)
                  Positioned(
                    right: -2,
                    bottom: -2,
                    child: Container(
                      padding: const EdgeInsets.all(3),
                      decoration: BoxDecoration(
                        color: AppColors.primary,
                        shape: BoxShape.circle,
                        border: Border.all(color: AppColors.surface, width: 2),
                      ),
                      child: const Icon(Icons.camera_alt, size: 11, color: AppColors.onPrimary),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(stats.username, style: AppTheme.heading(16), overflow: TextOverflow.ellipsis),
                    ),
                    LevelBadge(level: stats.level),
                    const SizedBox(width: 8),
                    CoinChip(coins: stats.coins),
                  ],
                ),
                const SizedBox(height: 2),
                Text(
                  playerTitleForLevel(stats.level),
                  style: AppTheme.body(11, color: AppColors.textMuted, weight: FontWeight.w600),
                ),
                const SizedBox(height: 8),
                XpBar(progress: stats.xpProgress, xp: stats.xp, requiredXp: stats.requiredXp),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
