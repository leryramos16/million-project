import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';

class QuestTypeBadge extends StatelessWidget {
  const QuestTypeBadge({super.key, required this.type});

  final String type;

  @override
  Widget build(BuildContext context) {
    return _Pill(fg: AppColors.typeColor(type), bg: AppColors.typeSoft(type), label: type.replaceAll('_', ' '));
  }
}

class DifficultyBadge extends StatelessWidget {
  const DifficultyBadge({super.key, required this.difficulty});

  final String difficulty;

  @override
  Widget build(BuildContext context) {
    return _Pill(
      fg: AppColors.difficultyColor(difficulty),
      bg: AppColors.difficultySoft(difficulty),
      label: difficulty,
    );
  }
}

class StatusBadge extends StatelessWidget {
  const StatusBadge({super.key, required this.status});

  final String status;

  @override
  Widget build(BuildContext context) {
    return _Pill(fg: AppColors.statusColor(status), bg: AppColors.statusSoft(status), label: status);
  }
}

class _Pill extends StatelessWidget {
  const _Pill({required this.fg, required this.bg, required this.label});

  final Color fg;
  final Color bg;
  final String label;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(8)),
      child: Text(
        label[0].toUpperCase() + label.substring(1),
        style: AppTheme.body(11, color: fg, weight: FontWeight.w600),
      ),
    );
  }
}
