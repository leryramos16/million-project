import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:gal/gal.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';
import '../data/models/payment_method.dart';

class PaymentMethodCard extends StatefulWidget {
  const PaymentMethodCard({super.key, required this.method});

  final PaymentMethod method;

  @override
  State<PaymentMethodCard> createState() => _PaymentMethodCardState();
}

class _PaymentMethodCardState extends State<PaymentMethodCard> {
  bool _saving = false;

  Future<void> _copyNumber() async {
    await Clipboard.setData(ClipboardData(text: widget.method.accountNumber));
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Account number copied'), duration: Duration(seconds: 1)),
      );
    }
  }

  Future<void> _saveQr() async {
    final url = widget.method.qrCodeUrl;
    if (url == null || _saving) return;

    setState(() => _saving = true);

    try {
      final hasAccess = await Gal.hasAccess() || await Gal.requestAccess();
      if (!hasAccess) {
        throw Exception('Photos permission was denied.');
      }

      final response = await Dio().get<List<int>>(url, options: Options(responseType: ResponseType.bytes));
      await Gal.putImageBytes(Uint8List.fromList(response.data!), name: '${widget.method.label}_qr');

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('QR code saved to gallery')));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Could not save QR code: $e')));
      }
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final method = widget.method;

    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.surfaceElevated,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (method.qrCodeUrl != null) ...[
            Column(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: Image.network(
                    method.qrCodeUrl!,
                    width: 64,
                    height: 64,
                    fit: BoxFit.cover,
                    errorBuilder: (_, _, _) => const SizedBox(width: 64, height: 64),
                  ),
                ),
                const SizedBox(height: 4),
                InkWell(
                  onTap: _saveQr,
                  child: _saving
                      ? const SizedBox(
                          width: 14,
                          height: 14,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        )
                      : const Icon(Icons.download_rounded, size: 18, color: AppColors.textMuted),
                ),
              ],
            ),
            const SizedBox(width: 12),
          ],
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(method.label, style: AppTheme.heading(14)),
                const SizedBox(height: 2),
                Text(method.accountName, style: AppTheme.body(12, color: AppColors.textSecondary)),
                const SizedBox(height: 4),
                InkWell(
                  onTap: _copyNumber,
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        method.accountNumber,
                        style: AppTheme.heading(15, color: AppColors.textPrimary),
                      ),
                      const SizedBox(width: 6),
                      const Icon(Icons.copy_rounded, size: 14, color: AppColors.textMuted),
                    ],
                  ),
                ),
                if (method.instructions != null && method.instructions!.isNotEmpty) ...[
                  const SizedBox(height: 6),
                  Text(method.instructions!, style: AppTheme.body(11, color: AppColors.textMuted)),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}
