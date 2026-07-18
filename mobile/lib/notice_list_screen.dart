import 'package:flutter/material.dart';
import 'api.dart';
import 'login_screen.dart';
import 'notice_detail_screen.dart';
import 'teacher_notifications_screen.dart';
import 'cr_post_screen.dart';
import 'cr_my_notices_screen.dart';

class NoticeListScreen extends StatefulWidget {
  const NoticeListScreen({super.key});
  @override
  State<NoticeListScreen> createState() => _NoticeListScreenState();
}

class _NoticeListScreenState extends State<NoticeListScreen> {
  late Future<Map<String, dynamic>> _future;
  String? _role;

  @override
  void initState() {
    super.initState();
    _future = Api.getNotices();
    Api.getRole().then((r) => setState(() => _role = r));
  }

  Future<void> _refresh() async {
    setState(() => _future = Api.getNotices());
  }

  Color _priorityColor(String p) {
    if (p == 'high') return const Color(0xFFDC2626);
    if (p == 'medium') return const Color(0xFFD97706);
    return const Color(0xFF2563EB);
  }

  IconData _priorityIcon(String p) {
    if (p == 'high') return Icons.priority_high;
    if (p == 'medium') return Icons.info_outline;
    return Icons.campaign_outlined;
  }

  @override
  Widget build(BuildContext context) {
    final w = MediaQuery.of(context).size.width;
    final scale = (w / 390).clamp(0.85, 1.15);
    final pad = (16 * scale).clamp(12.0, 20.0);

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: const Color(0xFFF1F5F9),
        appBar: AppBar(
          title: const Text('Notices', style: TextStyle(fontWeight: FontWeight.bold)),
          backgroundColor: const Color(0xFF1E3A8A),
          foregroundColor: Colors.white,
          elevation: 0,
          actions: [
            if (_role == 'cr')
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 4),
                child: w < 400
                    ? IconButton(
                        icon: const Icon(Icons.add_circle_rounded),
                        tooltip: 'Post',
                        onPressed: () => Navigator.push(context,
                            MaterialPageRoute(builder: (_) => const CrPostScreen())),
                      )
                    : FilledButton.icon(
                        style: FilledButton.styleFrom(
                          backgroundColor: Colors.white.withOpacity(0.16),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 14),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(20)),
                          elevation: 0,
                        ),
                        onPressed: () => Navigator.push(context,
                            MaterialPageRoute(builder: (_) => const CrPostScreen())),
                        icon: const Icon(Icons.add, size: 18),
                        label: const Text('Post',
                            style: TextStyle(fontWeight: FontWeight.w700)),
                      ),
              ),
            if (_role == 'cr')
              IconButton(
                icon: const Icon(Icons.assignment),
                tooltip: 'My Notices',
                onPressed: () => Navigator.push(context,
                    MaterialPageRoute(builder: (_) => const CrMyNoticesScreen())),
              ),
            if (_role == 'teacher' || _role == 'super_admin')
              IconButton(
                icon: const Icon(Icons.notifications),
                tooltip: 'Notices for Me',
                onPressed: () => Navigator.push(context,
                    MaterialPageRoute(builder: (_) => const TeacherNotificationsScreen())),
              ),
            IconButton(
              icon: const Icon(Icons.logout),
              tooltip: 'Logout',
              onPressed: () async {
                await Api.logout();
                if (!mounted) return;
                Navigator.pushReplacement(context,
                    MaterialPageRoute(builder: (_) => const LoginScreen()));
              },
            ),
          ],
          bottom: const TabBar(
            indicatorColor: Colors.white,
            indicatorWeight: 3,
            labelColor: Colors.white,
            unselectedLabelColor: Colors.white70,
            labelStyle: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
            tabs: [
              Tab(icon: Icon(Icons.apartment, size: 20), text: 'Departmental'),
              Tab(icon: Icon(Icons.groups, size: 20), text: 'Class Updates'),
            ],
          ),
        ),
        body: FutureBuilder<Map<String, dynamic>>(
          future: _future,
          builder: (context, snapshot) {
            if (snapshot.connectionState == ConnectionState.waiting) {
              return const Center(child: CircularProgressIndicator());
            }

            final data = snapshot.data ?? {'departmental': [], 'class_updates': []};
            final departmental = data['departmental'] as List;
            final classUpdates = data['class_updates'] as List;

            return TabBarView(
              children: [
                _noticeList(departmental, scale, pad,
                    emptyText: 'No departmental notices right now',
                    emptyIcon: Icons.apartment_outlined),
                _noticeList(classUpdates, scale, pad,
                    emptyText: 'No class updates right now',
                    emptyIcon: Icons.groups_outlined,
                    isClassUpdate: true),
              ],
            );
          },
        ),
      ),
    );
  }

  Widget _noticeList(List notices, double scale, double pad,
      {required String emptyText,
      required IconData emptyIcon,
      bool isClassUpdate = false}) {
    return RefreshIndicator(
      onRefresh: _refresh,
      color: const Color(0xFF1E3A8A),
      child: notices.isEmpty
          ? ListView(children: [
              SizedBox(height: MediaQuery.of(context).size.height * 0.22),
              Icon(emptyIcon, size: 70, color: Colors.grey[400]),
              const SizedBox(height: 12),
              Center(
                  child: Text(emptyText,
                      style: TextStyle(color: Colors.grey[600], fontSize: 15))),
            ])
          : ListView.builder(
              padding: EdgeInsets.symmetric(vertical: 12, horizontal: pad),
              itemCount: notices.length,
              itemBuilder: (context, i) {
                final n = notices[i];
                final pColor = _priorityColor(n['priority']);
                return Container(
                  margin: const EdgeInsets.only(bottom: 12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(16),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.06),
                          blurRadius: 10, offset: const Offset(0, 4)),
                    ],
                  ),
                  child: Material(
                    color: Colors.transparent,
                    child: InkWell(
                      borderRadius: BorderRadius.circular(16),
                      onTap: () => Navigator.push(context,
                          MaterialPageRoute(builder: (_) => NoticeDetailScreen(notice: n))),
                      child: Padding(
                        padding: EdgeInsets.all(pad),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Container(
                                  width: 44 * scale, height: 44 * scale,
                                  decoration: BoxDecoration(
                                    color: pColor.withOpacity(0.12),
                                    shape: BoxShape.circle,
                                  ),
                                  child: Icon(_priorityIcon(n['priority']), color: pColor, size: 22 * scale),
                                ),
                                SizedBox(width: 12 * scale),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(n['title'],
                                          maxLines: 2, overflow: TextOverflow.ellipsis,
                                          style: TextStyle(
                                              fontSize: 16 * scale, fontWeight: FontWeight.bold,
                                              color: const Color(0xFF1E293B))),
                                      const SizedBox(height: 4),
                                      Wrap(spacing: 6, children: [
                                        _chip(n['priority'].toString().toUpperCase(), pColor, scale),
                                        if (isClassUpdate && n['year'] != null && n['section'] != null)
                                          _chip('${n['year']} - ${n['section']}',
                                              const Color(0xFF059669), scale),
                                      ]),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            SizedBox(height: 12 * scale),
                            Text(n['body'],
                                maxLines: 2, overflow: TextOverflow.ellipsis,
                                style: TextStyle(fontSize: 14 * scale, color: Colors.grey[700], height: 1.4)),
                            if (n['ai_summary'] != null && n['ai_summary'].toString().isNotEmpty) ...[
                              SizedBox(height: 10 * scale),
                              Container(
                                padding: EdgeInsets.all(10 * scale),
                                decoration: BoxDecoration(
                                  color: const Color(0xFFEFF6FF),
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(color: const Color(0xFFBFDBFE)),
                                ),
                                child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Icon(Icons.auto_awesome, size: 15 * scale, color: const Color(0xFF2563EB)),
                                    const SizedBox(width: 6),
                                    Expanded(
                                      child: Text(n['ai_summary'],
                                          maxLines: 2, overflow: TextOverflow.ellipsis,
                                          style: TextStyle(fontSize: 12 * scale, color: const Color(0xFF1E40AF))),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                            SizedBox(height: 10 * scale),
                            Row(
                              mainAxisAlignment: MainAxisAlignment.end,
                              children: [
                                Text('Read more',
                                    style: TextStyle(fontSize: 12 * scale, fontWeight: FontWeight.w600, color: pColor)),
                                Icon(Icons.arrow_forward_ios, size: 11 * scale, color: pColor),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                );
              },
            ),
    );
  }

  Widget _chip(String text, Color color, double scale) => Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
        decoration: BoxDecoration(
          color: color.withOpacity(0.12),
          borderRadius: BorderRadius.circular(20),
        ),
        child: Text(text,
            style: TextStyle(fontSize: 10 * scale, fontWeight: FontWeight.bold, color: color)),
      );
}