import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'api.dart';

class CrMyNoticesScreen extends StatefulWidget {
  const CrMyNoticesScreen({super.key});
  @override
  State<CrMyNoticesScreen> createState() => _CrMyNoticesScreenState();
}

class _CrMyNoticesScreenState extends State<CrMyNoticesScreen>
    with SingleTickerProviderStateMixin {
  List notices = [];
  bool loading = true;
  final Map<int, bool> _expanded = {};
  final Map<int, bool> _deleting = {};

  // ── palette ──────────────────────────────────────────────
  static const _navy    = Color(0xFF0F2355);
  static const _blue    = Color(0xFF2563EB);
  static const _surface = Color(0xFFF3F6FB);
  static const _border  = Color(0xFFE8EDF5);

  static const _highFg = Color(0xFFDC2626);
  static const _highBg = Color(0xFFFFF1F1);
  static const _medFg  = Color(0xFFD97706);
  static const _medBg  = Color(0xFFFFF8ED);
  static const _lowFg  = Color(0xFF2563EB);
  static const _lowBg  = Color(0xFFEEF5FF);

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() => loading = true);
    final data = await Api.getMyCrNotices();
    if (!mounted) return;
    setState(() {
      notices = data;
      loading = false;
    });
  }

  _PriorityStyle _ps(String p) {
    switch (p) {
      case 'high':   return const _PriorityStyle(_highFg, _highBg, Icons.error_rounded, 'HIGH');
      case 'medium': return const _PriorityStyle(_medFg, _medBg, Icons.warning_amber_rounded, 'MEDIUM');
      default:       return const _PriorityStyle(_lowFg, _lowBg, Icons.info_rounded, 'LOW');
    }
  }

  Future<void> _delete(int id, String title) async {
    HapticFeedback.mediumImpact();
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        insetPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 24),
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 420),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(mainAxisSize: MainAxisSize.min, children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: _highBg,
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.delete_outline_rounded, color: _highFg, size: 30),
              ),
              const SizedBox(height: 16),
              const Text('Delete notice?',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: Color(0xFF0F172A))),
              const SizedBox(height: 8),
              Text(
                '"$title"',
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 13.5, color: Color(0xFF64748B), fontStyle: FontStyle.italic),
              ),
              const SizedBox(height: 6),
              const Text('This will permanently remove the notice.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 13, color: Color(0xFF94A3B8))),
              const SizedBox(height: 24),
              Row(children: [
                Expanded(
                  child: OutlinedButton(
                    style: OutlinedButton.styleFrom(
                      foregroundColor: const Color(0xFF64748B),
                      side: const BorderSide(color: _border),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      padding: const EdgeInsets.symmetric(vertical: 13),
                    ),
                    onPressed: () => Navigator.pop(context, false),
                    child: const Text('Cancel', style: TextStyle(fontWeight: FontWeight.w700)),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: FilledButton(
                    style: FilledButton.styleFrom(
                      backgroundColor: _highFg,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      padding: const EdgeInsets.symmetric(vertical: 13),
                    ),
                    onPressed: () => Navigator.pop(context, true),
                    child: const Text('Delete', style: TextStyle(fontWeight: FontWeight.w700)),
                  ),
                ),
              ]),
            ]),
          ),
        ),
      ),
    );

    if (confirm != true) return;
    setState(() => _deleting[id] = true);
    final ok = await Api.deleteCrNotice(id);
    if (!mounted) return;
    setState(() => _deleting[id] = false);
    if (ok) {
      _showSnack('Notice deleted', error: false);
      _load();
    } else {
      _showSnack('Failed to delete. Try again.', error: true);
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
            child: Text(msg,
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
          ),
        ]),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final replied = notices.where((n) => n['reply'] != null && n['reply'].toString().isNotEmpty).length;
    final pending = notices.length - replied;

    final media = MediaQuery.of(context);
    final width = media.size.width;
    final isTablet = width >= 700;
    // Clamp text scale so huge accessibility font settings don't blow up the header.
    final textScale = media.textScaler.scale(1.0).clamp(0.85, 1.3);

    // Base collapsed toolbar height + top safe area + room for title/subtitle/pills,
    // scaled by both device size and text scale so it never overflows.
    final baseExpanded = isTablet ? 168.0 : 150.0;
    final expandedHeight =
        (baseExpanded + media.padding.top * 0.4) * (textScale > 1.0 ? (1 + (textScale - 1) * 0.6) : 1.0);

    return Scaffold(
      backgroundColor: _surface,
      body: NestedScrollView(
        headerSliverBuilder: (_, __) => [
          SliverAppBar(
            pinned: true,
            expandedHeight: expandedHeight,
            backgroundColor: _navy,
            foregroundColor: Colors.white,
            elevation: 0,
            systemOverlayStyle: SystemUiOverlayStyle.light,
            flexibleSpace: FlexibleSpaceBar(
              collapseMode: CollapseMode.parallax,
              // No `title:` here on purpose — showing a second "My Notices"
              // in the pinned/collapsed title duplicated the one already in
              // the background, so it's removed.
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
                              'My Notices',
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: isTablet ? 30 : 26,
                                fontWeight: FontWeight.w800,
                                letterSpacing: -0.5,
                              ),
                            ),
                          ),
                          const SizedBox(height: 3),
                          Text(
                            loading
                                ? 'Loading…'
                                : '${notices.length} posted · $replied replied · $pending pending',
                            textAlign: TextAlign.center,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                                color: Colors.white.withOpacity(0.65),
                                fontSize: isTablet ? 13.5 : 12.5),
                          ),
                          if (!loading && notices.isNotEmpty) ...[
                            const SizedBox(height: 10),
                            Wrap(
                              alignment: WrapAlignment.center,
                              spacing: 8,
                              runSpacing: 6,
                              children: [
                                _statPill(Icons.mark_email_read_rounded, '$replied', 'Replied',
                                    const Color(0xFF059669)),
                                _statPill(Icons.hourglass_top_rounded, '$pending', 'Pending',
                                    const Color(0xFFD97706)),
                              ],
                            ),
                          ],
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
        body: loading
            ? _loadingState()
            : notices.isEmpty
                ? _emptyState()
                : RefreshIndicator(
                    color: _blue,
                    onRefresh: _load,
                    child: LayoutBuilder(
                      builder: (context, constraints) {
                        final contentWidth = constraints.maxWidth;
                        final horizontalPad = contentWidth >= 700
                            ? (contentWidth - 640) / 2 + 16
                            : 16.0;
                        return ListView.builder(
                          padding: EdgeInsets.fromLTRB(
                              horizontalPad, 10, horizontalPad, 32),
                          itemCount: notices.length,
                          itemBuilder: (_, i) => _buildCard(notices[i], isTablet),
                        );
                      },
                    ),
                  ),
      ),
    );
  }

  Widget _statPill(IconData icon, String value, String label, Color color) {
    return Container(
      constraints: const BoxConstraints(maxWidth: 160),
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.14),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.22)),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Icon(icon, color: color, size: 13),
        const SizedBox(width: 5),
        Flexible(
          child: Text('$value $label',
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              softWrap: false,
              style: const TextStyle(
                  color: Colors.white, fontSize: 11.5, fontWeight: FontWeight.w700)),
        ),
      ]),
    );
  }

  Widget _buildCard(dynamic n, bool isTablet) {
    final id  = n['id'] as int;
    final ps  = _ps(n['priority'] ?? 'low');
    final exp = _expanded[id] ?? false;
    final hasReply = n['reply'] != null && n['reply'].toString().isNotEmpty;
    final hasTeacher = n['teacher'] != null && n['teacher'].toString().isNotEmpty;
    final isDeleting = _deleting[id] ?? false;

    return AnimatedOpacity(
      duration: const Duration(milliseconds: 300),
      opacity: isDeleting ? 0.45 : 1.0,
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFE8EDF5)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 18,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

          // ── accent strip ──────────────────────────────────
          Container(
            height: 4,
            decoration: BoxDecoration(
              color: ps.fg,
              borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
            ),
          ),

          // ── badge row ─────────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
            child: Wrap(
              crossAxisAlignment: WrapCrossAlignment.center,
              spacing: 7,
              runSpacing: 7,
              children: [
                _badge(ps.bg, ps.fg, ps.icon, ps.label),
                if (n['year'] != null && n['section'] != null)
                  _badge(const Color(0xFFEEF5FF), _blue,
                      Icons.school_rounded, '${n['year']} · ${n['section']}'),
                hasReply
                    ? _badge(const Color(0xFFECFDF5), const Color(0xFF059669),
                        Icons.check_circle_rounded, 'Replied')
                    : _badge(const Color(0xFFFFF8ED), const Color(0xFFD97706),
                        Icons.hourglass_top_rounded, 'Pending'),
              ],
            ),
          ),

          // ── title ─────────────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: Text(n['title'] ?? '',
                style: TextStyle(
                  fontSize: isTablet ? 18.5 : 17,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF0F172A),
                  height: 1.25,
                )),
          ),

          // ── body (collapsible) ────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
            child: AnimatedCrossFade(
              duration: const Duration(milliseconds: 220),
              crossFadeState: exp ? CrossFadeState.showSecond : CrossFadeState.showFirst,
              firstChild: Text(n['body'] ?? '',
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 13.5, color: Color(0xFF475569), height: 1.5)),
              secondChild: Text(n['body'] ?? '',
                  style: const TextStyle(fontSize: 13.5, color: Color(0xFF475569), height: 1.5)),
            ),
          ),
          if ((n['body'] ?? '').toString().length > 100)
            GestureDetector(
              onTap: () => setState(() => _expanded[id] = !exp),
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 4, 16, 0),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  Text(exp ? 'Show less' : 'Show more',
                      style: const TextStyle(
                          color: _blue, fontSize: 12.5, fontWeight: FontWeight.w700)),
                  Icon(
                    exp ? Icons.keyboard_arrow_up_rounded : Icons.keyboard_arrow_down_rounded,
                    size: 15, color: _blue,
                  ),
                ]),
              ),
            ),

          // ── notified teacher ─────────────────────────────
          if (hasTeacher)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 0),
              child: Row(children: [
                const Icon(Icons.notifications_active_rounded, size: 13, color: Color(0xFF94A3B8)),
                const SizedBox(width: 5),
                Expanded(
                  child: Text('Notified: ${n['teacher']}',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12.5, color: Color(0xFF64748B))),
                ),
              ]),
            ),

          const SizedBox(height: 12),
          const Divider(height: 1, thickness: 1, color: Color(0xFFE8EDF5)),

          // ── teacher reply bubble ──────────────────────────
          if (hasReply)
            Container(
              margin: const EdgeInsets.fromLTRB(14, 14, 14, 0),
              padding: const EdgeInsets.all(14),
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
                const SizedBox(width: 10),
                Expanded(
                  child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text('Teacher replied',
                        style: TextStyle(
                          fontSize: 11, fontWeight: FontWeight.w800,
                          color: Color(0xFF059669), letterSpacing: 0.2,
                        )),
                    const SizedBox(height: 4),
                    Text(n['reply'].toString(),
                        style: const TextStyle(
                            fontSize: 13.5, color: Color(0xFF065F46), height: 1.4)),
                    if (n['replied_at'] != null) ...[
                      const SizedBox(height: 5),
                      Row(children: [
                        const Icon(Icons.schedule_rounded, size: 11, color: Color(0xFF6EE7B7)),
                        const SizedBox(width: 3),
                        Expanded(
                          child: Text(n['replied_at'].toString(),
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                  fontSize: 11, color: Color(0xFF6EE7B7))),
                        ),
                      ]),
                    ],
                  ]),
                ),
              ]),
            )
          else
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
              child: Row(children: [
                Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF8ED),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Icon(Icons.hourglass_top_rounded,
                      size: 13, color: Color(0xFFD97706)),
                ),
                const SizedBox(width: 8),
                const Text('Awaiting teacher reply',
                    style: TextStyle(
                      fontSize: 13, color: Color(0xFFD97706),
                      fontStyle: FontStyle.italic, fontWeight: FontWeight.w600,
                    )),
              ]),
            ),

          // ── delete button ─────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(14, 12, 14, 14),
            child: Align(
              alignment: Alignment.centerRight,
              child: GestureDetector(
                onTap: isDeleting ? null : () => _delete(id, n['title'] ?? ''),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 180),
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 9),
                  decoration: BoxDecoration(
                    color: isDeleting ? const Color(0xFFF1F5F9) : _highBg,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                        color: isDeleting
                            ? const Color(0xFFE2E8F0)
                            : _highFg.withOpacity(0.25)),
                  ),
                  child: Row(mainAxisSize: MainAxisSize.min, children: [
                    isDeleting
                        ? const SizedBox(
                            width: 14, height: 14,
                            child: CircularProgressIndicator(
                                strokeWidth: 1.8, color: Color(0xFF94A3B8)))
                        : const Icon(Icons.delete_outline_rounded,
                            size: 16, color: _highFg),
                    const SizedBox(width: 6),
                    Text(
                      isDeleting ? 'Deleting…' : 'Delete',
                      style: TextStyle(
                        fontSize: 13, fontWeight: FontWeight.w700,
                        color: isDeleting ? const Color(0xFF94A3B8) : _highFg,
                      ),
                    ),
                  ]),
                ),
              ),
            ),
          ),
        ]),
      ),
    );
  }

  Widget _badge(Color bg, Color fg, IconData icon, String label) => Container(
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
                style: TextStyle(
                    color: fg, fontSize: 11, fontWeight: FontWeight.w800, letterSpacing: 0.3)),
          ),
        ]),
      );

  Widget _loadingState() => Center(
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          SizedBox(
            width: 42, height: 42,
            child: CircularProgressIndicator(
              strokeWidth: 3,
              color: _blue,
              backgroundColor: _blue.withOpacity(0.12),
            ),
          ),
          const SizedBox(height: 16),
          const Text('Loading your notices…',
              style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
        ]),
      );

  Widget _emptyState() => Center(
        child: Padding(
          padding: const EdgeInsets.all(40),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            Container(
              padding: const EdgeInsets.all(26),
              decoration: const BoxDecoration(
                color: Color(0xFFEEF5FF),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.campaign_rounded, size: 46, color: _blue),
            ),
            const SizedBox(height: 20),
            const Text('No notices yet',
                style: TextStyle(
                    fontSize: 20, fontWeight: FontWeight.w800, color: Color(0xFF0F2355))),
            const SizedBox(height: 8),
            const Text(
              "You haven't posted any notices.\nTap + to create your first one.",
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 14, color: Color(0xFF64748B), height: 1.5),
            ),
          ]),
        ),
      );
}

// ── priority style ───────────────────────────────────────────
class _PriorityStyle {
  final Color fg, bg;
  final IconData icon;
  final String label;
  const _PriorityStyle(this.fg, this.bg, this.icon, this.label);
}