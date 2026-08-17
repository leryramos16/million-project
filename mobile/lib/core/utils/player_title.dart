/// Cosmetic rank shown next to a player's name, purely derived from level —
/// no backend state needed.
String playerTitleForLevel(int level) {
  if (level >= 50) return 'Legend';
  if (level >= 25) return 'Veteran';
  if (level >= 10) return 'Hunter';
  if (level >= 5) return 'Adventurer';
  return 'Novice';
}
