import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';

class XpBar extends StatelessWidget {
  const XpBar({super.key, required this.progress, required this.xp, required this.requiredXp});

  final double progress;
  final int xp;
  final int requiredXp;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(8),
          child: Container(
            height: 8,
            color: AppColors.border,
            child: FractionallySizedBox(
              alignment: Alignment.centerLeft,
              widthFactor: progress.clamp(0, 1),
              child: Container(color: AppColors.xpFill),
            ),
          ),
        ),
        const SizedBox(height: 4),
        Text('$xp / $requiredXp XP', style: AppTheme.body(11, color: AppColors.textSecondary)),
      ],
    );
  }
}
