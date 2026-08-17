import '../../core/network/api_client.dart';
import '../models/cashout_request.dart';

class CashoutRepository {
  CashoutRepository(this._api);

  final ApiClient _api;

  Future<void> request({
    required int coins,
    required String paymentMethod,
    required String accountName,
    required String accountNumber,
  }) {
    return _api.guarded(() => _api.post('/v1/me/cashout', data: {
          'coins': coins,
          'payment_method': paymentMethod,
          'account_name': accountName,
          'account_number': accountNumber,
        }));
  }

  Future<List<CashoutRequest>> history() {
    return _api.guarded(() async {
      final res = await _api.get('/v1/me/cashout');
      return (res['data'] as List).map((e) => CashoutRequest.fromJson(e as Map<String, dynamic>)).toList();
    });
  }
}
