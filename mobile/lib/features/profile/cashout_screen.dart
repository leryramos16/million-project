import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../data/models/cashout_request.dart';
import '../../data/models/user.dart';
import '../../widgets/quest_scaffold.dart';

class CashoutScreen extends ConsumerStatefulWidget {
  const CashoutScreen({super.key});

  @override
  ConsumerState<CashoutScreen> createState() => _CashoutScreenState();
}

class _CashoutScreenState extends ConsumerState<CashoutScreen> {
  static const _minCoins = 50;
  static const _methods = {'gcash': 'GCash', 'maya': 'Maya', 'bank_transfer': 'Bank Transfer', 'other': 'Other'};

  final _coinsController = TextEditingController();
  final _accountNameController = TextEditingController();
  final _accountNumberController = TextEditingController();
  String _method = 'gcash';

  PlayerStats? _stats;
  List<CashoutRequest> _history = [];
  bool _loading = true;
  bool _submitting = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _coinsController.dispose();
    _accountNameController.dispose();
    _accountNumberController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final stats = await ref.read(userRepositoryProvider).stats();
      final history = await ref.read(cashoutRepositoryProvider).history();
      setState(() {
        _stats = stats;
        _history = history;
      });
    } on ApiException {
      // ignore, form still usable
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _submit() async {
    final coins = int.tryParse(_coinsController.text.trim()) ?? 0;

    if (coins < _minCoins) {
      setState(() => _error = 'Minimum cashout is $_minCoins coins.');
      return;
    }
    if (_stats != null && coins > _stats!.coins) {
      setState(() => _error = "You don't have that many coins.");
      return;
    }
    if (_accountNameController.text.trim().isEmpty || _accountNumberController.text.trim().isEmpty) {
      setState(() => _error = 'Account name and number are required.');
      return;
    }

    setState(() {
      _submitting = true;
      _error = null;
    });

    try {
      await ref.read(cashoutRepositoryProvider).request(
            coins: coins,
            paymentMethod: _method,
            accountName: _accountNameController.text.trim(),
            accountNumber: _accountNumberController.text.trim(),
          );

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Cashout requested! Paid out on the next payout run.')),
        );
        _coinsController.clear();
        await _load();
      }
    } on ApiException catch (e) {
      setState(() => _error = e.message);
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  Color _statusColor(String status) {
    switch (status) {
      case 'paid':
        return AppColors.success;
      case 'rejected':
        return AppColors.danger;
      default:
        return AppColors.warning;
    }
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(title: const Text('Cash Out')),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _load,
              child: ListView(
                padding: const EdgeInsets.all(20),
                children: [
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: AppColors.surface,
                      border: Border.all(color: AppColors.border),
                      borderRadius: BorderRadius.circular(14),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.monetization_on, color: AppColors.gold),
                        const SizedBox(width: 8),
                        Text('${_stats?.coins ?? 0} coins available', style: AppTheme.heading(15)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    '1 coin = ₱1. Minimum cashout is $_minCoins coins. Paid out weekly.',
                    style: AppTheme.body(12, color: AppColors.textMuted),
                  ),
                  const SizedBox(height: 20),
                  if (_error != null) ...[
                    Text(_error!, style: AppTheme.body(13, color: AppColors.danger)),
                    const SizedBox(height: 12),
                  ],
                  TextField(
                    controller: _coinsController,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(labelText: 'Coins to cash out'),
                  ),
                  const SizedBox(height: 14),
                  DropdownButtonFormField<String>(
                    initialValue: _method,
                    decoration: const InputDecoration(labelText: 'Payment method'),
                    items: _methods.entries.map((e) => DropdownMenuItem(value: e.key, child: Text(e.value))).toList(),
                    onChanged: (v) => setState(() => _method = v ?? _method),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _accountNameController,
                    decoration: const InputDecoration(labelText: 'Account name'),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _accountNumberController,
                    decoration: const InputDecoration(labelText: 'Account / mobile number'),
                  ),
                  const SizedBox(height: 20),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: _submitting ? null : _submit,
                      child: _submitting
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(strokeWidth: 2, color: AppColors.onPrimary),
                            )
                          : const Text('Request Cashout'),
                    ),
                  ),
                  const SizedBox(height: 28),
                  Text('History', style: AppTheme.heading(15)),
                  const SizedBox(height: 10),
                  if (_history.isEmpty)
                    Text('No cashout requests yet.', style: AppTheme.body(13, color: AppColors.textMuted))
                  else
                    ..._history.map(
                      (r) => Container(
                        margin: const EdgeInsets.only(bottom: 8),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          border: Border.all(color: AppColors.border),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('₱${r.pesoAmount} · ${r.coinsRequested} coins', style: AppTheme.body(13, weight: FontWeight.w600)),
                                  Text(r.requestedAt, style: AppTheme.body(11, color: AppColors.textMuted)),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: _statusColor(r.status).withValues(alpha: 0.15),
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: Text(
                                r.status.toUpperCase(),
                                style: AppTheme.body(10, color: _statusColor(r.status), weight: FontWeight.w700),
                              ),
                            ),
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
