import '../../core/network/api_client.dart';

class Quest {
  Quest({
    required this.id,
    required this.title,
    required this.description,
    required this.xpReward,
    required this.coinsReward,
    required this.type,
    required this.difficulty,
    required this.status,
    this.paymentProof,
    this.createdBy,
    this.creatorUsername,
    this.creatorAvatar,
    this.acceptedBy,
    this.acceptedByUsername,
    this.createdAt,
    this.location,
    this.lat,
    this.lng,
  });

  final int id;
  final String title;
  final String description;
  final int xpReward;
  final int coinsReward;
  final String type;
  final String difficulty;
  final String status;
  final String? paymentProof;
  final int? createdBy;
  final String? creatorUsername;
  final String? creatorAvatar;
  final int? acceptedBy;
  final String? acceptedByUsername;
  final String? createdAt;
  final String? location;
  final double? lat;
  final double? lng;

  bool get hasCoordinates => lat != null && lng != null;

  String? get creatorAvatarUrl => creatorAvatar == null ? null : '$kAssetBaseUrl/uploads/avatars/$creatorAvatar';

  factory Quest.fromJson(Map<String, dynamic> json) => Quest(
        id: json['id'] as int,
        title: json['title'] as String? ?? '',
        description: json['description'] as String? ?? '',
        xpReward: (json['xp_reward'] as num?)?.toInt() ?? 0,
        coinsReward: (json['coins_reward'] as num?)?.toInt() ?? 0,
        type: json['type'] as String? ?? 'side_quests',
        difficulty: json['difficulty'] as String? ?? 'easy',
        status: json['status'] as String? ?? 'pending',
        paymentProof: json['payment_proof'] as String?,
        createdBy: json['created_by'] as int?,
        creatorUsername: json['username'] as String?,
        creatorAvatar: json['creator_avatar'] as String?,
        acceptedBy: json['accepted_by'] as int?,
        acceptedByUsername: json['accepted_by_name'] as String?,
        createdAt: json['created_at'] as String?,
        location: json['creator_location'] as String?,
        lat: _parseDouble(json['creator_lat']),
        lng: _parseDouble(json['creator_lng']),
      );

  String get typeLabel => type.replaceAll('_', ' ');
}

/// MySQL DECIMAL columns come back through PHP's PDO as strings, not numbers.
double? _parseDouble(dynamic value) {
  if (value == null) return null;
  if (value is num) return value.toDouble();
  return double.tryParse(value.toString());
}

class PaginatedQuests {
  PaginatedQuests({required this.quests, required this.page, required this.totalPages, required this.total});

  final List<Quest> quests;
  final int page;
  final int totalPages;
  final int total;

  factory PaginatedQuests.fromJson(Map<String, dynamic> json) => PaginatedQuests(
        quests: (json['data'] as List).map((e) => Quest.fromJson(e as Map<String, dynamic>)).toList(),
        page: (json['page'] as num?)?.toInt() ?? 1,
        totalPages: (json['total_pages'] as num?)?.toInt() ?? 1,
        total: (json['total'] as num?)?.toInt() ?? 0,
      );
}
