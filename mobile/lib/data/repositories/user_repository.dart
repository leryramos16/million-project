import 'package:dio/dio.dart';

import '../../core/network/api_client.dart';
import '../models/achievement.dart';
import '../models/leaderboard_entry.dart';
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

  Future<void> uploadAvatar(String path) {
    return _api.guarded(() async {
      final formData = FormData.fromMap({'avatar': await MultipartFile.fromFile(path)});
      await _api.postMultipart('/v1/me/avatar', formData);
    });
  }

  Future<List<LeaderboardEntry>> leaderboard({int limit = 20}) {
    return _api.guarded(() async {
      final res = await _api.get('/v1/leaderboard', query: {'limit': limit});
      return (res['data'] as List).map((e) => LeaderboardEntry.fromJson(e as Map<String, dynamic>)).toList();
    });
  }
}
