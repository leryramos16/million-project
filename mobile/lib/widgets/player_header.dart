import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';
import '../data/models/user.dart';
import 'coin_chip.dart';
import 'xp_bar.dart';

class PlayerHeader extends StatelessWidget {
  const PlayerHeader({super.key, required this.stats});

  final PlayerStats stats;

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
          CircleAvatar(
            radius: 24,
            backgroundColor: AppColors.primarySoft,
            backgroundImage: const AssetImage('assets/images/logo.png'),
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
