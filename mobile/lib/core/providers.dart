import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../data/models/user.dart';
import '../data/repositories/auth_repository.dart';
import '../data/repositories/quest_repository.dart';
import '../data/repositories/user_repository.dart';
import 'network/api_client.dart';
import 'storage/token_storage.dart';

final tokenStorageProvider = Provider((ref) => TokenStorage());

final apiClientProvider = Provider((ref) => ApiClient(ref.watch(tokenStorageProvider)));

final authRepositoryProvider = Provider(
  (ref) => AuthRepository(ref.watch(apiClientProvider), ref.watch(tokenStorageProvider)),
);

final userRepositoryProvider = Provider((ref) => UserRepository(ref.watch(apiClientProvider)));

final questRepositoryProvider = Provider((ref) => QuestRepository(ref.watch(apiClientProvider)));

enum AuthStatus { unknown, authenticated, unauthenticated }

class AuthState {
  const AuthState({required this.status, this.user});

  final AuthStatus status;
  final AppUser? user;

  AuthState copyWith({AuthStatus? status, AppUser? user}) =>
      AuthState(status: status ?? this.status, user: user ?? this.user);
}

class AuthController extends StateNotifier<AuthState> {
  AuthController(this._authRepository) : super(const AuthState(status: AuthStatus.unknown)) {
    _bootstrap();
  }

  final AuthRepository _authRepository;

  Future<void> _bootstrap() async {
    final hasSession = await _authRepository.hasSession();
    state = AuthState(status: hasSession ? AuthStatus.authenticated : AuthStatus.unauthenticated);
  }

  Future<void> login(String usernameOrEmail, String password) async {
    final user = await _authRepository.login(usernameOrEmail: usernameOrEmail, password: password);
    state = AuthState(status: AuthStatus.authenticated, user: user);
  }

  Future<void> register(String username, String email, String password) {
    return _authRepository.register(username: username, email: email, password: password);
  }

  Future<void> logout() async {
    await _authRepository.logout();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }
}

final authControllerProvider = StateNotifierProvider<AuthController, AuthState>(
  (ref) => AuthController(ref.watch(authRepositoryProvider)),
);
