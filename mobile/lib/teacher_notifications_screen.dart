import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'api.dart';
import 'login_screen.dart';

class TeacherNotificationsScreen extends StatefulWidget {
  const TeacherNotificationsScreen({super.key});

  @override
  State<TeacherNotificationsScreen> createState() =>
      _TeacherNotificationsScreenState();
}

class _TeacherNotificationsScreenState
    extends State<TeacherNotificationsScreen> with TickerProviderStateMixin {
  List classUpdates = [];
  List forTeachers = [];
  bool loading = true;
  final Map<int, TextEditingController> _replyControllers = {};
  final Map<int, bool> _sending = {};
  final Map<int, bool> _expanded = {};

  // ── palette ──────────────────────────────────────────────
  static const _navy = Color(0xFF0F2355);
  static const _blue = Color(0xFF2563EB);
  static const _amber = Color(0xFFD97706);
  static const _surface = Color(0xFFF7F9FC);
  static const _card = Colors.white;
  static const _divider = Color(0xFFE8EDF5);

  static const _highBg = Color(0xFFFFF1F1);
  static const _highFg = Color(0xFFDC2626);
  static const _medBg = Color(0xFFFFF8ED);
  static const _medFg = Color(0xFFD97706);
  static const _lowBg = Color(0xFFEEF5FF);
  static const _lowFg = Color(0xFF2563EB);

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    final data = await Api.getMyNotifications();
    if (!mounted) return;
    setState(() {
      classUpdates = data['class_updates'] ?? [];
      forTeachers = data['for_teachers'] ?? [];
      loading = false;
    });
    await Api.markNotificationsSeen();
  }

  @override
  void dispose() {
    for (final c in _replyControllers.values) c.dispose();
    super.dispose();
  }

  // ── priority helpers ─────────────────────────────────────
  _PriorityStyle _ps(String p) {
    switch (p) {
      case 'high':
        return const _PriorityStyle(_highFg, _highBg, Icons.error_rounded, 'HIGH');
      case 'medium':
        return const _PriorityStyle(_medFg, _medBg, Icons.warning_amber_rounded, 'MEDIUM');
      default:
        return const _PriorityStyle(_lowFg, _lowBg, Icons.info_rounded, 'LOW');
    }
  }

  Future<void> _sendReply(dynamic notice) async {
    final id = notice['id'] as int;
    final text = _replyControllers[id]?.text.trim() ?? '';
    if (text.isEmpty) return;

    HapticFeedback.lightImpact();
    setState(() => _sending[id] = true);

    final ok = await Api.replyToNotice(id, text);
    if (!mounted) return;
    setState(() => _sending[id] = false);

    if (ok) {
      _showSnack('Reply sent successfully', error: false);
      _replyControllers[id]?.clear();
      _load();
    } else {
      _showSnack('Failed to send reply', error: true);
    }
  }

  void _showSnack(String msg, {required bool error}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(16),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        backgroundColor: error ? _highFg : const Color(0xFF059669),
        duration: const Duration(seconds: 2),
        content: Row(children: [
          Icon(
            error ? Icons.error_outline_rounded : Icons.check_circle_outline_rounded,
            color: Colors.white, size: 18,
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(msg, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
          ),
        ]),
      ),
    );
  }

  // ── build ─────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final media = MediaQuery.of(context);
    final width = media.size.width;
    final isTablet = width >= 700;

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: _surface,
        body: NestedScrollView(
          headerSliverBuilder: (_, __) => [_buildAppBar(media, isTablet)],
          body: loading
              ? const _LoadingState()
              : TabBarView(
                  children: [
                    _fromCrTab(isTablet),
                    _forTeachersTab(isTablet),
                  ],
                ),
        ),
      ),
    );
  }

  // ── Tab 1: From CR (existing design, reply enabled) ──
  Widget _fromCrTab(bool isTablet) {
    if (classUpdates.isEmpty) {
      return const _EmptyState(text: 'No notices from CRs yet.\nPull down to refresh.');
    }
    return RefreshIndicator(
      color: _blue,
      onRefresh: _load,
      child: LayoutBuilder(
        builder: (context, constraints) {
          final contentWidth = constraints.maxWidth;
          final horizontalPad = contentWidth >= 700 ? (contentWidth - 640) / 2 + 16 : 16.0;
          return ListView.builder(
            padding: EdgeInsets.fromLTRB(horizontalPad, 12, horizontalPad, 32),
            itemCount: classUpdates.length,
            itemBuilder: (_, i) => _NoticeCard(
              notice: classUpdates[i],
              ps: _ps(classUpdates[i]['priority'] ?? 'low'),
              expanded: _expanded[classUpdates[i]['id']] ?? false,
              isTablet: isTablet,
              onToggle: () => setState(
                  () => _expanded[classUpdates[i]['id']] =
                      !(_expanded[classUpdates[i]['id']] ?? false)),
              controller: _replyControllers.putIfAbsent(
                  classUpdates[i]['id'], () => TextEditingController()),
              sending: _sending[classUpdates[i]['id']] ?? false,
              onSend: () => _sendReply(classUpdates[i]),
            ),
          );
        },
      ),
    );
  }

  // ── Tab 2: For Teachers (admin notices, read-only) ──
  Widget _forTeachersTab(bool isTablet) {
    if (forTeachers.isEmpty) {
      return const _EmptyState(text: 'No notices for teachers yet.\nPull down to refresh.');
    }
    return RefreshIndicator(
      color: _blue,
      onRefresh: _load,
      child: LayoutBuilder(
        builder: (context, constraints) {
          final contentWidth = constraints.maxWidth;
          final horizontalPad = contentWidth >= 700 ? (contentWidth - 640) / 2 + 16 : 16.0;
          return ListView.builder(
            padding: EdgeInsets.fromLTRB(horizontalPad, 12, horizontalPad, 32),
            itemCount: forTeachers.length,
            itemBuilder: (_, i) {
              final n = forTeachers[i];
              final ps = _ps(n['priority'] ?? 'low');
              return Container(
                margin: const EdgeInsets.only(bottom: 14),
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: _card,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: const Color(0xFFFDE68A)),
                  boxShadow: [
                    BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 14, offset: const Offset(0, 4)),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Wrap(spacing: 6, runSpacing: 6, children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(color: ps.bg, borderRadius: BorderRadius.circular(30)),
                        child: Text(ps.label,
                            style: TextStyle(color: ps.fg, fontSize: 11, fontWeight: FontWeight.w800)),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(
                            color: _amber.withOpacity(0.12), borderRadius: BorderRadius.circular(30)),
                        child: const Text('FROM HEAD',
                            style: TextStyle(color: _amber, fontSize: 11, fontWeight: FontWeight.w800)),
                      ),
                    ]),
                    const SizedBox(height: 10),
                    Text(n['title'] ?? '',
                        style: const TextStyle(
                            fontSize: 17, fontWeight: FontWeight.w800, color: Color(0xFF0F172A))),
                    const SizedBox(height: 8),
                    Text(n['body'] ?? '',
                        style: const TextStyle(fontSize: 13.5, color: Color(0xFF475569), height: 1.5)),
                    if (n['ai_summary'] != null && n['ai_summary'].toString().isNotEmpty) ...[
                      const SizedBox(height: 10),
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: const Color(0xFFEFF6FF),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: const Color(0xFFBFDBFE)),
                        ),
                        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                          const Icon(Icons.auto_awesome, size: 15, color: _blue),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(n['ai_summary'],
                                style: const TextStyle(fontSize: 13, color: Color(0xFF1E40AF))),
                          ),
                        ]),
                      ),
                    ],
                    const SizedBox(height: 10),
                    Row(children: [
                      const Icon(Icons.person_outline_rounded, size: 13, color: Color(0xFF94A3B8)),
                      const SizedBox(width: 4),
                      Text(n['author'] ?? 'Admin',
                          style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                      const SizedBox(width: 10),
                      const Icon(Icons.schedule_rounded, size: 13, color: Color(0xFF94A3B8)),
                      const SizedBox(width: 4),
                      Text(n['created'] ?? '',
                          style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                    ]),
                  ],
                ),
              );
            },
          );
        },
      ),
    );
  }

  SliverAppBar _buildAppBar(MediaQueryData media, bool isTablet) {
    final unread = classUpdates.where((n) => n['reply'] == null || n['reply'].toString().isEmpty).length;

    final textScale = media.textScaler.scale(1.0).clamp(0.85, 1.3);
    final baseExpanded = isTablet ? 178.0 : 160.0;
    final expandedHeight =
        (baseExpanded + media.padding.top * 0.4) * (textScale > 1.0? (1 + (textScale - 1) * 0.6) : 1.0);

    return SliverAppBar(
      pinned: true,
      expandedHeight: expandedHeight,
      backgroundColor: _navy,
      foregroundColor: Colors.white,
      elevation: 0,
      systemOverlayStyle: SystemUiOverlayStyle.light,
      actions: [
        IconButton(
          icon: const Icon(Icons.logout_rounded),
          tooltip: 'Logout',
          onPressed: () async {
            await Api.logout();
            if (!mounted) return;
            Navigator.pushReplacement(
              context, MaterialPageRoute(builder: (_) => const LoginScreen()));
          },
        ),
        const SizedBox(width: 4),
      ],
      flexibleSpace: FlexibleSpaceBar(
        collapseMode: CollapseMode.parallax,
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [_navy, Color(0xFF1A4BA0)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: SafeArea(
            bottom: false,
            child: Center(
              child: SingleChildScrollView(
                physics: const ClampingScrollPhysics(),
                padding: EdgeInsets.symmetric(
                  horizontal: isTablet ? 28 : 20,
                  vertical: 12,
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    FittedBox(
                      fit: BoxFit.scaleDown,
                      alignment: Alignment.center,
                      child: Text(
                        'Notices',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: isTablet ? 32 : 28,
                          fontWeight: FontWeight.w800,
                          letterSpacing: -0.5,
                        ),
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      loading
                          ? 'Loading…'
                          : '${classUpdates.length} from CR · ${forTeachers.length} for teachers',
                      textAlign: TextAlign.center,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(color: Colors.white.withOpacity(0.65), fontSize: isTablet ? 14 : 13),
                    ),
                    if (!loading && unread > 0) ...[
                      const SizedBox(height: 10),
                      Container(
                        constraints: const BoxConstraints(maxWidth:220),
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
                        decoration: BoxDecoration(
                          color: Colors.white.withOpacity(0.18),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(color: Colors.white.withOpacity(0.25)),
                        ),
                        child: Row(mainAxisSize: MainAxisSize.min, children: [
                          const Icon(Icons.mark_email_unread_rounded, color: Colors.white, size: 15),
                          const SizedBox(width: 5),
                          Flexible(
                            child: Text('$unread pending',
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                softWrap: false,
                                style: const TextStyle(
                                    color: Colors.white, fontSize: 12, fontWeight: FontWeight.w700)),
                          ),
                        ]),
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
      bottom: const TabBar(
        indicatorColor: Colors.white,
        indicatorWeight: 3,
        labelColor: Colors.white,
        unselectedLabelColor: Colors.white70,
        labelStyle: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
        tabs: [
          Tab(icon: Icon(Icons.groups, size: 20), text: 'From CR'),
          Tab(icon: Icon(Icons.school, size: 20), text: 'From Head'),
        ],
      ),
    );
  }
}

// ── priority style data ──────────────────────────────────────
class _PriorityStyle {
  final Color fg, bg;
  final IconData icon;
  final String label;
  const _PriorityStyle(this.fg, this.bg, this.icon, this.label);
}

// ── notice card (From CR tab) ───────────────────────────────────────────────
class _NoticeCard extends StatelessWidget {
  final dynamic notice;
  final _PriorityStyle ps;
  final bool expanded;
  final bool isTablet;
  final VoidCallback onToggle;
  final TextEditingController controller;
  final bool sending;
  final VoidCallback onSend;

  const _NoticeCard({
    required this.notice,
    required this.ps,
    required this.expanded,
    required this.isTablet,
    required this.onToggle,
    required this.controller,
    required this.sending,
    required this.onSend,
  });

  static const _navy = Color(0xFF0F2355);
  static const _blue = Color(0xFF2563EB);
  static const _divider = Color(0xFFE8EDF5);

  bool get hasReply =>
      notice['reply'] != null && notice['reply'].toString().isNotEmpty;

  Widget _badge(Color bg, Color fg, IconData icon, String label) =>Container(
        constraints: const BoxConstraints(maxWidth: 220),
        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
        decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(30)),
        child: Row(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, size: 11, color: fg),
          const SizedBox(width: 4),
          Flexible(
            child: Text(label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                softWrap: false,
                style: TextStyle(color: fg, fontSize: 11, fontWeight: FontWeight.w800, letterSpacing: 0.3)),
          ),
        ]),
      );

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: _TeacherNotificationsScreenState._card,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: _divider),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 18,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Container(
          height: 4,
          decoration: BoxDecoration(
            color: ps.fg,
            borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
          child: Wrap(
            crossAxisAlignment: WrapCrossAlignment.center,
            spacing: 8,
            runSpacing: 7,
            children: [
              _badge(ps.bg, ps.fg, ps.icon, ps.label),
              if (notice['year'] != null && notice['section'] != null)
                _badge(const Color(0xFFEEF5FF), _blue, Icons.school_rounded,
                    '${notice['year']} · ${notice['section']}'),
              if (notice['course'] != null)
                _badge(const Color(0xFFF3E8FF), const Color(0xFF7C3AED), Icons.book_rounded,
                    notice['course']),
              if (hasReply)
                _badge(const Color(0xFFECFDF5), const Color(0xFF059669),
                    Icons.check_circle_rounded, 'Replied'),
            ],
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
          child: Text(notice['title'] ?? '',
              style: TextStyle(
                fontSize: isTablet ? 18.5 : 17,
                fontWeight: FontWeight.w800,
                color: const Color(0xFF0F172A),
                height: 1.25,
              )),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
          child: AnimatedCrossFade(
            duration: const Duration(milliseconds: 220),
            crossFadeState:
                expanded ? CrossFadeState.showSecond : CrossFadeState.showFirst,
            firstChild: Text(
              notice['body'] ?? '',
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(
                  fontSize: 13.5, color: Color(0xFF475569), height:1.5),
            ),
            secondChild: Text(
              notice['body'] ?? '',
              style: const TextStyle(
                  fontSize: 13.5, color: Color(0xFF475569), height:1.5),
            ),
          ),
        ),
        if ((notice['body'] ?? '').toString().length > 100)
          GestureDetector(
            onTap: onToggle,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 4, 16, 0),
              child: Row(mainAxisSize: MainAxisSize.min, children: [
                Text(expanded ? 'Show less' : 'Show more',
                    style: const TextStyle(
                        color: _blue, fontSize: 12.5, fontWeight: FontWeight.w700)),
                const SizedBox(width: 2),
                Icon(
                  expanded ? Icons.keyboard_arrow_up_rounded : Icons.keyboard_arrow_down_rounded,
                  size: 15, color: _blue,
                ),
              ]),
            ),
          ),
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 10, 16, 14),
          child: Wrap(
            crossAxisAlignment: WrapCrossAlignment.center,
            spacing: 4,
            runSpacing: 4,
            children: [
              Row(mainAxisSize: MainAxisSize.min, children: [
                const Icon(Icons.person_outline_rounded, size: 13, color: Color(0xFF94A3B8)),
                const SizedBox(width: 4),
                ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 160),
                  child: Text(notice['from'] ?? '',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                ),
              ]),
              Row(mainAxisSize: MainAxisSize.min, children: [
                const SizedBox(width: 6),
                const Icon(Icons.schedule_rounded, size: 13, color:Color(0xFF94A3B8)),
                const SizedBox(width: 4),
                ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 160),
                  child: Text(notice['created'] ?? '',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12, color: Color(0xFF64748B))),
                ),
              ]),
            ],
          ),
        ),
        const Divider(height: 1, thickness: 1, color: _divider),
        if (hasReply)
          Container(
            margin: const EdgeInsets.fromLTRB(14, 14, 14, 0),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFFECFDF5), Color(0xFFF0FDF9)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: const Color(0xFFBBF7D0)),
            ),
            child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Icon(Icons.reply_rounded, size: 16, color: Color(0xFF059669)),
              const SizedBox(width: 8),
              Expanded(
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  const Text('Your reply',
                      style: TextStyle(
                        fontSize: 11, fontWeight: FontWeight.w800,
                        color: Color(0xFF059669), letterSpacing: 0.2,
                      )),
                  const SizedBox(height: 3),
                  Text(notice['reply'].toString(),
                      style: const TextStyle(fontSize: 13.5, color:Color(0xFF065F46), height: 1.4)),
                ]),
              ),
            ]),
          ),
        Padding(
          padding: const EdgeInsets.fromLTRB(14, 12, 14, 14),
          child: Row(crossAxisAlignment: CrossAxisAlignment.center,children: [
            Expanded(
              child: Container(
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: _divider),
                ),
                child: TextField(
                  controller: controller,
                  style: const TextStyle(fontSize: 14, color: Color(0xFF1E293B)),
                  minLines: 1,
                  maxLines: 3,
                  decoration: InputDecoration(
                    hintText: hasReply ? 'Update your reply…' : 'Reply to CR…',
                    hintStyle: const TextStyle(color: Color(0xFFB0BEC5), fontSize: 13.5),
                    contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    border: InputBorder.none,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 8),
            GestureDetector(
              onTap: sending ? null : onSend,
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 180),
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  gradient: sending
                      ? null
                      : const LinearGradient(
                          colors: [_navy, _blue],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                  color: sending ? const Color(0xFFE2E8F0) : null,
                  borderRadius: BorderRadius.circular(14),
                  boxShadow: sending
                      ? []
                      : [BoxShadow(color: _blue.withOpacity(0.35), blurRadius: 10, offset: const Offset(0, 4))],
                ),
                child: Center(
                  child: sending
                      ? const SizedBox(
                          width: 18, height: 18,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Color(0xFF94A3B8)))
                      : const Icon(Icons.send_rounded, color: Colors.white, size: 19),
                ),
              ),
            ),
          ]),
        ),
      ]),
    );
  }
}

