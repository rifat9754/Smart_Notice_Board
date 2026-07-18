import 'package:flutter/material.dart';
import 'api.dart';

class SignupScreen extends StatefulWidget {
  const SignupScreen({super.key});
  @override
  State<SignupScreen> createState() => _SignupScreenState();
}

class _SignupScreenState extends State<SignupScreen> {
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _password = TextEditingController();
  String _role = 'student';
  bool _loading = false;
  bool _obscure = true;
  String? _error;
  String? _year;
  String? _section;

  Future<void> _doSignup() async {
    if (_name.text.trim().isEmpty || _email.text.trim().isEmpty || _password.text.isEmpty) {
      setState(() => _error = 'Please fill in all fields.');
      return;
    }
    // ── student হলে year/section বাধ্যতামূলক ──────────────────
    if (_role == 'student' && (_year == null || _section == null)) {
      setState(() => _error = 'Students must select year and section.');
      return;
    }

    setState(() { _loading = true; _error = null; });
    final res = await Api.register(
        _name.text.trim(), _email.text.trim(), _password.text, _role,
        year: _year, section: _section); // ← year/section এখন পাঠানো হচ্ছে
    if (!mounted) return;
    setState(() => _loading = false);

    if (res['ok'] == true) {
      showDialog(
        context: context,
        builder: (_) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
          title: const Text('Registration Successful'),
          content: Text(res['message']),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.pop(context);       // dialog বন্ধ
                Navigator.pop(context);       // login-এ ফেরত
              },
              child: const Text('OK'),
            ),
          ],
        ),
      );
    } else {
      setState(() => _error = res['message']);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scale = (MediaQuery.of(context).size.width / 390).clamp(0.85, 1.15);
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter, end: Alignment.bottomCenter,
            colors: [Color(0xFF1E3A8A), Color(0xFF0F172A)],
          ),
        ),
        child: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: EdgeInsets.all(28 * scale),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: EdgeInsets.all(20 * scale),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.12), shape: BoxShape.circle),
                    child: Icon(Icons.person_add, size: 48 * scale, color: Colors.white),
                  ),
                  SizedBox(height: 18 * scale),
                  Text('Create Account',
                      style: TextStyle(fontSize: 24 * scale, fontWeight: FontWeight.bold, color: Colors.white)),
                  SizedBox(height: 6 * scale),
                  Text('Join the KUET CSE Notice Board',
                      style: TextStyle(fontSize: 14 * scale, color: Colors.white.withOpacity(0.7))),
                  SizedBox(height: 30 * scale),

                  Container(
                    padding: EdgeInsets.all(22 * scale),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(18),
                    ),
                    child: Column(
                      children: [
                        TextField(
                          controller: _name,
                          decoration: InputDecoration(
                            labelText: 'Full Name',
                            prefixIcon: const Icon(Icons.person_outline),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                        ),
                        SizedBox(height: 14 * scale),
                        TextField(
                          controller: _email,
                          keyboardType: TextInputType.emailAddress,
                          decoration: InputDecoration(
                            labelText: 'Email',
                            prefixIcon: const Icon(Icons.email_outlined),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                        ),
                        SizedBox(height: 14 * scale),
                        TextField(
                          controller: _password,
                          obscureText: _obscure,
                          decoration: InputDecoration(
                            labelText: 'Password',
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(_obscure ? Icons.visibility_off : Icons.visibility),
                              onPressed: () => setState(() => _obscure = !_obscure),
                            ),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                        ),
                        SizedBox(height: 14 * scale),
                        // role dropdown
                        DropdownButtonFormField<String>(
                          value: _role,
                          decoration: InputDecoration(
                            labelText: 'Register as',
                            prefixIcon: const Icon(Icons.badge_outlined),
                            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                          ),
                          items: const [
                            DropdownMenuItem(value: 'student', child: Text('Student')),
                            DropdownMenuItem(value: 'teacher', child: Text('Teacher')),
                          ],
                          onChanged: (v) => setState(() {
                            _role = v!;
                            // role পাল্টালে আগের year/section clear করে দেওয়া হচ্ছে
                            // (যেমন student থেকে teacher-এ গেলে)
                            if (_role != 'student') {
                              _year = null;
                              _section = null;
                            }
                          }),
                        ),
                        if (_role == 'student') ...[
                          SizedBox(height: 8 * scale),
                          Text('Students must use a @stud.kuet.ac.bd email',
                              style: TextStyle(fontSize: 11.5 * scale, color: Colors.grey[600])),

                          // ── Year dropdown ────────────────────────
                          SizedBox(height: 14 * scale),
                          DropdownButtonFormField<String>(
                            value: _year,
                            decoration: InputDecoration(
                              labelText: 'Year',
                              prefixIcon: const Icon(Icons.school_outlined),
                              border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            items: const [
                              DropdownMenuItem(value: '1st', child: Text('1st Year')),
                              DropdownMenuItem(value: '2nd', child: Text('2nd Year')),
                              DropdownMenuItem(value: '3rd', child: Text('3rd Year')),
                              DropdownMenuItem(value: '4th', child: Text('4th Year')),
                            ],
                            onChanged: (v) => setState(() => _year = v),
                          ),

                          // ── Section dropdown ─────────────────────
                          SizedBox(height: 14 * scale),
                          DropdownButtonFormField<String>(
                            value: _section,
                            decoration: InputDecoration(
                              labelText: 'Section',
                              prefixIcon: const Icon(Icons.group_outlined),
                              border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            items: const [
                              DropdownMenuItem(value: 'A', child: Text('Section A')),
                              DropdownMenuItem(value: 'B', child: Text('Section B')),
                            ],
                            onChanged: (v) => setState(() => _section = v),
                          ),
                        ],
                        if (_error != null) ...[
                          SizedBox(height: 12 * scale),
                          Text(_error!, style: TextStyle(color: Colors.red, fontSize: 13 * scale)),
                        ],
                        SizedBox(height: 20 * scale),
                        SizedBox(
                          width: double.infinity,
                          height: 50 * scale,
                          child: FilledButton(
                            style: FilledButton.styleFrom(
                              backgroundColor: const Color(0xFF2563EB),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            onPressed: _loading ? null : _doSignup,
                            child: _loading
                                ? const SizedBox(height: 22, width: 22, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                : Text('Sign Up', style: TextStyle(fontSize: 16 * scale, fontWeight: FontWeight.bold)),
                          ),
                        ),
                      ],
                    ),
                  ),
                  SizedBox(height: 18 * scale),
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: Text('Already have an account? Sign in',
                        style: TextStyle(color: Colors.white.withOpacity(0.9), fontSize: 13 * scale)),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}