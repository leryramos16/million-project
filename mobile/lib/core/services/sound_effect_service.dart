import 'package:audioplayers/audioplayers.dart';

/// Short one-shot UI sounds (tab switches, page navigation) — a separate
/// low-latency player from the looping background music so both can play
/// independently without cutting each other off.
class SoundEffectService {
  SoundEffectService() : _player = AudioPlayer() {
    _player.setPlayerMode(PlayerMode.lowLatency);
    _configureContext();
  }

  final AudioPlayer _player;
  bool _contextReady = false;

  Future<void> _configureContext() async {
    try {
      // audioFocus: none — this player never requests focus, so it can't
      // steal it from (and silently pause) the background music.
      await _player.setAudioContext(AudioContext(
        android: AudioContextAndroid(
          isSpeakerphoneOn: false,
          stayAwake: false,
          contentType: AndroidContentType.sonification,
          usageType: AndroidUsageType.assistanceSonification,
          audioFocus: AndroidAudioFocus.none,
        ),
        // mixWithOthers is only valid with playback/playAndRecord/multiRoute.
        iOS: AudioContextIOS(
          category: AVAudioSessionCategory.playback,
          options: {AVAudioSessionOptions.mixWithOthers},
        ),
      ));
      _contextReady = true;
    } catch (_) {
      // Ignore — worst case the platform default context is used.
    }
  }

  Future<void> playPageTurn({required bool muted}) async {
    if (muted) return;
    if (!_contextReady) await _configureContext();

    try {
      // lowLatency (SoundPool on Android) can silently no-op on replay if
      // the player isn't explicitly reset first — stop() before each play
      // guarantees every trigger actually fires, not just the first.
      await _player.stop();
      await _player.play(AssetSource('audio/page_turn.wav'), volume: 0.6);
    } catch (_) {
      // No page_turn.wav bundled yet.
    }
  }

  void dispose() => _player.dispose();
}
