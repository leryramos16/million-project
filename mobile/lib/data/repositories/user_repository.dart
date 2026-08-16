import '../../core/network/api_client.dart';
import '../models/achievement.dart';
import '../models/user.dart';

class UserRepository {
  UserRepository(this._api);

  final ApiClient _api;

  Future<PlayerStats> stats() {
    return _api.guarded(() async {
      final res = await _api.get('/v1/me/stats');
      return PlayerStats.fromJson(res['data'] as Map<String, dynamic>);
    });
  }

  Future<List<Achievement>> achievements() {
    return _api.guarded(() async {
      final res = await _api.get('/v1/me/achievements');
      return (res['data'] as List).map((e) => Achievement.fromJson(e as Map<String, dynamic>)).toList();
    });
  }
}
