class CashoutRequest {
  CashoutRequest({
    required this.id,
    required this.coinsRequested,
    required this.pesoAmount,
    required this.paymentMethod,
    required this.accountNumber,
    required this.status,
    required this.requestedAt,
  });

  final int id;
  final int coinsRequested;
  final int pesoAmount;
  final String paymentMethod;
  final String accountNumber;
  final String status;
  final String requestedAt;

  factory CashoutRequest.fromJson(Map<String, dynamic> json) => CashoutRequest(
        id: json['id'] as int,
        coinsRequested: (json['coins_requested'] as num?)?.toInt() ?? 0,
        pesoAmount: (json['peso_amount'] as num?)?.toInt() ?? 0,
        paymentMethod: json['payment_method'] as String? ?? '',
        accountNumber: json['account_number'] as String? ?? '',
        status: json['status'] as String? ?? 'pending',
        requestedAt: json['requested_at'] as String? ?? '',
      );
}
