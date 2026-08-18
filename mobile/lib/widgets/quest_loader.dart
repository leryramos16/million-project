import 'dart:math';

import 'package:flutter/material.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';

/// A spinning rune-compass loader with a pulsing glow, used everywhere the
/// app would otherwise show a plain CircularProgressIndicator.
class QuestLoader extends StatefulWidget {
  const QuestLoader({super.key, this.size = 56, this.label});

  final double size;
  final String? label;

  @override
  State<QuestLoader> createState() => _QuestLoaderState();
}

class _QuestLoaderState extends State<QuestLoader> with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(
    vsync: this,
    duration: const Duration(seconds: 2),
  )..repeat();

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        AnimatedBuilder(
          animation: _controller,
          builder: (context, child) {
            final t = _controller.value;
            final glow = 0.4 + 0.3 * sin(t * 2 * pi);

            return Container(
              width: widget.size,
              height: widget.size,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(color: AppColors.primary.withValues(alpha: glow * 0.35), blurRadius: 18, spreadRadius: 2),
                ],
              ),
              child: Stack(
                alignment: Alignment.center,
                children: [
                  Transform.rotate(
                    angle: t * 2 * pi,
                    child: CustomPaint(
                      size: Size(widget.size, widget.size),
                      painter: _RuneRingPainter(color: AppColors.primary.withValues(alpha: 0.9)),
                    ),
                  ),
                  Transform.rotate(
                    angle: -t * 2 * pi * 0.6,
                    child: Icon(Icons.shield_outlined, size: widget.size * 0.4, color: AppColors.textPrimary),
                  ),
                ],
              ),
            );
          },
        ),
        if (widget.label != null) ...[
          const SizedBox(height: 14),
          Text(widget.label!, style: AppTheme.body(12, color: AppColors.textMuted)),
        ],
      ],
    );
  }
}

class _RuneRingPainter extends CustomPainter {
  _RuneRingPainter({required this.color});

  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.width / 2 - 3;

    final ringPaint = Paint()
      ..color = color
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2.5
      ..strokeCap = StrokeCap.round;

    // Dashed ring: a series of short arcs, not a solid circle.
    const dashCount = 10;
    const sweep = (2 * pi / dashCount) * 0.6;

    for (var i = 0; i < dashCount; i++) {
      final start = i * (2 * pi / dashCount);
      canvas.drawArc(Rect.fromCircle(center: center, radius: radius), start, sweep, false, ringPaint);
    }

    // Four tick marks like a compass.
    final tickPaint = Paint()
      ..color = color
      ..strokeWidth = 2;

    for (var i = 0; i < 4; i++) {
      final angle = i * (pi / 2);
      final outer = center + Offset(cos(angle), sin(angle)) * radius;
      final inner = center + Offset(cos(angle), sin(angle)) * (radius - 6);
      canvas.drawLine(inner, outer, tickPaint);
    }
  }

  @override
  bool shouldRepaint(covariant _RuneRingPainter oldDelegate) => oldDelegate.color != color;
}
