import 'package:flutter/material.dart';
import 'api.dart';

class CrPostScreen extends StatefulWidget {
  const CrPostScreen({super.key});
  @override
  State<CrPostScreen> createState() => _CrPostScreenState();
}

class _CrPostScreenState extends State<CrPostScreen> {
  final _title = TextEditingController();
  final _body = TextEditingController();
  final _displayLine = TextEditingController();
  String _priority = 'low';
  int? _courseId;
  String? _teacherId;
  List _courses = [];
  List _teachers = [];
  bool _saving = false;
  bool _loadingCourses = true;
  bool _loadingTeachers = false;

  static const _navy = Color(0xFF1E3A8A);
  static const _blue = Color(0xFF2563EB);
  static const _bg = Color(0xFFF3F6FB);
  static const _border = Color(0xFFE2E8F0);

  static const _priorities = [
    {'value': 'low', 'label': 'Low', 'color': Color(0xFF059669), 'icon': Icons.arrow_downward_rounded},
    {'value': 'medium', 'label': 'Medium', 'color': Color(0xFFD97706), 'icon': Icons.remove_rounded},
    {'value': 'high', 'label': 'High', 'color': Color(0xFFDC2626), 'icon': Icons.priority_high_rounded},
  ];

@override
  void initState() {
    super.initState();
    Api.getCourses().then((c) {
      if (!mounted) return;
      setState(() {
        _courses = c;
        _loadingCourses = false;
      });
    }).catchError((_) {
      if (!mounted) return;
      setState(() => _loadingCourses = false);
    });
  }

  Future<void> _onCourseChanged(int? courseId) async {
    setState(() {
      _courseId = courseId;
      _teacherId = null;
      _teachers = [];
      _loadingTeachers = courseId != null;
    });

    if (courseId == null) return;

    final teachers = await Api.getCourseTeachers(courseId);
    if (!mounted) return;
    setState(() {
      _teachers = teachers;
      _loadingTeachers = false;
    });
  }

@override
  void dispose() {
    _title.dispose();
    _body.dispose();
    _displayLine.dispose();
    super.dispose();
  }

  Color get _priorityColor =>
      (_priorities.firstWhere((p) => p['value'] == _priority)['color'] as Color);

Future<void> _submit() async {
    if (_title.text.trim().isEmpty || _body.text.trim().isEmpty) {
      _showSnack('Please fill in title and message', isError: true);
      return;
    }
    if (_courseId == null || _teacherId == null) {
      _showSnack('Please select a course and teacher', isError: true);
      return;
    }
    setState(() => _saving = true);

final ok = await Api.crPost({
      'title': _title.text.trim(),
      'body': _body.text.trim(),
      'priority': _priority,
      'course_id': _courseId,
      'notified_teacher_id': _teacherId,
      'display_line': _displayLine.text.trim().isEmpty ? null : _displayLine.text.trim(),
    });



    if (!mounted) return;
    setState(() => _saving = false);
    if (ok) {
      _showSnack('Notice posted successfully!', isError: false);
      Navigator.pop(context);
    } else {
      _showSnack('Failed to post. Try again.', isError: true);
    }
  }

