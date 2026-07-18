import 'package:flutter/material.dart';
import 'login_screen.dart';
import 'signup_screen.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final w = size.width;
    final scale = (w / 390).clamp(0.85, 1.2);   // স্ক্রিন অনুযায়ী স্কেল

    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Color(0xFF0F172A), Color(0xFF1E3A8A), Color(0xFF2563EB)],
          ),
        ),
        child: SafeArea(
          child: LayoutBuilder(
            builder: (context, constraints) {
              return SingleChildScrollView(
                child: ConstrainedBox(
                  constraints: BoxConstraints(minHeight: constraints.maxHeight),
                  child: IntrinsicHeight(
                    child: Padding(
                      padding: EdgeInsets.symmetric(
                        horizontal: (28 * scale).clamp(20.0, 40.0),
                        vertical: 24,
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Spacer(),

                          // logo
                          Container(
                            width: (220 * scale).clamp(180.0, 280.0),
                            height: (130 * scale).clamp(110.0, 170.0),
                            padding: EdgeInsets.symmetric(
                                horizontal: 20 * scale, vertical: 16 * scale),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.96),
                              borderRadius: BorderRadius.circular(24),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.15),
                                  blurRadius: 16,
                                  offset: const Offset(0, 6),
                                ),
                              ],
                            ),
                            child: Image.asset(
                              'assets/images/kuet_logo.png',
                              fit: BoxFit.contain,
                              errorBuilder: (context, error, stackTrace) {
                                return Icon(Icons.campaign,
                                    size: 56 * scale, color: const Color(0xFF1E3A8A));
                              },
                            ),
                          ),
                          SizedBox(height: 24 * scale),

                          Text('KUET CSE',
                              style: TextStyle(
                                  fontSize: 32 * scale,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white)),
                          SizedBox(height: 4 * scale),
                          Text('Digital Notice Board',
                              style: TextStyle(
                                  fontSize: 18 * scale, color: Colors.white70)),
                          SizedBox(height: 12 * scale),
                          Text(
                            'Stay updated with all department notices,\nexams and class announcements.',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                                fontSize: 13 * scale,
                                color: Colors.white.withOpacity(0.6),
                                height: 1.5),
                          ),

                          const Spacer(),

                          // Login button
                          SizedBox(
                            width: double.infinity,
                            height: (52 * scale).clamp(48.0, 62.0),
                            child: FilledButton(
                              style: FilledButton.styleFrom(
                                backgroundColor: Colors.white,
                                foregroundColor: const Color(0xFF1E3A8A),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(14)),
                              ),
                              onPressed: () {
                                Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) => const LoginScreen()));
                              },
                              child: Text('Login',
                                  style: TextStyle(
                                      fontSize: 17 * scale,
                                      fontWeight: FontWeight.bold)),
                            ),
                          ),
SizedBox(height: 14 * scale),

                          // Sign Up button (outlined)
                          SizedBox(
                            width: double.infinity,
                            height: (52 * scale).clamp(48.0, 62.0),
                            child: OutlinedButton(
                              style: OutlinedButton.styleFrom(
                                backgroundColor: Colors.white,
                                foregroundColor: const Color(0xFF1E3A8A),
                                side: BorderSide(color: Colors.white.withOpacity(0.7), width: 1.5),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(14)),
                              ),
                              onPressed: () {
                                Navigator.push(
                                    context,
                                    MaterialPageRoute(
                                        builder: (_) => const SignupScreen()));
                              },
                              child: Text('Sign Up',
                                  style: TextStyle(
                                      fontSize: 17 * scale,
                                      fontWeight: FontWeight.bold)),
                            ),
                          ),
                          SizedBox(height: 16 * scale),

                          Text('Students & Teachers can sign in to continue',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                  fontSize: 12 * scale,
                                  color: Colors.white.withOpacity(0.5))),

                          SizedBox(height: 20 * scale),
                        ],
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        ),
      ),
    );
  }
}