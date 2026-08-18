import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';

Future<bool> showQuestConfirmDialog(
  BuildContext context, {
  required String title,
  required String message,
  String confirmLabel = 'Confirm',
}) async {
  final result = await showDialog<bool>(
    context: context,
    builder: (context) => AlertDialog(
      title: Text(title),
      content: Text(message),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(false),
          child: const Text('Cancel'),
        ),
        ElevatedButton(
          onPressed: () => Navigator.of(context).pop(true),
          child: Text(confirmLabel),
        ),
      ],
    ),
  );

  return result ?? false;
}

/// A gold-sealed "quest complete" style success modal.
Future<void> showQuestSuccessDialog(
  BuildContext context, {
  required String title,
  required String message,
  String buttonLabel = 'Continue',
}) {
  return showDialog(
    context: context,
    builder: (context) => _QuestResultDialog(
      title: title,
      message: message,
      buttonLabel: buttonLabel,
      icon: Icons.verified_outlined,
      accent: AppColors.success,
      accentSoft: AppColors.successSoft,
    ),
  );
}

/// A broken-seal style failure modal.
Future<void> showQuestFailureDialog(
  BuildContext context, {
  required String title,
  required String message,
  String buttonLabel = 'Close',
}) {
  return showDialog(
    context: context,
    builder: (context) => _QuestResultDialog(
      title: title,
      message: message,
      buttonLabel: buttonLabel,
      icon: Icons.report_gmailerrorred_outlined,
      accent: AppColors.danger,
      accentSoft: AppColors.dangerSoft,
    ),
  );
}

class _QuestResultDialog extends StatelessWidget {
  const _QuestResultDialog({
    required this.title,
    required this.message,
    required this.buttonLabel,
    required this.icon,
    required this.accent,
    required this.accentSoft,
  });

  final String title;
  final String message;
  final String buttonLabel;
  final IconData icon;
  final Color accent;
  final Color accentSoft;

  @override
  Widget build(BuildContext context) {
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0.7, end: 1),
      duration: const Duration(milliseconds: 320),
      curve: Curves.elasticOut,
      builder: (context, scale, child) => Transform.scale(scale: scale, child: child),
      child: Dialog(
        backgroundColor: Colors.transparent,
        child: Container(
          padding: const EdgeInsets.fromLTRB(24, 32, 24, 24),
          decoration: BoxDecoration(
            color: AppColors.surfaceElevated,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: accent.withValues(alpha: 0.5), width: 1.5),
            boxShadow: [BoxShadow(color: accent.withValues(alpha: 0.25), blurRadius: 30, spreadRadius: 2)],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 72,
                height: 72,
                decoration: BoxDecoration(shape: BoxShape.circle, color: accentSoft, border: Border.all(color: accent, width: 2)),
                child: Icon(icon, color: accent, size: 36),
              ),
              const SizedBox(height: 18),
              Text(title, textAlign: TextAlign.center, style: AppTheme.heading(18)),
              const SizedBox(height: 8),
              Text(message, textAlign: TextAlign.center, style: AppTheme.body(13, color: AppColors.textSecondary)),
              const SizedBox(height: 22),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.of(context).pop(),
                  child: Text(buttonLabel),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