  void _showSnack(String msg, {required bool isError}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(16),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        backgroundColor: isError ? const Color(0xFFDC2626) : const Color(0xFF059669),
        content: Row(children: [
          Icon(isError ? Icons.error_outline : Icons.check_circle_outline, color: Colors.white, size: 20),
          const SizedBox(width: 10),
          Expanded(child: Text(msg, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600))),
        ]),
      ),
    );
  }

  InputDecoration _dec(String label, IconData icon) => InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(color: Color(0xFF64748B)),
        prefixIcon: Icon(icon, color: _blue, size: 20),
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
        contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: _border)),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: _blue, width: 1.6)),
      );

  Widget _sectionLabel(String text, IconData icon) => Padding(
        padding: const EdgeInsets.only(bottom: 12),
        child: Row(children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(color: _navy.withOpacity(0.08), borderRadius: BorderRadius.circular(8)),
            child: Icon(icon, size: 16, color: _navy),
          ),
          const SizedBox(width: 8),
          Text(text,
              style: const TextStyle(fontWeight: FontWeight.w700, color: _navy, fontSize: 14, letterSpacing: 0.1)),
        ]),
      );

  Widget _card({required List<Widget> children}) => Container(
        margin: const EdgeInsets.only(bottom: 16),
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: _border.withOpacity(0.6)),
          boxShadow: [
            BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 16, offset: const Offset(0, 6)),
          ],
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: children),
      );

  Widget _priorityChip(Map<String, Object> p) {
    final selected = _priority == p['value'];
    final color = p['color'] as Color;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _priority = p['value'] as String),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          curve: Curves.easeOut,
          margin: const EdgeInsets.symmetric(horizontal: 4),
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: selected ? color.withOpacity(0.12) : const Color(0xFFF8FAFC),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: selected ? color : _border, width: selected ? 1.4 : 1),
          ),
          child: Column(children: [
            Icon(p['icon'] as IconData, size: 18, color: selected ? color : const Color(0xFF94A3B8)),
            const SizedBox(height: 4),
            Text(p['label'] as String,
                style: TextStyle(
                    fontSize: 12.5,
                    fontWeight: FontWeight.w700,
                    color: selected ? color : const Color(0xFF64748B))),
          ]),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final titleLen = _title.text.length;
    final bodyLen = _body.text.length;

    return Scaffold(
      backgroundColor: _bg,
      appBar: AppBar(
        title: const Text('Post Class Notice', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        backgroundColor: _navy,
        foregroundColor: Colors.white,
        elevation: 0,
        centerTitle: false,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // header banner
          Container(
            padding: const EdgeInsets.all(20),
            margin: const EdgeInsets.only(bottom: 20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [_navy, _blue],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(20),
              boxShadow: [
                BoxShadow(color: _blue.withOpacity(0.28), blurRadius: 20, offset: const Offset(0, 10)),
              ],
            ),
            child: Row(children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: Colors.white.withOpacity(0.15), shape: BoxShape.circle),
                child: const Icon(Icons.campaign_rounded, color: Colors.white, size: 26),
              ),
              const SizedBox(width: 14),
              const Expanded(
                child: Text(
                  'Share an update with your class and notify a teacher.',
                  style: TextStyle(color: Colors.white, fontSize: 13.5, height: 1.4, fontWeight: FontWeight.w500),
                ),
              ),
            ]),
          ),

          // notice content
          _card(children: [
            _sectionLabel('Notice Content', Icons.edit_note_rounded),
            TextField(
              controller: _title,
              maxLength: 80,
              onChanged: (_) => setState(() {}),
              decoration: _dec('Title', Icons.title_rounded).copyWith(
                counterText: '$titleLen/80',
              ),
            ),
const SizedBox(height: 12),
            TextField(
              controller: _body,
              maxLines: 4,
              maxLength: 500,
              onChanged: (_) => setState(() {}),
              decoration: _dec('Message', Icons.message_rounded).copyWith(
                alignLabelWithHint: true,
                counterText: '$bodyLen/500',
              ),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: _displayLine,
              maxLength: 120,
              maxLines: 1,
              onChanged: (_) => setState(() {}),
              decoration: _dec('Display Board Line (optional)', Icons.tv_rounded).copyWith(
                counterText: '${_displayLine.text.length}/120',
              ),
            ),
            const Padding(
              padding: EdgeInsets.only(top: 4, left: 4),
              child: Text('Shown on the digital display board. If empty, the title is shown.',
                  style: TextStyle(fontSize: 11.5, color: Color(0xFF94A3B8))),
            ),
          ]),

          // classification — year/section dropdown সরিয়ে শুধু priority রাখা হলো,
          // ও নিচে একটা info note দেওয়া হলো যে class auto-detect হবে।
          _card(children: [
            _sectionLabel('Classification', Icons.category_rounded),
            const Text('Priority', style: TextStyle(fontSize: 12.5, color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),
            Row(children: _priorities.map(_priorityChip).toList()),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFEFF6FF),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Row(children: const [
                Icon(Icons.info_outline, size: 18, color: Color(0xFF2563EB)),
                SizedBox(width: 8),
                Expanded(
                  child: Text(
                    'This notice will be posted for your class automatically.',
                    style: TextStyle(fontSize: 12, color: Color(0xFF1E40AF)),
                  ),
                ),
              ]),
            ),
          ]),



