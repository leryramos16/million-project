import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../core/utils/player_title.dart';
import '../../data/models/leaderboard_entry.dart';
import '../../widgets/quest_loader.dart';
import '../../widgets/quest_scaffold.dart';
import '../../widgets/user_avatar.dart';

class LeaderboardScreen extends ConsumerStatefulWidget {
  const LeaderboardScreen({super.key});

  @override
  ConsumerState<LeaderboardScreen> createState() => _LeaderboardScreenState();
}

class _LeaderboardScreenState extends ConsumerState<LeaderboardScreen> {
  List<LeaderboardEntry> _entries = [];
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
      final entries = await ref.read(userRepositoryProvider).leaderboard();
      setState(() => _entries = entries);
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  static const _medalColors = [Color(0xFFFACC15), Color(0xFFD1D5DB), Color(0xFFB45309)];

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(title: const Text('Leaderboard')),
      body: _loading
          ? const Center(child: QuestLoader())
          : _error != null
              ? Center(child: Text(_error!, style: AppTheme.body(14, color: AppColors.danger)))
              : RefreshIndicator(
                  onRefresh: _load,
                  child: ListView.separated(
                    padding: const EdgeInsets.all(16),
                    itemCount: _entries.length,
                    separatorBuilder: (_, _) => const SizedBox(height: 10),
                    itemBuilder: (context, index) {
                      final entry = _entries[index];
                      final isTopThree = index < 3;

                      return Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: isTopThree ? AppColors.surfaceElevated : AppColors.surface,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: isTopThree ? _medalColors[index] : AppColors.border),
                        ),
                        child: Row(
                          children: [
                            SizedBox(
                              width: 28,
                              child: Text(
                                '#${index + 1}',
                                textAlign: TextAlign.center,
                                style: AppTheme.heading(15, color: isTopThree ? _medalColors[index] : AppColors.textMuted),
                              ),
                            ),
                            const SizedBox(width: 10),
                            UserAvatar(url: entry.avatarUrl, radius: 20, username: entry.username),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(entry.username, style: AppTheme.heading(14)),
                                  const SizedBox(height: 2),
                                  Text(
                                    '${playerTitleForLevel(entry.level)} · Level ${entry.level}',
                                    style: AppTheme.body(12, color: AppColors.textSecondary),
                                  ),
                                ],
                              ),
                            ),
                            Row(
                              children: [
                                const Icon(Icons.monetization_on, size: 14, color: AppColors.gold),
                                const SizedBox(width: 3),
                                Text('${entry.coins}', style: AppTheme.body(13, weight: FontWeight.w600)),
                              ],
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
