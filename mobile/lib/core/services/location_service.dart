import 'package:dio/dio.dart';
import 'package:geolocator/geolocator.dart';

/// GPS capture + free reverse geocoding via OpenStreetMap's Nominatim —
/// no API key, no billing. Used only to suggest a location label; the
/// player can always edit it by hand.
class LocationService {
  final Dio _dio = Dio();

  Future<Position> getCurrentPosition() async {
    final serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw Exception('Location services are turned off.');
    }

    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        throw Exception('Location permission was denied.');
      }
    }
    if (permission == LocationPermission.deniedForever) {
      throw Exception('Location permission is permanently denied. Enable it in Settings.');
    }

    return Geolocator.getCurrentPosition(
      locationSettings: const LocationSettings(accuracy: LocationAccuracy.high),
    );
  }

  Future<String?> reverseGeocode(double lat, double lng) async {
    try {
      final response = await _dio.get(
        'https://nominatim.openstreetmap.org/reverse',
        queryParameters: {'lat': lat, 'lon': lng, 'format': 'json', 'zoom': 16},
        options: Options(headers: {'User-Agent': 'QuestBoardApp/1.0'}),
      );

      final address = response.data['address'] as Map?;
      if (address == null) return response.data['display_name'] as String?;

      final parts = [
        address['suburb'] ?? address['village'] ?? address['neighbourhood'],
        address['city'] ?? address['town'] ?? address['municipality'],
        address['state'],
      ].whereType<String>().toList();

      return parts.isEmpty ? response.data['display_name'] as String? : parts.join(', ');
    } catch (_) {
      return null;
    }
  }
}
