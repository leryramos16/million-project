import 'package:dio/dio.dart';

import '../../core/network/api_client.dart';
import '../models/achievement.dart';
import '../models/quest.dart';

class QuestRepository {
  QuestRepository(this._api);

  final ApiClient _api;

  Future<PaginatedQuests> listApproved({String? type, int page = 1, int limit = 10}) {
    return _api.guarded(() async {
      final res = await _api.get('/v1/quests', query: {
        if (type != null) 'type': type,
        'page': page,
        'limit': limit,
      });
      return PaginatedQuests.fromJson(res['data'] as Map<String, dynamic>);
    });
  }

  Future<Quest> detail(int id) {
    return _api.guarded(() async {
      final res = await _api.get('/v1/quests/$id');
      return Quest.fromJson(res['data'] as Map<String, dynamic>);
    });
  }

  Future<void> create({
    required String title,
    required String description,
    required String paymentProofPath,
  }) {
    return _api.guarded(() async {
      final formData = FormData.fromMap({
        'title': title,
        'description': description,
        'payment_proof': await MultipartFile.fromFile(paymentProofPath),
      });

      await _api.postMultipart('/v1/quests', formData);
    });
  }

  Future<void> accept(int id) {
    return _api.guarded(() => _api.post('/v1/quests/$id/accept'));
  }

  Future<List<Achievement>> complete(int id) {
    return _api.guarded(() async {
      final res = await _api.post('/v1/quests/$id/complete');
      final achievements = (res['data'] as Map<String, dynamic>)['new_achievements'] as List;
      return achievements.map((e) => Achievement.fromJson(e as Map<String, dynamic>)).toList();
    });
  }

  Future<PaginatedQuests> myRequests({required String status, int page = 1, int limit = 5}) {
    return _api.guarded(() async {
      final res = await _api.get('/v1/quests/mine', query: {'status': status, 'page': page, 'limit': limit});
      return PaginatedQuests.fromJson(res['data'] as Map<String, dynamic>);
    });
  }

  Future<List<Quest>> accepted() {
    return _api.guarded(() async {
      final res = await _api.get('/v1/quests/accepted');
      return (res['data'] as List).map((e) => Quest.fromJson(e as Map<String, dynamic>)).toList();
    });
  }
}
