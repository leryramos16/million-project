import 'package:dio/dio.dart';

import '../storage/token_storage.dart';

/// Android emulator reaches the host machine's XAMPP via 10.0.2.2.
/// A physical device over USB uses `adb reverse tcp:8080 tcp:80` so
/// `localhost:8080` on the phone forwards to the host's Apache on port 80.
/// On Wi-Fi instead of USB, replace with the host's LAN IP
/// (e.g. http://192.168.1.23/mymillionpesoproject/public/api).
const String kApiBaseUrl = 'http://localhost:8080/mymillionpesoproject/public/api';

class ApiException implements Exception {
  ApiException(this.message, {this.errors = const {}});

  final String message;
  final Map<String, dynamic> errors;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient(this._tokenStorage) {
    _dio = Dio(BaseOptions(baseUrl: kApiBaseUrl, connectTimeout: const Duration(seconds: 15)));

    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          if (!options.extra.containsKey('skipAuth')) {
            final token = await _tokenStorage.accessToken;
            if (token != null) {
              options.headers['Authorization'] = 'Bearer $token';
            }
          }
          handler.next(options);
        },
        onError: (error, handler) async {
          final isAuthEndpoint = error.requestOptions.path.contains('/auth/');

          if (error.response?.statusCode == 401 && !isAuthEndpoint && !_isRetry(error.requestOptions)) {
            final refreshed = await _tryRefresh();

            if (refreshed) {
              final retried = await _retry(error.requestOptions);
              return handler.resolve(retried);
            }
          }

          handler.next(error);
        },
      ),
    );
  }

  late final Dio _dio;
  final TokenStorage _tokenStorage;
  Future<bool>? _refreshInFlight;

  bool _isRetry(RequestOptions options) => options.extra['isRetry'] == true;

  Future<bool> _tryRefresh() {
    return _refreshInFlight ??= _doRefresh().whenComplete(() => _refreshInFlight = null);
  }

  Future<bool> _doRefresh() async {
    final refreshToken = await _tokenStorage.refreshToken;
    if (refreshToken == null) return false;

    try {
      final response = await _dio.post(
        '/v1/auth/refresh',
        data: {'refresh_token': refreshToken},
        options: Options(extra: {'skipAuth': true}),
      );

      final data = response.data['data'];
      await _tokenStorage.save(accessToken: data['access_token'], refreshToken: data['refresh_token']);
      return true;
    } catch (_) {
      await _tokenStorage.clear();
      return false;
    }
  }

  Future<Response> _retry(RequestOptions options) async {
    final token = await _tokenStorage.accessToken;
    final opts = Options(method: options.method, headers: {...options.headers, 'Authorization': 'Bearer $token'})
      ..extra = {...options.extra, 'isRetry': true};

    return _dio.request(options.path, data: options.data, queryParameters: options.queryParameters, options: opts);
  }

  Future<Map<String, dynamic>> get(String path, {Map<String, dynamic>? query, bool auth = true}) async {
    final res = await _dio.get(path, queryParameters: query, options: _authOption(auth));
    return _unwrap(res);
  }

  Future<Map<String, dynamic>> post(String path, {Map<String, dynamic>? data, bool auth = true}) async {
    final res = await _dio.post(path, data: data, options: _authOption(auth));
    return _unwrap(res);
  }

  Future<Map<String, dynamic>> put(String path, {Map<String, dynamic>? data, bool auth = true}) async {
    final res = await _dio.put(path, data: data, options: _authOption(auth));
    return _unwrap(res);
  }

  Future<Map<String, dynamic>> postMultipart(String path, FormData formData) async {
    final res = await _dio.post(path, data: formData);
    return _unwrap(res);
  }

  Options _authOption(bool auth) => auth ? Options() : Options(extra: {'skipAuth': true});

  Map<String, dynamic> _unwrap(Response res) {
    final body = res.data;
    if (body is Map<String, dynamic>) return body;
    return {'success': true, 'data': body};
  }

  /// Runs a request and converts Dio/API failures into a single [ApiException].
  Future<T> guarded<T>(Future<T> Function() action) async {
    try {
      return await action();
    } on DioException catch (e) {
      final data = e.response?.data;
      if (data is Map<String, dynamic>) {
        // PHP's json_encode([]) produces `[]`, not `{}`, so an empty error
        // map from the API arrives here as a List, not a Map.
        final rawErrors = data['errors'];
        throw ApiException(
          data['message']?.toString() ?? 'Something went wrong',
          errors: rawErrors is Map ? rawErrors.cast<String, dynamic>() : {},
        );
      }
      throw ApiException(e.message ?? 'Network error');
    }
  }
}
