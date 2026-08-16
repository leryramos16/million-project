import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../data/models/quest.dart';
import '../../data/models/user.dart';
import '../../widgets/player_header.dart';
import '../../widgets/quest_badges.dart';
import '../../widgets/quest_scaffold.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  PlayerStats? _stats;
  List<Quest> _accepted = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final stats = await ref.read(userRepositoryProvider).stats();
      final accepted = await ref.read(questRepositoryProvider).accepted();
      setState(() {
        _stats = stats;
        _accepted = accepted;
      });
    } on ApiException {
      // swallow: header/list simply stays empty
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _logout() async {
    await ref.read(authControllerProvider.notifier).logout();
    if (mounted) context.go('/login');
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(
        title: const Text('Profile'),
        actions: [IconButton(onPressed: _logout, icon: const Icon(Icons.logout))],
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.only(bottom: 40),
                children: [
                  if (_stats != null) PlayerHeader(stats: _stats!),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(20, 20, 20, 8),
                    child: Text('Quests I\'ve accepted', style: AppTheme.heading(16)),
                  ),
                  if (_accepted.isEmpty)
                    Padding(
                      padding: const EdgeInsets.all(20),
                      child: Text('No active quests yet.', style: AppTheme.body(13, color: AppColors.textMuted)),
                    )
                  else
                    ..._accepted.map(
                      (quest) => Container(
                        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          border: Border.all(color: AppColors.border),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(quest.title, style: AppTheme.heading(14)),
                                  const SizedBox(height: 4),
                                  Text(
                                    '${quest.xpReward} XP · ${quest.coinsReward} coins',
                                    style: AppTheme.body(12, color: AppColors.textSecondary),
                                  ),
                                ],
                              ),
                            ),
                            StatusBadge(status: quest.status),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
            ),
    );
  }
}
