import 'package:firebase_messaging/firebase_messaging.dart';
import 'api.dart';

// app background-এ থাকলে message এলে এটা চলে (top-level function হতে হবে)
Future<void> _backgroundHandler(RemoteMessage message) async {
  // background-এ system নিজেই notification দেখায়, তাই এখানে কিছু না করলেও চলে
}

class PushService {
  static final _fcm = FirebaseMessaging.instance;

  static Future<void> init() async {
    // অনুমতি চাও (Android 13+ এ লাগে)
    await _fcm.requestPermission(alert: true, badge: true, sound: true);

    // background handler
    FirebaseMessaging.onBackgroundMessage(_backgroundHandler);

    // app খোলা থাকলে message এলে
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      print('Foreground notification: ${message.notification?.title}');
      // চাইলে এখানে in-app banner/snackbar দেখাতে পারো
    });
  }

  // login-এর পর token নিয়ে backend-এ পাঠাও
  static Future<void> registerToken() async {
    final token = await _fcm.getToken();
    if (token != null) {
      await Api.saveFcmToken(token);
      print('FCM token sent: $token');
    }

    // token বদলালে আবার পাঠাও
    _fcm.onTokenRefresh.listen((newToken) {
      Api.saveFcmToken(newToken);
    });
  }
}