import 'package:audioplayers/audioplayers.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Title/login screen background music. Loops `assets/audio/theme.mp3` if
/// present; fails silently if no track has been bundled yet so the app
/// never crashes over missing audio.
class MusicService {
  MusicService() : _player = AudioPlayer();

  final AudioPlayer _player;
  static const _mutedKey = 'music_muted';
  bool _started = false;

  Future<bool> loadMuted() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_mutedKey) ?? false;
  }

  Future<void> playTheme({required bool muted}) async {
    if (_started) return;
    _started = true;

    try {
      // Request NO audio focus at all — same as the SFX player. If either
      // stream participates in the focus system, Android can send the other
      // a focus-loss event and audioplayers pauses it with no auto-resume.
      // With both opted out entirely, they just mix as raw output.
      await _player.setAudioContext(AudioContext(
        android: AudioContextAndroid(
          isSpeakerphoneOn: false,
          stayAwake: false,
          contentType: AndroidContentType.music,
          usageType: AndroidUsageType.media,
          audioFocus: AndroidAudioFocus.none,
        ),
        iOS: AudioContextIOS(
          category: AVAudioSessionCategory.playback,
          options: {AVAudioSessionOptions.mixWithOthers},
        ),
      ));
      await _player.setReleaseMode(ReleaseMode.loop);
      await _player.setVolume(muted ? 0 : 0.5);
      await _player.play(AssetSource('audio/theme.mp3'));
    } catch (_) {
      // No theme.mp3 bundled yet.
    }
  }

  Future<void> stop() async {
    _started = false;
    try {
      await _player.stop();
    } catch (_) {
      // Ignore.
    }
  }

  Future<void> setMuted(bool muted) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_mutedKey, muted);
    try {
      await _player.setVolume(muted ? 0 : 0.5);
    } catch (_) {
      // Ignore.
    }
  }

  void dispose() => _player.dispose();
}
