import 'package:flutter/material.dart';

/// Shared page background — plain, light, and consistent across screens.
class QuestScaffold extends StatelessWidget {
  const QuestScaffold({super.key, required this.body, this.appBar, this.floatingActionButton});

  final Widget body;
  final PreferredSizeWidget? appBar;
  final Widget? floatingActionButton;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: appBar,
      floatingActionButton: floatingActionButton,
      body: SafeArea(child: body),
    );
  }
}
