import 'package:flutter/material.dart';

/// Dark, monochrome-first palette — black background, white text/CTAs.
/// Small functional accent colors are kept only where they carry meaning
/// (quest type, difficulty, status, success/danger), so those stay readable
/// at a glance even in an otherwise black-and-white UI.
class AppColors {
  AppColors._();

  static const background = Color(0xFF0A0A0B);
  static const surface = Color(0xFF161618);
  static const surfaceElevated = Color(0xFF1E1E21);
  static const border = Color(0xFF2A2A2E);

  static const textPrimary = Color(0xFFFAFAFA);
  static const textSecondary = Color(0xFFA1A1AA);
  static const textMuted = Color(0xFF71717A);

  static const primary = Colors.white;
  static const onPrimary = Colors.black;
  static const primarySoft = Color(0xFF2A2A2E);

  static const success = Color(0xFF4ADE80);
  static const successSoft = Color(0xFF14351F);
  static const warning = Color(0xFFFACC15);
  static const warningSoft = Color(0xFF3A3013);
  static const danger = Color(0xFFF87171);
  static const dangerSoft = Color(0xFF3A1A1A);
  static const info = Color(0xFF60A5FA);
  static const infoSoft = Color(0xFF17263D);

  static const gold = Color(0xFFFACC15); // coins accent
  static const xpFill = Colors.white;

  static Color typeColor(String type) {
    switch (type) {
      case 'side_quests':
        return const Color(0xFF2DD4BF);
      case 'events':
        return const Color(0xFFC084FC);
      case 'main_quests':
      default:
        return const Color(0xFF93C5FD);
    }
  }

  static Color typeSoft(String type) {
    switch (type) {
      case 'side_quests':
        return const Color(0xFF123430);
      case 'events':
        return const Color(0xFF2E1F3D);
      case 'main_quests':
      default:
        return const Color(0xFF17263D);
    }
  }

  static Color difficultyColor(String difficulty) {
    switch (difficulty) {
      case 'medium':
        return warning;
      case 'hard':
        return const Color(0xFFFB923C);
      case 'legendary':
        return const Color(0xFFC084FC);
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
        return const Color(0xFF3A2313);
      case 'legendary':
        return const Color(0xFF2E1F3D);
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
        return const Color(0xFFC084FC);
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
        return const Color(0xFF2E1F3D);
      case 'pending':
      default:
        return warningSoft;
    }
  }
}
