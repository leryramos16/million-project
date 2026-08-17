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
import '../../widgets/quest_card.dart';
import '../../widgets/quest_scaffold.dart';

class QuestBoardScreen extends ConsumerStatefulWidget {
  const QuestBoardScreen({super.key});

  @override
  ConsumerState<QuestBoardScreen> createState() => _QuestBoardScreenState();
}

class _QuestBoardScreenState extends ConsumerState<QuestBoardScreen> {
  PlayerStats? _stats;
  List<Quest> _quests = [];
  String? _typeFilter;
  bool _loading = true;
  String? _error;

  static const _types = {
    null: 'All',
    'main_quests': 'Main',
    'side_quests': 'Side',
    'events': 'Events',
  };

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final stats = await ref.read(userRepositoryProvider).stats();
      final quests = await ref.read(questRepositoryProvider).listApproved(type: _typeFilter, limit: 30);
      setState(() {
        _stats = stats;
        _quests = quests.quests;
      });
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(
        title: const Text('Quest board'),
        actions: [
          IconButton(
            tooltip: 'Leaderboard',
            onPressed: () => context.push('/leaderboard'),
            icon: const Icon(Icons.leaderboard_outlined),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          await context.push('/quests/new');
          _load();
        },
        child: const Icon(Icons.add),
      ),
      body: RefreshIndicator(
        onRefresh: _load,
        child: _loading
            ? const Center(child: CircularProgressIndicator())
            : ListView(
                padding: const EdgeInsets.only(bottom: 90),
                children: [
                  if (_stats != null) PlayerHeader(stats: _stats!),
                  if (_error != null)
                    Padding(
                      padding: const EdgeInsets.all(16),
                      child: Text(_error!, style: AppTheme.body(13, color: AppColors.danger)),
                    ),
                  SizedBox(
                    height: 44,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      children: _types.entries.map((entry) {
                        final selected = _typeFilter == entry.key;
                        return Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 4),
                          child: ChoiceChip(
                            label: Text(entry.value),
                            selected: selected,
                            onSelected: (_) {
                              setState(() => _typeFilter = entry.key);
                              _load();
                            },
                            labelStyle: AppTheme.body(
                              12,
                              color: selected ? AppColors.primary : AppColors.textSecondary,
                              weight: FontWeight.w600,
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                  ),
                  if (_quests.isEmpty)
                    Padding(
                      padding: const EdgeInsets.all(40),
                      child: Text(
                        'No quests posted yet.',
                        textAlign: TextAlign.center,
                        style: AppTheme.body(15, color: AppColors.textMuted),
                      ),
                    )
                  else
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 14),
                      child: Wrap(
                        spacing: 14,
                        runSpacing: 14,
                        children: _quests
                            .map(
                              (q) => SizedBox(
                                width: MediaQuery.of(context).size.width > 500
                                    ? (MediaQuery.of(context).size.width - 60) / 2
                                    : double.infinity,
                                child: QuestCard(
                                  quest: q,
                                  onTap: () async {
                                    await context.push('/quests/${q.id}');
                                    _load();
                                  },
                                ),
                              ),
                            )
                            .toList(),
                      ),
                    ),
                ],
              ),
      ),
    );
  }
}
