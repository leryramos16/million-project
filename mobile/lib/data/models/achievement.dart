class Achievement {
  Achievement({
    required this.id,
    required this.code,
    required this.title,
    required this.description,
    required this.xpBonus,
    required this.coinsBonus,
    required this.unlocked,
    this.unlockedAt,
  });

  final int id;
  final String code;
  final String title;
  final String description;
  final int xpBonus;
  final int coinsBonus;
  final bool unlocked;
  final String? unlockedAt;

  factory Achievement.fromJson(Map<String, dynamic> json) => Achievement(
        id: json['id'] as int,
        code: json['code'] as String? ?? '',
        title: json['title'] as String? ?? '',
        description: json['description'] as String? ?? '',
        xpBonus: (json['xp_bonus'] as num?)?.toInt() ?? 0,
        coinsBonus: (json['coins_bonus'] as num?)?.toInt() ?? 0,
        unlocked: json['unlocked'] as bool? ?? false,
        unlockedAt: json['unlocked_at'] as String?,
      );
}
