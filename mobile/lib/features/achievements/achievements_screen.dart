import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../data/models/achievement.dart';
import '../../widgets/quest_scaffold.dart';

class AchievementsScreen extends ConsumerStatefulWidget {
  const AchievementsScreen({super.key});

  @override
  ConsumerState<AchievementsScreen> createState() => _AchievementsScreenState();
}

class _AchievementsScreenState extends ConsumerState<AchievementsScreen> {
  List<Achievement> _achievements = [];
  bool _loading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final achievements = await ref.read(userRepositoryProvider).achievements();
      setState(() => _achievements = achievements);
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(title: const Text('Achievements')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? Center(child: Text(_error!, style: AppTheme.body(14, color: AppColors.danger)))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: GridView.builder(
                    padding: const EdgeInsets.all(16),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      crossAxisSpacing: 14,
                      mainAxisSpacing: 14,
                      childAspectRatio: 0.85,
                    ),
                    itemCount: _achievements.length,
                    itemBuilder: (context, index) {
                      final achievement = _achievements[index];
                      final unlocked = achievement.unlocked;

                      return Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: unlocked ? AppColors.primarySoft : AppColors.surface,
                          border: Border.all(color: unlocked ? AppColors.primary : AppColors.border),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              unlocked ? Icons.military_tech : Icons.lock_outline,
                              size: 36,
                              color: unlocked ? AppColors.primary : AppColors.textMuted,
                            ),
                            const SizedBox(height: 10),
                            Text(
                              achievement.title,
                              textAlign: TextAlign.center,
                              style: AppTheme.heading(13, color: unlocked ? AppColors.primary : AppColors.textSecondary),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              achievement.description,
                              textAlign: TextAlign.center,
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: AppTheme.body(11, color: AppColors.textMuted),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }
}
