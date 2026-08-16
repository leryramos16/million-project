import '../../core/network/api_client.dart';
import '../models/payment_method.dart';

class PaymentMethodRepository {
  PaymentMethodRepository(this._api);

  final ApiClient _api;

  Future<List<PaymentMethod>> list() {
    return _api.guarded(() async {
      final res = await _api.get('/v1/payment-methods');
      return (res['data'] as List).map((e) => PaymentMethod.fromJson(e as Map<String, dynamic>)).toList();
    });
  }
}