// course + teacher
          _card(children: [
            _sectionLabel('Course & Teacher', Icons.book_rounded),

            const Text('Course', style: TextStyle(fontSize: 12.5, color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),

            if (_loadingCourses)
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 8),
                child: Row(children: [
                  SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)),
                  SizedBox(width: 10),
                  Text('Loading courses…', style: TextStyle(color: Color(0xFF64748B), fontSize: 13)),
                ]),
              )
            else if (_courses.isEmpty)
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 8),
                child: Text('No courses available for your year.',
                    style: TextStyle(color: Color(0xFF94A3B8), fontSize: 13)),
              )
            else
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _border),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int>(
                    value: _courseId,
                    isExpanded: true,
                    hint: const Text('Select a course',
                        style: TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    icon: const Icon(Icons.keyboard_arrow_down_rounded, color: Color(0xFF64748B)),
                    style: const TextStyle(color: Color(0xFF1E293B), fontWeight: FontWeight.w600, fontSize: 14),
                    items: _courses.map<DropdownMenuItem<int>>((c) => DropdownMenuItem<int>(
                          value: c['id'],
                          child: Text('${c['course_no']} — ${c['course_title']}',
                              overflow: TextOverflow.ellipsis),
                        )).toList(),
                    onChanged: _onCourseChanged,
                  ),
                ),
              ),

            const SizedBox(height: 16),

            const Text('Teacher', style: TextStyle(fontSize: 12.5, color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),

            if (_courseId == null)
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _border),
                ),
                child: const Text('Select a course first',
                    style: TextStyle(color: Color(0xFF94A3B8), fontSize: 13)),
              )
            else if (_loadingTeachers)
              const Padding(
                padding: EdgeInsets.symmetric(vertical: 8),
                child: Row(children: [
                  SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)),
                  SizedBox(width: 10),
                  Text('Loading teachers…', style: TextStyle(color: Color(0xFF64748B), fontSize: 13)),
                ]),
              )
            else
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _border),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: _teacherId,
                    isExpanded: true,
                    hint: const Text('Select a teacher',
                        style: TextStyle(fontSize: 14, color: Color(0xFF94A3B8))),
                    icon: const Icon(Icons.keyboard_arrow_down_rounded, color: Color(0xFF64748B)),
                    style: const TextStyle(color: Color(0xFF1E293B), fontWeight: FontWeight.w600, fontSize: 14),
                    items: _teachers.map<DropdownMenuItem<String>>((t) => DropdownMenuItem<String>(
                          value: t['id'].toString(),
                          child: Row(children: [
                            CircleAvatar(
                              radius: 11,
                              backgroundColor: _blue.withOpacity(0.12),
                              child: Text(
                                (t['name'] as String).isNotEmpty ? (t['name'] as String)[0].toUpperCase() : '?',
                                style: const TextStyle(fontSize: 11, color: _blue, fontWeight: FontWeight.bold),
                              ),
                            ),
                            const SizedBox(width: 8),
                            Flexible(child: Text(t['name'], overflow: TextOverflow.ellipsis)),
                          ]),
                        )).toList(),
                    onChanged: (v) => setState(() => _teacherId = v),
                  ),
                ),
              ),
          ]),




          const SizedBox(height: 4),
          SizedBox(
            height: 54,
            child: FilledButton.icon(
              style: FilledButton.styleFrom(
                backgroundColor: _blue,
                disabledBackgroundColor: _blue.withOpacity(0.6),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 0,
              ),
              onPressed: _saving ? null : _submit,
              icon: _saving
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(strokeWidth: 2.2, color: Colors.white))
                  : const Icon(Icons.send_rounded),
              label: Text(_saving ? 'Posting…' : 'Post Notice',
                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }
}