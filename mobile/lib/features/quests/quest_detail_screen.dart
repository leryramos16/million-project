import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../core/utils/map_launcher.dart';
import '../../data/models/quest.dart';
import '../../widgets/coin_chip.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/quest_badges.dart';
import '../../widgets/quest_loader.dart';
import '../../widgets/quest_scaffold.dart';

class QuestDetailScreen extends ConsumerStatefulWidget {
  const QuestDetailScreen({super.key, required this.questId});

  final int questId;

  @override
  ConsumerState<QuestDetailScreen> createState() => _QuestDetailScreenState();
}

class _QuestDetailScreenState extends ConsumerState<QuestDetailScreen> {
  Quest? _quest;
  bool _loading = true;
  bool _accepting = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final quest = await ref.read(questRepositoryProvider).detail(widget.questId);
      setState(() => _quest = quest);
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _accept() async {
    final confirmed = await showQuestConfirmDialog(
      context,
      title: 'Accept this quest?',
      message: 'You will take on this quest and be responsible for completing it.',
      confirmLabel: 'Yes, accept it',
    );
    if (!confirmed) return;

    setState(() => _accepting = true);
    try {
      await ref.read(questRepositoryProvider).accept(widget.questId);
      if (mounted) {
        await _load();
        if (mounted) {
          await showQuestSuccessDialog(
            context,
            title: 'Contract Sealed!',
            message: 'You\'ve accepted this quest. Complete it, then wait for the requester to confirm.',
          );
        }
      }
    } on ApiException catch (e) {
      if (mounted) {
        await showQuestFailureDialog(
          context,
          title: 'Could Not Accept',
          message: e.message,
        );
      }
    } finally {
      if (mounted) setState(() => _accepting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(title: const Text('Quest details')),
      body: _loading
          ? const Center(child: QuestLoader())
          : _error != null
              ? Center(child: Text(_error!, style: AppTheme.body(14, color: AppColors.danger)))
              : _quest == null
                  ? const SizedBox.shrink()
                  : SingleChildScrollView(
                      padding: const EdgeInsets.all(20),
                      child: Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: AppColors.border),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(_quest!.title, style: AppTheme.heading(22)),
                            const SizedBox(height: 8),
                            Wrap(
                              spacing: 8,
                              children: [
                                QuestTypeBadge(type: _quest!.type),
                                DifficultyBadge(difficulty: _quest!.difficulty),
                                StatusBadge(status: _quest!.status),
                              ],
                            ),
                            const Divider(height: 28),
                            Text(_quest!.description, style: AppTheme.body(15, color: AppColors.textSecondary)),
                            const SizedBox(height: 20),
                            Row(
                              children: [
                                const Icon(Icons.bolt, color: AppColors.primary),
                                const SizedBox(width: 4),
                                Text('${_quest!.xpReward} XP', style: AppTheme.body(14, color: AppColors.textSecondary)),
                                const SizedBox(width: 18),
                                CoinChip(coins: _quest!.coinsReward),
                              ],
                            ),
                            if (_quest!.creatorUsername != null) ...[
                              const SizedBox(height: 14),
                              Text('Posted by ${_quest!.creatorUsername}', style: AppTheme.body(12, color: AppColors.textMuted)),
                            ],
                            if (_quest!.location != null && _quest!.location!.isNotEmpty) ...[
                              const SizedBox(height: 14),
                              InkWell(
                                onTap: _quest!.hasCoordinates
                                    ? () => openInMaps(_quest!.lat!, _quest!.lng!, label: _quest!.location)
                                    : null,
                                child: Row(
                                  children: [
                                    const Icon(Icons.location_on_outlined, size: 16, color: AppColors.textSecondary),
                                    const SizedBox(width: 4),
                                    Expanded(
                                      child: Text(
                                        _quest!.location!,
                                        style: AppTheme.body(
                                          13,
                                          color: _quest!.hasCoordinates ? AppColors.info : AppColors.textSecondary,
                                          weight: _quest!.hasCoordinates ? FontWeight.w600 : null,
                                        ),
                                      ),
                                    ),
                                    if (_quest!.hasCoordinates)
                                      const Icon(Icons.open_in_new, size: 14, color: AppColors.info),
                                  ],
                                ),
                              ),
                            ],
                            const SizedBox(height: 24),
                            if (_quest!.status == 'approved')
                              SizedBox(
                                width: double.infinity,
                                child: ElevatedButton(
                                  onPressed: _accepting ? null : _accept,
                                  child: _accepting
                                      ? const SizedBox(
                                          width: 20,
                                          height: 20,
                                          child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.onPrimary),
                                        )
                                      : const Text('Accept quest'),
                                ),
                              ),
                          ],
                        ),
                      ),
                    ),
    );
  }
}
