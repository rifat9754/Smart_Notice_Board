import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'login_screen.dart';
import 'api.dart';


class SessionGuard extends StatefulWidget {
  final Widget child;


  final Duration timeout;

  const SessionGuard({
    super.key,
    required this.child,
    this.timeout = const Duration(minutes: 15),
  });

  @override
  State<SessionGuard> createState() => _SessionGuardState();
}

class _SessionGuardState extends State<SessionGuard>
    with WidgetsBindingObserver {
  static const _kLastActiveKey = 'last_active_time';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    _checkSession();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {

      _checkSession();
    } else if (state == AppLifecycleState.paused ||
        state == AppLifecycleState.inactive) {

      _updateLastActiveTime();
    }
  }

  Future<void> _updateLastActiveTime() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setInt(_kLastActiveKey, DateTime.now().millisecondsSinceEpoch);
  }

  Future<void> _checkSession() async {
    final prefs = await SharedPreferences.getInstance();
    final lastActiveMs = prefs.getInt(_kLastActiveKey);


    if (lastActiveMs == null) return;

    final lastActive = DateTime.fromMillisecondsSinceEpoch(lastActiveMs);
    final elapsed = DateTime.now().difference(lastActive);

    if (elapsed > widget.timeout) {
      await _forceLogout();
    } else {

      await _updateLastActiveTime();
    }
  }

  Future<void> _forceLogout() async {

    try {
      await Api.logout();
    } catch (_) {

    }

    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_kLastActiveKey);

    if (!mounted) return;


    final navigator = navigatorKey.currentState;
    if (navigator != null) {
      navigator.pushAndRemoveUntil(
        MaterialPageRoute(builder: (_) => const LoginScreen()),
        (route) => false,
      );
      ScaffoldMessenger.of(navigatorKey.currentContext!).showSnackBar(
        const SnackBar(content: Text('Session expired. Please log in again.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) => widget.child;
}

final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();