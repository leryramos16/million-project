import '../../core/network/api_client.dart';
import '../../core/storage/token_storage.dart';
import '../models/user.dart';

class AuthRepository {
  AuthRepository(this._api, this._tokenStorage);

  final ApiClient _api;
  final TokenStorage _tokenStorage;

  Future<void> register({required String username, required String email, required String password}) {
    return _api.guarded(() => _api.post(
          '/v1/auth/register',
          data: {'username': username, 'email': email, 'password': password},
          auth: false,
        ));
  }

  Future<AppUser> login({required String usernameOrEmail, required String password}) {
    return _api.guarded(() async {
      final res = await _api.post(
        '/v1/auth/login',
        data: {'username_or_email': usernameOrEmail, 'password': password},
        auth: false,
      );

      final data = res['data'] as Map<String, dynamic>;
      await _tokenStorage.save(accessToken: data['access_token'], refreshToken: data['refresh_token']);
      return AppUser.fromJson(data['user'] as Map<String, dynamic>);
    });
  }

  Future<void> logout() async {
    final refreshToken = await _tokenStorage.refreshToken;
    try {
      if (refreshToken != null) {
        await _api.post('/v1/auth/logout', data: {'refresh_token': refreshToken}, auth: false);
      }
    } catch (_) {
      // Best-effort server-side revoke; local logout must succeed regardless.
    } finally {
      await _tokenStorage.clear();
    }
  }

  Future<bool> hasSession() async => (await _tokenStorage.accessToken) != null;
}
