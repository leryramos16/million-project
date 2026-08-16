class AppUser {
  AppUser({
    required this.id,
    required this.username,
    required this.email,
    required this.role,
    required this.level,
    required this.xp,
    required this.coins,
    this.profileImage,
  });

  final int id;
  final String username;
  final String email;
  final String role;
  final int level;
  final int xp;
  final int coins;
  final String? profileImage;

  factory AppUser.fromJson(Map<String, dynamic> json) => AppUser(
        id: json['id'] as int,
        username: json['username'] as String,
        email: json['email'] as String? ?? '',
        role: json['role'] as String? ?? 'user',
        level: (json['level'] as num?)?.toInt() ?? 1,
        xp: (json['xp'] as num?)?.toInt() ?? 0,
        coins: (json['coins'] as num?)?.toInt() ?? 0,
        profileImage: json['profile_image'] as String?,
      );
}

class PlayerStats {
  PlayerStats({
    required this.username,
    required this.level,
    required this.xp,
    required this.coins,
    required this.requiredXp,
    required this.completedQuests,
    this.profileImage,
  });

  final String username;
  final int level;
  final int xp;
  final int coins;
  final int requiredXp;
  final int completedQuests;
  final String? profileImage;

  double get xpProgress => requiredXp <= 0 ? 0 : (xp / requiredXp).clamp(0, 1).toDouble();

  factory PlayerStats.fromJson(Map<String, dynamic> json) => PlayerStats(
        username: json['username'] as String,
        level: (json['level'] as num?)?.toInt() ?? 1,
        xp: (json['xp'] as num?)?.toInt() ?? 0,
        coins: (json['coins'] as num?)?.toInt() ?? 0,
        requiredXp: (json['required_xp'] as num?)?.toInt() ?? 100,
        completedQuests: (json['completed_quests'] as num?)?.toInt() ?? 0,
        profileImage: json['profile_image'] as String?,
      );
}
