import 'package:url_launcher/url_launcher.dart';

/// Opens the device's own maps app (Google Maps, Waze, whatever's
/// installed) via a plain URL — no map SDK, no API key, no billing.
Future<void> openInMaps(double lat, double lng, {String? label}) async {
  final query = label != null && label.isNotEmpty ? Uri.encodeComponent(label) : '$lat,$lng';
  final geoUri = Uri.parse('geo:$lat,$lng?q=$lat,$lng($query)');

  if (await canLaunchUrl(geoUri)) {
    await launchUrl(geoUri);
    return;
  }

  final webUri = Uri.parse('https://www.google.com/maps/search/?api=1&query=$lat,$lng');
  await launchUrl(webUri, mode: LaunchMode.externalApplication);
}