// ── states ────────────────────────────────────────────────────
class _LoadingState extends StatelessWidget {
  const _LoadingState();
  @override
  Widget build(BuildContext context) => Center(
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          SizedBox(
            width: 42,
            height: 42,
            child: CircularProgressIndicator(
              strokeWidth: 3,
              color: const Color(0xFF2563EB),
              backgroundColor: const Color(0xFF2563EB).withOpacity(0.12),
            ),
          ),
          const SizedBox(height: 16),
          const Text('Loading notices…',
              style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
        ]),
      );
}

class _EmptyState extends StatelessWidget {
  final String text;
  const _EmptyState({this.text = 'No notices directed to you yet.\nPull down to refresh.'});
  @override
  Widget build(BuildContext context) => Center(
        child: Padding(
          padding: const EdgeInsets.all(40),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                color: const Color(0xFFEEF5FF),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.inbox_rounded, size: 44, color: Color(0xFF2563EB)),
            ),
            const SizedBox(height: 20),
            const Text('All clear!',
                style: TextStyle(
                    fontSize: 20, fontWeight: FontWeight.w800, color: Color(0xFF0F2355))),
            const SizedBox(height: 8),
            Text(
              text,
              textAlign: TextAlign.center,
              style: const TextStyle(fontSize: 14, color: Color(0xFF64748B), height: 1.5),
            ),
          ]),
        ),
      );
}