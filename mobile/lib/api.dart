import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class Api {

static const String baseUrl = 'https://notice-board-jowu.onrender.com/api';

  static Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  static Future<String?> getRole() async {
  final prefs = await SharedPreferences.getInstance();
  return prefs.getString('role');
}

  

  static Future<bool> login(String email, String password) async {
    final res = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );
if (res.statusCode == 200) {
  final data = jsonDecode(res.body);
  final prefs = await SharedPreferences.getInstance();
  await prefs.setString('token', data['token']);
  await prefs.setString('name', data['user']['name']);
  await prefs.setString('role', data['user']['role'] ?? 'student');   // ← নতুন
  return true;
}
    return false;
  }


static Future<Map<String, dynamic>> register(
    String name, String email, String password, String role,
    {String? year, String? section}) async {
  final res = await http.post(
    Uri.parse('$baseUrl/register'),
    headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
    body: jsonEncode({
      'name': name, 'email': email, 'password': password, 'role': role,
      if (role == 'student') 'year': year,
      if (role == 'student') 'section': section,
    }),
  );
  final body = jsonDecode(res.body);
  return {
    'ok': res.statusCode == 201,
    'message': body['message'] ?? 'Something went wrong.',
  };
}

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('token');
    await prefs.remove('role');
await prefs.remove('name');
  }

static Future<Map<String, dynamic>> getNotices() async {
    final token = await getToken();
    final res = await http.get(
      Uri.parse('$baseUrl/notices'),
      headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
    );

    if (res.statusCode == 200) {
      final data = jsonDecode(res.body);
      return {
        'departmental': data['departmental'] ?? [],
        'class_updates': data['class_updates'] ?? [],
      };
    }
    return {'departmental': [], 'class_updates': []};
  }


static Future<Map<String, dynamic>> getMyNotifications() async {
  final token = await getToken();

  final res = await http.get(
    Uri.parse('$baseUrl/my-notifications'),
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );

  if (res.statusCode == 200) {
    final data = jsonDecode(res.body);
    return {
      'class_updates': data['class_updates'] ?? [],
      'for_teachers': data['for_teachers'] ?? [],
      'unseen': data['unseen'] ?? 0,
    };
  }

  return {
    'class_updates': [],
    'for_teachers': [],
    'unseen': 0,
  };
}

static Future<bool> replyToNotice(int noticeId, String reply) async {
  final token = await getToken();
  final res = await http.post(Uri.parse('$baseUrl/notices/$noticeId/reply'),
    headers: {'Accept':'application/json','Content-Type':'application/json','Authorization':'Bearer $token'},
    body: jsonEncode({'reply': reply}));
  return res.statusCode == 200;
}

static Future<void> markNotificationsSeen() async {
  final token = await getToken();

  await http.post(
    Uri.parse('$baseUrl/my-notifications/seen'),
    headers: {
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );
}

static Future<List<dynamic>> getTeachers() async {
  final token = await getToken();
  final res = await http.get(Uri.parse('$baseUrl/teachers'),
    headers: {'Accept':'application/json','Authorization':'Bearer $token'});
  return res.statusCode == 200 ? jsonDecode(res.body) : [];
}

// ── crPost() ── status code check ঠিক করা হয়েছে (201 ও accept করবে) ──
static Future<bool> crPost(Map<String, dynamic> data) async {
  final token = await getToken();
  final res = await http.post(
    Uri.parse('$baseUrl/cr/notice'),
    headers: {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    },
    body: jsonEncode({
      'title': data['title'],
      'body': data['body'],
      'priority': data['priority'],
      'course_id': data['course_id'],
      'notified_teacher_id': data['notified_teacher_id'],
      'display_line': data['display_line'],         
    }),
  );
  return res.statusCode == 201 || res.statusCode == 200;
}

static Future<List<dynamic>> getMyCrNotices() async {
  final token = await getToken();
  final res = await http.get(
    Uri.parse('$baseUrl/my-cr-notices'),
    headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
  );
  return res.statusCode == 200 ? jsonDecode(res.body) : [];
}

static Future<bool> deleteCrNotice(int noticeId) async {
  final token = await getToken();
  final res = await http.delete(
    Uri.parse('$baseUrl/my-cr-notices/$noticeId'),
    headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
  );
  return res.statusCode == 200;
}
static Future<void> saveFcmToken(String fcmToken) async {
  final token = await getToken();
  if (token == null) return;              // login না থাকলে বাদ

  await http.post(
    Uri.parse('$baseUrl/fcm-token'),
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    },
    body: jsonEncode({'fcm_token': fcmToken}),
  );
}

static Future<List<dynamic>> getCourses() async {
  final token = await getToken();
  final res = await http.get(
    Uri.parse('$baseUrl/courses'),
    headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
  );
  return res.statusCode == 200 ? jsonDecode(res.body) : [];
}

static Future<List<dynamic>> getCourseTeachers(int courseId) async {
  final token = await getToken();
  final res = await http.get(
    Uri.parse('$baseUrl/courses/$courseId/teachers'),
    headers: {'Accept': 'application/json', 'Authorization': 'Bearer $token'},
  );
  return res.statusCode == 200 ? jsonDecode(res.body) : [];
}


}