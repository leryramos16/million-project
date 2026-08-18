import 'dart:io';
import 'dart:math';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:image_picker/image_picker.dart';

import '../../core/data/quest_title_samples.dart';
import '../../core/network/api_client.dart';
import '../../core/providers.dart';
import '../../core/services/location_service.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_theme.dart';
import '../../data/models/payment_method.dart';
import '../../widgets/confirm_dialog.dart';
import '../../widgets/payment_method_card.dart';
import '../../widgets/quest_scaffold.dart';

/// Players only supply what they're asking for; an admin decides the
/// XP/coin reward and quest type when reviewing the request.
class AddQuestScreen extends ConsumerStatefulWidget {
  const AddQuestScreen({super.key});

  @override
  ConsumerState<AddQuestScreen> createState() => _AddQuestScreenState();
}

class _AddQuestScreenState extends ConsumerState<AddQuestScreen> {
  final _titleController = TextEditingController();
  final _descriptionController = TextEditingController();
  final _locationController = TextEditingController();
  final _locationService = LocationService();

  XFile? _proofImage;
  bool _submitting = false;
  bool _locating = false;
  String? _error;
  double? _lat;
  double? _lng;

  List<PaymentMethod> _paymentMethods = [];
  bool _loadingPaymentMethods = true;

  final _random = Random();
  late String _titleHint;

  @override
  void initState() {
    super.initState();
    _titleHint = questTitleSamples[_random.nextInt(questTitleSamples.length)];
    _loadPaymentMethods();
  }

  void _rollTitleSuggestion() {
    String next;
    do {
      next = questTitleSamples[_random.nextInt(questTitleSamples.length)];
    } while (next == _titleController.text && questTitleSamples.length > 1);

    setState(() => _titleController.text = next);
  }

  Future<void> _loadPaymentMethods() async {
    try {
      final methods = await ref.read(paymentMethodRepositoryProvider).list();
      if (mounted) setState(() => _paymentMethods = methods);
    } on ApiException {
      // Non-critical: form still works without payment info shown.
    } finally {
      if (mounted) setState(() => _loadingPaymentMethods = false);
    }
  }

  @override
  void dispose() {
    _titleController.dispose();
    _descriptionController.dispose();
    _locationController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    final image = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 85);
    if (image != null) setState(() => _proofImage = image);
  }

  Future<void> _useCurrentLocation() async {
    setState(() => _locating = true);

    try {
      final position = await _locationService.getCurrentPosition();
      final label = await _locationService.reverseGeocode(position.latitude, position.longitude);

      setState(() {
        _lat = position.latitude;
        _lng = position.longitude;
        if (label != null) _locationController.text = label;
      });
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('$e')));
      }
    } finally {
      if (mounted) setState(() => _locating = false);
    }
  }

  Future<void> _submit() async {
    if (_titleController.text.trim().isEmpty || _descriptionController.text.trim().isEmpty) {
      setState(() => _error = 'Title and description are required.');
      return;
    }

    if (_proofImage == null) {
      setState(() => _error = 'Please upload a payment screenshot.');
      return;
    }

    setState(() {
      _submitting = true;
      _error = null;
    });

    try {
      await ref.read(questRepositoryProvider).create(
            title: _titleController.text.trim(),
            description: _descriptionController.text.trim(),
            paymentProofPath: _proofImage!.path,
            location: _locationController.text.trim(),
            lat: _lat,
            lng: _lng,
          );

      if (mounted) {
        await showQuestSuccessDialog(
          context,
          title: 'Quest Submitted!',
          message: 'Your request is on its way to the Game Master for review.',
        );
        if (mounted) context.pop();
      }
    } on ApiException catch (e) {
      if (mounted) {
        await showQuestFailureDialog(context, title: 'Submission Failed', message: e.message);
      }
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      appBar: AppBar(title: const Text('Ask for help')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (!_loadingPaymentMethods && _paymentMethods.isNotEmpty) ...[
              Text('Send payment to', style: AppTheme.heading(14)),
              const SizedBox(height: 4),
              Text(
                'Pay first, then attach your screenshot below as proof.',
                style: AppTheme.body(12, color: AppColors.textMuted),
              ),
              const SizedBox(height: 10),
              ..._paymentMethods.map(
                (method) => Padding(
                  padding: const EdgeInsets.only(bottom: 10),
                  child: PaymentMethodCard(method: method),
                ),
              ),
              const SizedBox(height: 12),
            ],
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppColors.surface,
                border: Border.all(color: AppColors.border),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(color: AppColors.infoSoft, borderRadius: BorderRadius.circular(10)),
                    child: Text(
                      'An admin verifies your payment and sets the XP, coin reward, and quest type when '
                      'your request is reviewed.',
                      style: AppTheme.body(12, color: AppColors.info),
                    ),
                  ),
                  const SizedBox(height: 16),
                  if (_error != null) ...[
                    Text(_error!, style: AppTheme.body(13, color: AppColors.danger)),
                    const SizedBox(height: 12),
                  ],
                  TextField(
                    controller: _titleController,
                    decoration: InputDecoration(
                      labelText: 'Title',
                      hintText: _titleHint,
                      helperText: 'Give it a quest name, not a chore name — makes people want to click it.',
                      helperMaxLines: 2,
                      suffixIcon: IconButton(
                        tooltip: 'Give me a quest name idea',
                        onPressed: _rollTitleSuggestion,
                        icon: const Text('🎲', style: TextStyle(fontSize: 18)),
                      ),
                    ),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _descriptionController,
                    maxLines: 5,
                    decoration: const InputDecoration(
                      labelText: 'Description',
                      hintText: 'e.g. "The printer has achieved sentience and refuses to print. '
                          'Brave soul needed to negotiate peace (or just restart it). '
                          'ETA 15 mins, minor risk of paper cuts."',
                      helperText: 'Be detailed and have fun with it — specific, entertaining quests '
                          'attract more helpers than vague ones.',
                      helperMaxLines: 2,
                      alignLabelWithHint: true,
                    ),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _locationController,
                    decoration: InputDecoration(
                      labelText: 'Location',
                      hintText: 'e.g. Tabliyahan, Mabini, Batangas',
                      suffixIcon: IconButton(
                        tooltip: 'Use my current location',
                        onPressed: _locating ? null : _useCurrentLocation,
                        icon: _locating
                            ? const Padding(
                                padding: EdgeInsets.all(12),
                                child: SizedBox(
                                  width: 16,
                                  height: 16,
                                  child: CircularProgressIndicator(strokeWidth: 2),
                                ),
                              )
                            : const Icon(Icons.my_location, size: 20),
                      ),
                    ),
                  ),
                  if (_lat != null && _lng != null) ...[
                    const SizedBox(height: 4),
                    Text('GPS location attached', style: AppTheme.body(11, color: AppColors.success)),
                  ],
                  const SizedBox(height: 18),
                  Text('Payment proof', style: AppTheme.heading(14)),
                  const SizedBox(height: 8),
                  if (_proofImage != null)
                    ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: Image.file(File(_proofImage!.path), height: 140, width: double.infinity, fit: BoxFit.cover),
                    ),
                  const SizedBox(height: 8),
                  OutlinedButton.icon(
                    onPressed: _pickImage,
                    icon: const Icon(Icons.upload_outlined),
                    label: Text(_proofImage == null ? 'Upload screenshot' : 'Change screenshot'),
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
                          : const Text('Submit request'),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
