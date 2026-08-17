import '../../core/network/api_client.dart';

class LeaderboardEntry {
  LeaderboardEntry({
    required this.id,
    required this.username,
    required this.level,
    required this.xp,
    required this.coins,
    this.profileImage,
  });

  final int id;
  final String username;
  final int level;
  final int xp;
  final int coins;
  final String? profileImage;

  String? get avatarUrl => profileImage == null ? null : '$kAssetBaseUrl/uploads/avatars/$profileImage';

  factory LeaderboardEntry.fromJson(Map<String, dynamic> json) => LeaderboardEntry(
        id: json['id'] as int,
        username: json['username'] as String,
        level: (json['level'] as num?)?.toInt() ?? 1,
        xp: (json['xp'] as num?)?.toInt() ?? 0,
        coins: (json['coins'] as num?)?.toInt() ?? 0,
        profileImage: json['profile_image'] as String?,
      );
}
