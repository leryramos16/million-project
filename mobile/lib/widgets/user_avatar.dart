import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';

class UserAvatar extends StatelessWidget {
  const UserAvatar({super.key, this.url, required this.radius, this.username});

  final String? url;
  final double radius;
  final String? username;

  @override
  Widget build(BuildContext context) {
    if (url == null) {
      return CircleAvatar(
        radius: radius,
        backgroundColor: AppColors.primarySoft,
        child: Text(
          (username != null && username!.isNotEmpty) ? username![0].toUpperCase() : '?',
          style: TextStyle(color: AppColors.textPrimary, fontSize: radius * 0.8, fontWeight: FontWeight.w700),
        ),
      );
    }

    return ClipOval(
      child: CachedNetworkImage(
        imageUrl: url!,
        width: radius * 2,
        height: radius * 2,
        fit: BoxFit.cover,
        placeholder: (context, url) => CircleAvatar(radius: radius, backgroundColor: AppColors.primarySoft),
        errorWidget: (context, url, error) => CircleAvatar(
          radius: radius,
          backgroundColor: AppColors.primarySoft,
          child: Icon(Icons.person, color: AppColors.textMuted, size: radius),
        ),
      ),
    );
  }
}
