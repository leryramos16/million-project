import 'package:flutter/material.dart';

/// Clean, modern palette for a mainstream consumer app — light surfaces,
/// one confident accent color, soft pastel tags instead of heavy borders.
class AppColors {
  AppColors._();

  static const background = Color(0xFFF6F7FB);
  static const surface = Colors.white;
  static const border = Color(0xFFE5E7EB);

  static const textPrimary = Color(0xFF16181D);
  static const textSecondary = Color(0xFF6B7280);
  static const textMuted = Color(0xFF9CA3AF);

  static const primary = Color(0xFF5B5FEF);
  static const primarySoft = Color(0xFFEDEEFD);
  static const onPrimary = Colors.white;

  static const success = Color(0xFF16A34A);
  static const successSoft = Color(0xFFE8F8EE);
  static const warning = Color(0xFFD97706);
  static const warningSoft = Color(0xFFFDF3E4);
  static const danger = Color(0xFFDC2626);
  static const dangerSoft = Color(0xFFFCEAEA);
  static const info = Color(0xFF2563EB);
  static const infoSoft = Color(0xFFE9F0FE);

  static const gold = Color(0xFFD97706); // coins accent
  static const xpFill = primary;

  static Color typeColor(String type) {
    switch (type) {
      case 'side_quests':
        return const Color(0xFF0D9488);
      case 'events':
        return const Color(0xFF9333EA);
      case 'main_quests':
      default:
        return primary;
    }
  }

  static Color typeSoft(String type) {
    switch (type) {
      case 'side_quests':
        return const Color(0xFFE1F5F3);
      case 'events':
        return const Color(0xFFF4E9FC);
      case 'main_quests':
      default:
        return primarySoft;
    }
  }

  static Color difficultyColor(String difficulty) {
    switch (difficulty) {
      case 'medium':
        return warning;
      case 'hard':
        return const Color(0xFFEA580C);
      case 'legendary':
        return const Color(0xFF9333EA);
      case 'easy':
      default:
        return success;
    }
  }

  static Color difficultySoft(String difficulty) {
    switch (difficulty) {
      case 'medium':
        return warningSoft;
      case 'hard':
        return const Color(0xFFFCEDE3);
      case 'legendary':
        return const Color(0xFFF4E9FC);
      case 'easy':
      default:
        return successSoft;
    }
  }

  static Color statusColor(String status) {
    switch (status) {
      case 'approved':
        return success;
      case 'accepted':
        return info;
      case 'completed':
        return const Color(0xFF9333EA);
      case 'pending':
      default:
        return warning;
    }
  }

  static Color statusSoft(String status) {
    switch (status) {
      case 'approved':
        return successSoft;
      case 'accepted':
        return infoSoft;
      case 'completed':
        return const Color(0xFFF4E9FC);
      case 'pending':
      default:
        return warningSoft;
    }
  }
}
