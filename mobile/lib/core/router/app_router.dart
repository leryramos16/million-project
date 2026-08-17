import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../features/auth/login_screen.dart';
import '../../features/auth/register_screen.dart';
import '../../features/auth/splash_screen.dart';
import '../../features/home/home_shell.dart';
import '../../features/leaderboard/leaderboard_screen.dart';
import '../../features/profile/cashout_screen.dart';
import '../../features/quests/add_quest_screen.dart';
import '../../features/quests/quest_detail_screen.dart';
import '../providers.dart';

final routerProvider = Provider<GoRouter>((ref) {
  return GoRouter(
    initialLocation: '/splash',
    refreshListenable: _AuthListenable(ref),
    redirect: (context, state) {
      final auth = ref.read(authControllerProvider);
      final loggingIn = state.matchedLocation == '/login' || state.matchedLocation == '/register';
      final onSplash = state.matchedLocation == '/splash';

      if (auth.status == AuthStatus.unknown) {
        return onSplash ? null : '/splash';
      }
      if (auth.status == AuthStatus.unauthenticated) {
        return loggingIn ? null : '/login';
      }
      if (auth.status == AuthStatus.authenticated && (loggingIn || onSplash)) {
        return '/home';
      }
      return null;
    },
    routes: [
      GoRoute(path: '/splash', builder: (context, state) => const SplashScreen()),
      GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
      GoRoute(path: '/register', builder: (context, state) => const RegisterScreen()),
      GoRoute(path: '/home', builder: (context, state) => const HomeShell()),
      GoRoute(
        path: '/quests/new',
        builder: (context, state) => const AddQuestScreen(),
      ),
      GoRoute(
        path: '/leaderboard',
        builder: (context, state) => const LeaderboardScreen(),
      ),
      GoRoute(
        path: '/cashout',
        builder: (context, state) => const CashoutScreen(),
      ),
      GoRoute(
        path: '/quests/:id',
        builder: (context, state) => QuestDetailScreen(questId: int.parse(state.pathParameters['id']!)),
      ),
    ],
  );
});

class _AuthListenable extends ChangeNotifier {
  _AuthListenable(this.ref) {
    ref.listen(authControllerProvider, (_, _) => notifyListeners());
  }

  final Ref ref;
}
