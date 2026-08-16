import '../../core/network/api_client.dart';

class PaymentMethod {
  PaymentMethod({
    required this.id,
    required this.method,
    required this.label,
    required this.accountName,
    required this.accountNumber,
    this.qrCodeImage,
    this.instructions,
  });

  final int id;
  final String method;
  final String label;
  final String accountName;
  final String accountNumber;
  final String? qrCodeImage;
  final String? instructions;

  String? get qrCodeUrl => qrCodeImage == null ? null : '$kAssetBaseUrl/uploads/qrcodes/$qrCodeImage';

  factory PaymentMethod.fromJson(Map<String, dynamic> json) => PaymentMethod(
        id: json['id'] as int,
        method: json['method'] as String? ?? 'other',
        label: json['label'] as String? ?? 'Payment',
        accountName: json['account_name'] as String? ?? '',
        accountNumber: json['account_number'] as String? ?? '',
        qrCodeImage: json['qr_code_image'] as String?,
        instructions: json['instructions'] as String?,
      );
}
