import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:url_launcher/url_launcher.dart';

class NoticeDetailScreen extends StatefulWidget {
  final dynamic notice;
  const NoticeDetailScreen({super.key, required this.notice});
  @override
  State<NoticeDetailScreen> createState() => _NoticeDetailScreenState();
}

class _NoticeDetailScreenState extends State<NoticeDetailScreen> {
  bool _bookmarked = false;
  bool _openingAttachment = false;

  @override
  void initState() {
    super.initState();
    _loadBookmark();
  }

  Future<void> _loadBookmark() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getStringList('bookmarks') ?? [];
    setState(() => _bookmarked = saved.contains(widget.notice['id'].toString()));
  }

  Future<void> _toggleBookmark() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getStringList('bookmarks') ?? [];
    final id = widget.notice['id'].toString();
    saved.contains(id) ? saved.remove(id) : saved.add(id);
    await prefs.setStringList('bookmarks', saved);
    setState(() => _bookmarked = saved.contains(id));
    if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(_bookmarked ? 'Bookmarked' : 'Bookmark removed'),
        duration: const Duration(seconds: 1),
      ));
    }
  }

  // ── open the attachment (PDF etc.) in the device's default viewer/browser ──
  Future<void> _openAttachment(String fileUrl) async {
    if (_openingAttachment) return;
    setState(() => _openingAttachment = true);

    try {
      final uri = Uri.parse(fileUrl);
      final ok = await launchUrl(
        uri,
        // externalApplication opens PDFs etc. in the system viewer/browser
        // instead of trying (and failing) to render inside the Flutter app.
        mode: LaunchMode.externalApplication,
      );
      if (!ok && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
          content: Text('Could not open the attachment.'),
        ));
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('Failed to open attachment: $e'),
        ));
      }
    } finally {
      if (mounted) setState(() => _openingAttachment = false);
    }
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
    final n = widget.notice;
    final pColor = _priorityColor(n['priority']);

    final media = MediaQuery.of(context);
    final width = media.size.width;
    final isTablet = width >= 700;
    // Clamp so large accessibility text sizes can't blow up the header.
    final textScale = media.textScaler.scale(1.0).clamp(0.85, 1.3);
    final baseExpanded = isTablet ? 170.0 : 150.0;
    final expandedHeight =
        (baseExpanded + media.padding.top * 0.3) * (textScale > 1.0 ? (1 + (textScale - 1) * 0.5) : 1.0);

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9), // আলাদা হালকা background
      body: CustomScrollView(
        slivers: [
          // colored header
         SliverAppBar(
            expandedHeight: expandedHeight,
            pinned: true,
            backgroundColor: pColor,
            foregroundColor: Colors.white,
            flexibleSpace: FlexibleSpaceBar(
              centerTitle: true,
              title: FittedBox(
                fit: BoxFit.scaleDown,
                child: const Text('Notice Details',
                    style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
              ),
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [pColor, pColor.withOpacity(0.7)],
                  ),
                ),
                child: Center(
                  child: Padding(
                    padding: const EdgeInsets.only(bottom: 30),
                    child: Icon(_priorityIcon(n['priority']),
                        size: isTablet ? 64 : 54, color: Colors.white.withOpacity(0.85)),
                  ),
                ),
              ),
            ),
          ),

          // content
          SliverToBoxAdapter(
            child: LayoutBuilder(
              builder: (context, constraints) {
                // On wide/tablet screens, center the content with a sensible
                // max width instead of letting text stretch edge-to-edge.
                final contentWidth = constraints.maxWidth;
                final horizontalPad =
                    contentWidth >= 700 ? (contentWidth - 640) / 2 + 16 : 16.0;

                return Padding(
                  padding: EdgeInsets.fromLTRB(horizontalPad, 16, horizontalPad, 16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // priority badge
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
                        decoration: BoxDecoration(
                          color: pColor.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text('${n['priority'].toString().toUpperCase()} PRIORITY',
                            style: TextStyle(color: pColor, fontWeight: FontWeight.bold, fontSize: 12)),
                      ),
                      const SizedBox(height: 16),

                      // title
                      Text(n['title'],
                          style: TextStyle(
                              fontSize: isTablet ? 28 : 25,
                              fontWeight: FontWeight.bold,
                              color: const Color(0xFF1E293B),
                              height: 1.3)),
                      const SizedBox(height: 16),

                      // main body card
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(18),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 4))],
                        ),
                        child: Text(n['body'],
                            style: TextStyle(fontSize: isTablet ? 17 : 16, height: 1.6, color: const Color(0xFF334155))),
                      ),

                      // AI summary
                      if (n['ai_summary'] != null && n['ai_summary'].toString().isNotEmpty) ...[
                        const SizedBox(height: 16),
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.all(18),
                          decoration: BoxDecoration(
                            color: const Color(0xFFEFF6FF),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: const Color(0xFFBFDBFE)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: const [
                                  Icon(Icons.auto_awesome, size: 18, color: Color(0xFF2563EB)),
                                  SizedBox(width: 8),
                                  Text('AI Summary',
                                      style: TextStyle(color: Color(0xFF2563EB), fontWeight: FontWeight.bold, fontSize: 15)),
                                ],
                              ),
                              const SizedBox(height: 10),
                              Text(n['ai_summary'],
                                  style: const TextStyle(fontSize: 15, height: 1.5, color: Color(0xFF1E40AF))),
                            ],
                          ),
                        ),
                      ],

                      // attachment (if any) — now tappable, opens the PDF/file
                      if (n['file_url'] != null && n['file_url'].toString().isNotEmpty) ...[
                        const SizedBox(height: 16),
                        Material(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(14),
                          child: InkWell(
                            borderRadius: BorderRadius.circular(14),
                            onTap: _openingAttachment
                                ? null
                                : () => _openAttachment(n['file_url'].toString()),
                            child: Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(14),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(14),
                                boxShadow: [
                                  BoxShadow(
                                      color: Colors.black.withOpacity(0.05),
                                      blurRadius: 8,
                                      offset: const Offset(0, 3)),
                                ],
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    padding: const EdgeInsets.all(8),
                                    decoration: BoxDecoration(
                                      color: pColor.withOpacity(0.1),
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: _openingAttachment
                                        ? SizedBox(
                                            width: 20,
                                            height: 20,
                                            child: CircularProgressIndicator(
                                                strokeWidth: 2, color: pColor),
                                          )
                                        : Icon(Icons.picture_as_pdf_rounded, color: pColor),
                                  ),
                                  const SizedBox(width: 12),
                                  const Expanded(
                                    child: Text('View attachment',
                                        overflow: TextOverflow.ellipsis,
                                        style: TextStyle(
                                            color: Color(0xFF334155),
                                            fontWeight: FontWeight.w600)),
                                  ),
                                  const SizedBox(width: 8),
                                  Icon(Icons.open_in_new_rounded, size: 18, color: pColor),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ],

                      const SizedBox(height: 24),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}