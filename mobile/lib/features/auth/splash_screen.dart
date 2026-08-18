import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/providers.dart';
import '../../widgets/quest_loader.dart';
import '../../widgets/quest_scaffold.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen> {
  @override
  void initState() {
    super.initState();
    // Title screen music starts here and keeps playing through login;
    // HomeShell stops it once the player is actually in the app.
    Future.microtask(() => ref.read(musicControllerProvider.notifier).playTheme());
  }

  @override
  Widget build(BuildContext context) {
    return QuestScaffold(
      body: Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: Image.asset('assets/images/logo.png', width: 88, height: 88, fit: BoxFit.cover),
            ),
            const SizedBox(height: 28),
            const QuestLoader(label: 'Entering the notice board...'),
          ],
        ),
      ),
    );
  }
}
