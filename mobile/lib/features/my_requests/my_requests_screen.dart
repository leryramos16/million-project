import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../data/models/quest.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/quest_badges.dart';
import '../../widgets/quest_loader.dart';
import '../../widgets/quest_scaffold.dart';

class MyRequestsScreen extends ConsumerStatefulWidget {
  const MyRequestsScreen({super.key});

  @override
  ConsumerState<MyRequestsScreen> createState() => _MyRequestsScreenState();
}

class _MyRequestsScreenState extends ConsumerState<MyRequestsScreen> with SingleTickerProviderStateMixin {
  late final TabController _tabController;
  static const _statuses = ['pending', 'accepted', 'completed'];

  final Map<String, int> _page = {for (final s in _statuses) s: 1};
  final Map<String, PaginatedQuests?> _data = {for (final s in _statuses) s: null};
  bool _loading = true;
  String? _error;

  String get _status => _statuses[_tabController.index];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _statuses.length, vsync: this);
    _tabController.addListener(() {
      if (!_tabController.indexIsChanging) {
        ref.read(musicControllerProvider.notifier).playPageTurn();
        _load();
      }
    });
    _load();
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final result = await ref.read(questRepositoryProvider).myRequests(status: _status, page: _page[_status]!, limit: 5);
      setState(() => _data[_status] = result);
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _markDone(Quest quest) async {
    final confirmed = await showQuestConfirmDialog(
      context,
      title: 'Mark as done?',
      message: 'The reward will be granted to whoever completed this quest.',
      confirmLabel: 'Yes, complete it',
    );
    if (!confirmed) return;

    try {
      final achievements = await ref.read(questRepositoryProvider).complete(quest.id);
      if (!mounted) return;

      final helperName = quest.acceptedByUsername ?? 'The helper';
      await showQuestSuccessDialog(
        context,
        title: 'Quest Completed!',
        message: '$helperName earned ${quest.xpReward} XP and ${quest.coinsReward} coins.',
      );

      for (final achievement in achievements) {
        if (!mounted) return;
        await showQuestSuccessDialog(
          context,
          title: '🎉 $helperName unlocked an achievement!',
          message: achievement.title,
          buttonLabel: 'Nice!',
        );
      }

      _load();
    } on ApiException catch (e) {
      if (mounted) await showQuestFailureDialog(context, title: 'Could Not Complete', message: e.message);
    }
  }

  @override
  Widget build(BuildContext context) {
    final current = _data[_status];

    return QuestScaffold(
      appBar: AppBar(
        title: const Text('My requests'),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: AppColors.primary,
          labelColor: AppColors.primary,
          unselectedLabelColor: AppColors.textMuted,
          tabs: const [Tab(text: 'Pending'), Tab(text: 'Accepted'), Tab(text: 'Completed')],
        ),
      ),
      body: _loading
          ? const Center(child: QuestLoader())
          : _error != null
              ? Center(child: Text(_error!, style: AppTheme.body(14, color: AppColors.danger)))
              : current == null || current.quests.isEmpty
                  ? Center(
                      child: Text('No $_status quests here.', style: AppTheme.body(15, color: AppColors.textMuted)),
                    )
                  : Column(
                      children: [
                        Expanded(
                          child: ListView.separated(
                            padding: const EdgeInsets.all(16),
                            itemCount: current.quests.length,
                            separatorBuilder: (_, _) => const SizedBox(height: 14),
                            itemBuilder: (context, index) {
                              final quest = current.quests[index];
                              return Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: AppColors.surface,
                                  border: Border.all(color: AppColors.border),
                                  borderRadius: BorderRadius.circular(14),
                                ),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Expanded(
                                          child: Text(quest.title, style: AppTheme.heading(15)),
                                        ),
                                        StatusBadge(status: quest.status),
                                      ],
                                    ),
                                    const SizedBox(height: 6),
                                    Text(quest.description, style: AppTheme.body(13, color: AppColors.textSecondary)),
                                    if (quest.acceptedByUsername != null) ...[
                                      const SizedBox(height: 6),
                                      Text(
                                        'Accepted by ${quest.acceptedByUsername}',
                                        style: AppTheme.body(12, color: AppColors.textMuted),
                                      ),
                                    ],
                                    if (quest.status == 'accepted') ...[
                                      const SizedBox(height: 10),
                                      ElevatedButton(onPressed: () => _markDone(quest), child: const Text('Mark as done')),
                                    ],
                                  ],
                                ),
                              );
                            },
                          ),
                        ),
                        if (current.totalPages > 1)
                          Padding(
                            padding: const EdgeInsets.only(bottom: 16),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                IconButton(
                                  onPressed: _page[_status]! > 1
                                      ? () {
                                          setState(() => _page[_status] = _page[_status]! - 1);
                                          _load();
                                        }
                                      : null,
                                  icon: const Icon(Icons.chevron_left),
                                ),
                                Text('${current.page} / ${current.totalPages}', style: AppTheme.body(13, weight: FontWeight.w600)),
                                IconButton(
                                  onPressed: _page[_status]! < current.totalPages
                                      ? () {
                                          setState(() => _page[_status] = _page[_status]! + 1);
                                          _load();
                                        }
                                      : null,
                                  icon: const Icon(Icons.chevron_right),
                                ),
                              ],
                            ),
                          ),
                      ],
                    ),
    );
  }
}
