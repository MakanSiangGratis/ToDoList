import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../settings.dart';
import '../auth/login.dart';
import 'package:intl/intl.dart';

class HomePage extends StatefulWidget {
  @override
  _HomePageState createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> with TickerProviderStateMixin {
  List<dynamic> _todos = [];
  bool _isLoading = true;
  String _userName = '';
  String _userEmail = '';
  
  // Animation controllers
  late AnimationController _headerAnimationController;
  late AnimationController _listAnimationController;
  late AnimationController _fabAnimationController;
  
  // Animations
  late Animation<double> _headerSlideAnimation;
  late Animation<double> _headerFadeAnimation;
  late Animation<double> _listFadeAnimation;
  late Animation<double> _fabScaleAnimation;

  @override
  void initState() {
    super.initState();
    _initAnimations();
    _loadUserData();
    _fetchTodos();
  }

  void _initAnimations() {
    _headerAnimationController = AnimationController(
      duration: Duration(milliseconds: 1200),
      vsync: this,
    );
    
    _listAnimationController = AnimationController(
      duration: Duration(milliseconds: 800),
      vsync: this,
    );
    
    _fabAnimationController = AnimationController(
      duration: Duration(milliseconds: 600),
      vsync: this,
    );

    _headerSlideAnimation = Tween<double>(
      begin: -100.0,
      end: 0.0,
    ).animate(CurvedAnimation(
      parent: _headerAnimationController,
      curve: Curves.elasticOut,
    ));

    _headerFadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _headerAnimationController,
      curve: Interval(0.3, 1.0, curve: Curves.easeOut),
    ));

    _listFadeAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _listAnimationController,
      curve: Curves.easeInOut,
    ));

    _fabScaleAnimation = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _fabAnimationController,
      curve: Curves.elasticOut,
    ));

    // Start animations
    _headerAnimationController.forward();
    Future.delayed(Duration(milliseconds: 300), () {
      _listAnimationController.forward();
    });
    Future.delayed(Duration(milliseconds: 600), () {
      _fabAnimationController.forward();
    });
  }

  @override
  void dispose() {
    _headerAnimationController.dispose();
    _listAnimationController.dispose();
    _fabAnimationController.dispose();
    super.dispose();
  }

  Future<void> _loadUserData() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      _userName = prefs.getString('user_name') ?? 'User';
      _userEmail = prefs.getString('user_email') ?? '';
    });
  }

  Future<void> _fetchTodos() async {
    setState(() {
      _isLoading = true;
    });

    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('$base_url/todos'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        setState(() {
          _todos = data['data'] ?? [];
        });
      } else {
        _showSnackBar('Failed to fetch todos', Colors.red);
      }
    } catch (e) {
      _showSnackBar('Network error: $e', Colors.red);
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  // Method baru untuk update status
  Future<void> _updateStatus(int todoId, String newStatus) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      final response = await http.patch(
        Uri.parse('$base_url/todos/$todoId/status'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: json.encode({
          'status': newStatus,
        }),
      );

      if (response.statusCode == 200) {
        _showSnackBar('Status updated successfully', Colors.green);
        _fetchTodos(); // Refresh list
      } else {
        _showSnackBar('Failed to update status', Colors.red);
      }
    } catch (e) {
      _showSnackBar('Network error: $e', Colors.red);
    }
  }

  Future<void> _logout() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      await http.post(
        Uri.parse('$base_url/logout'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      // Clear stored data
      await prefs.clear();

      // Navigate to login
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => LoginPage()),
      );
    } catch (e) {
      _showSnackBar('Logout error: $e', Colors.red);
    }
  }

  Future<void> _deleteTodo(int id) async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      String? token = prefs.getString('token');

      final response = await http.delete(
        Uri.parse('$base_url/todos/$id'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        _showSnackBar('Todo deleted successfully', Colors.green);
        _fetchTodos();
      } else {
        _showSnackBar('Failed to delete todo', Colors.red);
      }
    } catch (e) {
      _showSnackBar('Network error: $e', Colors.red);
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Container(
              padding: EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.2),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(
                color == Colors.green ? Icons.check_circle : Icons.error,
                color: Colors.white,
                size: 20,
              ),
            ),
            SizedBox(width: 12),
            Expanded(
              child: Text(
                message,
                style: TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 14,
                ),
              ),
            ),
          ],
        ),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
        ),
        margin: EdgeInsets.all(16),
        elevation: 8,
        duration: Duration(seconds: 3),
      ),
    );
  }

  void _showDeleteDialog(int todoId, String title) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
          ),
          elevation: 16,
          backgroundColor: Colors.white,
          title: Column(
            children: [
              Container(
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Colors.red.shade400, Colors.red.shade600],
                  ),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Icon(
                  Icons.delete_forever,
                  color: Colors.white,
                  size: 32,
                ),
              ),
              SizedBox(height: 16),
              Text(
                'Delete Todo',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[800],
                ),
              ),
            ],
          ),
          content: Text(
            'Are you sure you want to delete "$title"? This action cannot be undone.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey[600],
              height: 1.4,
            ),
          ),
          actions: [
            Row(
              children: [
                Expanded(
                  child: TextButton(
                    onPressed: () => Navigator.pop(context),
                    style: TextButton.styleFrom(
                      padding: EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                    child: Text(
                      'Cancel',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: Colors.grey[600],
                      ),
                    ),
                  ),
                ),
                SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton(
                    onPressed: () {
                      Navigator.pop(context);
                      _deleteTodo(todoId);
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.red,
                      foregroundColor: Colors.white,
                      padding: EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                      elevation: 4,
                    ),
                    child: Text(
                      'Delete',
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        );
      },
    );
  }

  // Method untuk menampilkan dialog bahwa tugas tidak bisa diedit
  void _showCompletedTaskDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
          ),
          elevation: 16,
          backgroundColor: Colors.white,
          title: Column(
            children: [
              Container(
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Colors.blue.shade400, Colors.blue.shade600],
                  ),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Icon(
                  Icons.info_rounded,
                  color: Colors.white,
                  size: 32,
                ),
              ),
              SizedBox(height: 16),
              Text(
                'Task Completed',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Colors.grey[800],
                ),
              ),
            ],
          ),
          content: Text(
            'This task is already completed and cannot be edited. You can mark it as pending to edit it again.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey[600],
              height: 1.4,
            ),
          ),
          actions: [
            Center(
              child: ElevatedButton(
                onPressed: () => Navigator.pop(context),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.blue[600],
                  foregroundColor: Colors.white,
                  padding: EdgeInsets.symmetric(horizontal: 32, vertical: 12),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  elevation: 4,
                ),
                child: Text(
                  'OK',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
          ],
        );
      },
    );
  }

  Color _getStatusColor(String status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return Colors.green;
      case 'pending':
        return Colors.orange;
      case 'in_progress':
        return Colors.blue;
      case 'late':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status.toLowerCase()) {
      case 'completed':
        return Icons.check_circle;
      case 'pending':
        return Icons.schedule;
      case 'in_progress':
        return Icons.timelapse;
      case 'late':
        return Icons.warning;
      default:
        return Icons.radio_button_unchecked;
    }
  }

  Widget _buildAnimatedTaskCard(Map<String, dynamic> todo, int index) {
    final status = todo['status'].toString().toLowerCase();
    final isCompleted = status == 'completed';
    
    return AnimatedBuilder(
      animation: _listAnimationController,
      builder: (context, child) {
        return Transform.translate(
          offset: Offset(
            0,
            50 * (1 - _listFadeAnimation.value) * (index + 1),
          ),
          child: Opacity(
            opacity: _listFadeAnimation.value,
            child: Container(
              margin: EdgeInsets.only(bottom: 16),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: isCompleted
                      ? [Colors.green.shade50, Colors.green.shade100]
                      : status == 'late'
                          ? [Colors.red.shade50, Colors.red.shade100]
                          : [Colors.white, Colors.blue.shade50],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: status == 'late'
                      ?                        Colors.red.withOpacity(0.3)
                      : isCompleted
                          ? Colors.green.withOpacity(0.3)
                          : Colors.blue.withOpacity(0.1),
                  width: 2,
                ),
                boxShadow: [
                  BoxShadow(
                    color: status == 'late'
                        ? Colors.red.withOpacity(0.15)
                        : isCompleted
                            ? Colors.green.withOpacity(0.15)
                            : Colors.blue.withOpacity(0.08),
                    blurRadius: 12,
                    offset: Offset(0, 4),
                    spreadRadius: 0,
                  ),
                ],
              ),
              child: Material(
                color: Colors.transparent,
                child: InkWell(
                  borderRadius: BorderRadius.circular(20),
                  onTap: () {
                    // Add haptic feedback
                    // HapticFeedback.lightImpact();
                  },
                  child: Padding(
                    padding: EdgeInsets.all(20),
                    child: Row(
                      children: [
                        // Status Toggle with Animation
                        GestureDetector(
                          onTap: () {
                            if (status == 'pending' || status == 'late') {
                              _updateStatus(todo['id'], 'completed');
                            } else if (status == 'completed') {
                              _updateStatus(todo['id'], 'pending');
                            }
                          },
                          child: AnimatedContainer(
                            duration: Duration(milliseconds: 300),
                            curve: Curves.elasticOut,
                            width: 28,
                            height: 28,
                            decoration: BoxDecoration(
                              gradient: isCompleted
                                  ? LinearGradient(
                                      colors: [Colors.green.shade400, Colors.green.shade600],
                                    )
                                  : null,
                              color: isCompleted ? null : Colors.transparent,
                              border: Border.all(
                                color: isCompleted
                                    ? Colors.transparent
                                    : _getStatusColor(status),
                                width: 2.5,
                              ),
                              borderRadius: BorderRadius.circular(8),
                              boxShadow: isCompleted
                                  ? [
                                      BoxShadow(
                                        color: Colors.green.withOpacity(0.3),
                                        blurRadius: 8,
                                        offset: Offset(0, 2),
                                      ),
                                    ]
                                  : null,
                            ),
                            child: isCompleted
                                ? Icon(
                                    Icons.check,
                                    size: 18,
                                    color: Colors.white,
                                  )
                                : null,
                          ),
                        ),
                        
                        SizedBox(width: 16),
                        
                        // Status Icon with Gradient Background
                        Container(
                          width: 56,
                          height: 56,
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              colors: [
                                _getStatusColor(status).withOpacity(0.2),
                                _getStatusColor(status).withOpacity(0.1),
                              ],
                            ),
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(
                              color: _getStatusColor(status).withOpacity(0.3),
                              width: 1,
                            ),
                          ),
                          child: Icon(
                            _getStatusIcon(status),
                            color: _getStatusColor(status),
                            size: 28,
                          ),
                        ),
                        
                        SizedBox(width: 16),
                        
                        // Content
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Title with enhanced styling
                              Text(
                                todo['title'] ?? 'No Title',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                  color: isCompleted ? Colors.grey[500] : Colors.grey[800],
                                  decoration: isCompleted
                                      ? TextDecoration.lineThrough
                                      : TextDecoration.none,
                                  decorationColor: Colors.grey,
                                  decorationThickness: 2,
                                  height: 1.2,
                                ),
                              ),
                              
                              // Description
                              if (todo['description'] != null && todo['description'].isNotEmpty) ...[
                                SizedBox(height: 6),
                                Text(
                                  todo['description'],
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: isCompleted ? Colors.grey[400] : Colors.grey[600],
                                    decoration: isCompleted
                                        ? TextDecoration.lineThrough
                                        : TextDecoration.none,
                                    height: 1.3,
                                  ),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                              
                              SizedBox(height: 12),
                              
                              // Status and Deadline Row
                              Row(
                                children: [
                                  // Deadline
                                  if (todo['deadline'] != null) ...[
                                    Container(
                                      padding: EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                      decoration: BoxDecoration(
                                        color: isCompleted
                                            ? Colors.grey[200]
                                            : status == 'late'
                                                ? Colors.red.shade100
                                                : Colors.blue.shade100,
                                        borderRadius: BorderRadius.circular(12),
                                        border: Border.all(
                                          color: isCompleted
                                              ? Colors.grey.shade300
                                              : status == 'late'
                                                  ? Colors.red.shade300
                                                  : Colors.blue.shade300,
                                          width: 1,
                                        ),
                                      ),
                                      child: Row(
                                        mainAxisSize: MainAxisSize.min,
                                        children: [
                                          Icon(
                                            Icons.access_time_rounded,
                                            size: 14,
                                            color: isCompleted
                                                ? Colors.grey[500]
                                                : status == 'late'
                                                    ? Colors.red.shade600
                                                    : Colors.blue.shade600,
                                          ),
                                          SizedBox(width: 4),
                                          Text(
                                            todo['deadline'] ?? 'No deadline',
                                            style: TextStyle(
                                              fontSize: 12,
                                              fontWeight: FontWeight.w600,
                                              color: isCompleted
                                                  ? Colors.grey[500]
                                                  : status == 'late'
                                                      ? Colors.red.shade600
                                                      : Colors.blue.shade600,
                                              decoration: isCompleted
                                                  ? TextDecoration.lineThrough
                                                  : TextDecoration.none,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    SizedBox(width: 8),
                                  ],
                                  
                                  // Status Badge
                                  Container(
                                    padding: EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                                    decoration: BoxDecoration(
                                      gradient: LinearGradient(
                                        colors: [
                                          _getStatusColor(status),
                                          _getStatusColor(status).withOpacity(0.8),
                                        ],
                                      ),
                                      borderRadius: BorderRadius.circular(12),
                                      boxShadow: [
                                        BoxShadow(
                                          color: _getStatusColor(status).withOpacity(0.3),
                                          blurRadius: 4,
                                          offset: Offset(0, 2),
                                        ),
                                      ],
                                    ),
                                    child: Text(
                                      isCompleted
                                          ? 'COMPLETED'
                                          : status == 'late'
                                              ? 'OVERDUE'
                                              : status.toUpperCase(),
                                      style: TextStyle(
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.white,
                                        letterSpacing: 0.5,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        
                        // Action Menu
                        PopupMenuButton<String>(
                          icon: Container(
                            padding: EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade100,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Icon(
                              Icons.more_vert,
                              color: Colors.grey[600],
                              size: 20,
                            ),
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                          elevation: 8,
                          onSelected: (String value) {
                            if (value == 'edit') {
                              if (isCompleted) {
                                _showCompletedTaskDialog();
                              } else {
                                _showEditDialog(todo);
                              }
                            } else if (value == 'delete') {
                              _showDeleteDialog(todo['id'], todo['title']);
                            } else if (value == 'mark_completed') {
                              _updateStatus(todo['id'], 'completed');
                            } else if (value == 'mark_pending') {
                              _updateStatus(todo['id'], 'pending');
                            }
                          },
                          itemBuilder: (BuildContext context) {
                            List<PopupMenuEntry<String>> menuItems = [];
                            
                            // Status change options
                            if (!isCompleted) {
                              menuItems.add(
                                PopupMenuItem<String>(
                                  value: 'mark_completed',
                                  child: Container(
                                    padding: EdgeInsets.symmetric(vertical: 4),
                                    child: Row(
                                      children: [
                                        Container(
                                          padding: EdgeInsets.all(6),
                                          decoration: BoxDecoration(
                                            color: Colors.green.shade100,
                                            borderRadius: BorderRadius.circular(8),
                                          ),
                                          child: Icon(Icons.check_circle, size: 16, color: Colors.green.shade600),
                                        ),
                                        SizedBox(width: 12),
                                        Text(
                                          'Mark as Completed',
                                          style: TextStyle(fontWeight: FontWeight.w500),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              );
                            }
                            
                            if (isCompleted) {
                              menuItems.add(
                                PopupMenuItem<String>(
                                  value: 'mark_pending',
                                  child: Container(
                                    padding: EdgeInsets.symmetric(vertical: 4),
                                    child: Row(
                                      children: [
                                        Container(
                                          padding: EdgeInsets.all(6),
                                          decoration: BoxDecoration(
                                            color: Colors.orange.shade100,
                                            borderRadius: BorderRadius.circular(8),
                                          ),
                                          child: Icon(Icons.schedule, size: 16, color: Colors.orange.shade600),
                                        ),
                                        SizedBox(width: 12),
                                        Text(
                                          'Mark as Pending',
                                          style: TextStyle(fontWeight: FontWeight.w500),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              );
                            }
                            
                            // Edit option
                            menuItems.add(
                              PopupMenuItem<String>(
                                value: 'edit',
                                enabled: !isCompleted,
                                child: Container(
                                  padding: EdgeInsets.symmetric(vertical: 4),
                                  child: Row(
                                    children: [
                                      Container(
                                        padding: EdgeInsets.all(6),
                                        decoration: BoxDecoration(
                                          color: isCompleted ? Colors.grey.shade200 : Colors.blue.shade100,
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Icon(
                                          Icons.edit,
                                          size: 16,
                                          color: isCompleted ? Colors.grey.shade400 : Colors.blue.shade600,
                                        ),
                                      ),
                                      SizedBox(width: 12),
                                      Text(
                                        'Edit',
                                        style: TextStyle(
                                          fontWeight: FontWeight.w500,
                                          color: isCompleted ? Colors.grey.shade400 : Colors.grey.shade700,
                                        ),
                                      ),
                                      if (isCompleted) ...[
                                        SizedBox(width: 8),
                                        Icon(
                                          Icons.lock,
                                          size: 12,
                                          color: Colors.grey.shade400,
                                        ),
                                      ],
                                    ],
                                  ),
                                ),
                              ),
                            );
                            
                            // Delete option
                            menuItems.add(
                              PopupMenuItem<String>(
                                value: 'delete',
                                child: Container(
                                  padding: EdgeInsets.symmetric(vertical: 4),
                                  child: Row(
                                    children: [
                                      Container(
                                        padding: EdgeInsets.all(6),
                                        decoration: BoxDecoration(
                                          color: Colors.red.shade100,
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Icon(Icons.delete, size: 16, color: Colors.red.shade600),
                                      ),
                                      SizedBox(width: 12),
                                      Text(
                                        'Delete',
                                        style: TextStyle(
                                          fontWeight: FontWeight.w500,
                                          color: Colors.red.shade600,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            );
                            
                            return menuItems;
                          },
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      body: CustomScrollView(
        slivers: [
          // Enhanced App Bar with Animation
          SliverAppBar(
            expandedHeight: 280,
            floating: false,
            pinned: true,
            elevation: 0,
            backgroundColor: Colors.transparent,
            flexibleSpace: AnimatedBuilder(
              animation: _headerAnimationController,
              builder: (context, child) {
                return Transform.translate(
                  offset: Offset(0, _headerSlideAnimation.value),
                                    child: Opacity(
                    opacity: _headerFadeAnimation.value,
                    child: FlexibleSpaceBar(
                      background: Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                            colors: [
                              Colors.blue[600]!,
                              Colors.blue[700]!,
                              Colors.indigo[600]!,
                            ],
                          ),
                        ),
                        child: Stack(
                          children: [
                            // Background Pattern
                            Positioned(
                              top: -50,
                              right: -50,
                              child: Container(
                                width: 200,
                                height: 200,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: Colors.white.withOpacity(0.1),
                                ),
                              ),
                            ),
                            Positioned(
                              bottom: -30,
                              left: -30,
                              child: Container(
                                width: 150,
                                height: 150,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: Colors.white.withOpacity(0.05),
                                ),
                              ),
                            ),
                            
                            // Content
                            SafeArea(
                              child: Padding(
                                padding: EdgeInsets.all(24),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    // Header Row
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                      children: [
                                        // User Avatar and Info
                                        Row(
                                          children: [
                                            Container(
                                              width: 50,
                                              height: 50,
                                              decoration: BoxDecoration(
                                                gradient: LinearGradient(
                                                  colors: [Colors.white, Colors.blue.shade100],
                                                ),
                                                borderRadius: BorderRadius.circular(16),
                                                boxShadow: [
                                                  BoxShadow(
                                                    color: Colors.black.withOpacity(0.1),
                                                    blurRadius: 8,
                                                    offset: Offset(0, 4),
                                                  ),
                                                ],
                                              ),
                                              child: Center(
                                                child: Text(
                                                  _userName.isNotEmpty ? _userName[0].toUpperCase() : 'U',
                                                  style: TextStyle(
                                                    fontSize: 20,
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.blue[700],
                                                  ),
                                                ),
                                              ),
                                            ),
                                            SizedBox(width: 12),
                                            Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Text(
                                                  'Welcome back,',
                                                  style: TextStyle(
                                                    color: Colors.white.withOpacity(0.8),
                                                    fontSize: 14,
                                                    fontWeight: FontWeight.w500,
                                                  ),
                                                ),
                                                Text(
                                                  _userName,
                                                  style: TextStyle(
                                                    color: Colors.white,
                                                    fontSize: 18,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ],
                                        ),
                                        
                                        // Menu Button
                                        PopupMenuButton<String>(
                                          icon: Container(
                                            padding: EdgeInsets.all(12),
                                            decoration: BoxDecoration(
                                              color: Colors.white.withOpacity(0.2),
                                              borderRadius: BorderRadius.circular(12),
                                              border: Border.all(
                                                color: Colors.white.withOpacity(0.3),
                                                width: 1,
                                              ),
                                            ),
                                            child: Icon(
                                              Icons.more_vert,
                                              color: Colors.white,
                                              size: 20,
                                            ),
                                          ),
                                          shape: RoundedRectangleBorder(
                                            borderRadius: BorderRadius.circular(16),
                                          ),
                                          elevation: 8,
                                          onSelected: (String value) {
                                            if (value == 'logout') {
                                              _logout();
                                            }
                                          },
                                          itemBuilder: (BuildContext context) => [
                                            PopupMenuItem<String>(
                                              value: 'logout',
                                              child: Container(
                                                padding: EdgeInsets.symmetric(vertical: 4),
                                                child: Row(
                                                  children: [
                                                    Container(
                                                      padding: EdgeInsets.all(6),
                                                      decoration: BoxDecoration(
                                                        color: Colors.red.shade100,
                                                        borderRadius: BorderRadius.circular(8),
                                                      ),
                                                      child: Icon(Icons.logout, size: 16, color: Colors.red.shade600),
                                                    ),
                                                    SizedBox(width: 12),
                                                    Text(
                                                      'Logout',
                                                      style: TextStyle(
                                                        fontWeight: FontWeight.w500,
                                                        color: Colors.red.shade600,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ],
                                    ),
                                    
                                    SizedBox(height: 32),
                                    
                                    // Stats Card
                                    Container(
                                      padding: EdgeInsets.all(20),
                                      decoration: BoxDecoration(
                                        color: Colors.white.withOpacity(0.15),
                                        borderRadius: BorderRadius.circular(20),
                                        border: Border.all(
                                          color: Colors.white.withOpacity(0.2),
                                          width: 1,
                                        ),
                                        boxShadow: [
                                          BoxShadow(
                                            color: Colors.black.withOpacity(0.1),
                                            blurRadius: 10,
                                            offset: Offset(0, 4),
                                          ),
                                        ],
                                      ),
                                      child: Row(
                                        children: [
                                          Container(
                                            padding: EdgeInsets.all(12),
                                            decoration: BoxDecoration(
                                              color: Colors.white.withOpacity(0.2),
                                              borderRadius: BorderRadius.circular(12),
                                            ),
                                            child: Icon(
                                              Icons.task_alt,
                                              color: Colors.white,
                                              size: 24,
                                            ),
                                          ),
                                          SizedBox(width: 16),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Text(
                                                  'Total Tasks',
                                                  style: TextStyle(
                                                    color: Colors.white.withOpacity(0.8),
                                                    fontSize: 14,
                                                    fontWeight: FontWeight.w500,
                                                  ),
                                                ),
                                                SizedBox(height: 4),
                                                Text(
                                                  '${_todos.length}',
                                                  style: TextStyle(
                                                    color: Colors.white,
                                                    fontSize: 24,
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                          // Progress indicator
                                          if (_todos.isNotEmpty) ...[
                                            Container(
                                              width: 60,
                                              height: 60,
                                              child: Stack(
                                                children: [
                                                  Container(
                                                    width: 60,
                                                    height: 60,
                                                    child: CircularProgressIndicator(
                                                      value: _todos.where((todo) => todo['status'] == 'completed').length / _todos.length,
                                                      backgroundColor: Colors.white.withOpacity(0.2),
                                                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                                      strokeWidth: 4,
                                                    ),
                                                  ),
                                                  Center(
                                                    child: Text(
                                                      '${((_todos.where((todo) => todo['status'] == 'completed').length / _todos.length) * 100).round()}%',
                                                      style: TextStyle(
                                                        color: Colors.white,
                                                        fontSize: 12,
                                                        fontWeight: FontWeight.bold,
                                                      ),
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ],
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
          
          // Todo List
          SliverToBoxAdapter(
            child: Container(
              padding: EdgeInsets.all(16),
              child: _isLoading
                  ? Container(
                      height: 400,
                      child: Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              width: 60,
                              height: 60,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [Colors.blue[400]!, Colors.blue[600]!],
                                ),
                                borderRadius: BorderRadius.circular(30),
                              ),
                              child: Center(
                                child: CircularProgressIndicator(
                                  valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                  strokeWidth: 3,
                                ),
                              ),
                            ),
                            SizedBox(height: 16),
                            Text(
                              'Loading your tasks...',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w500,
                                color: Colors.grey[600],
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                  : _todos.isEmpty
                      ? Container(
                          height: 400,
                          child: Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Container(
                                  width: 120,
                                  height: 120,
                                  decoration: BoxDecoration(
                                    gradient: LinearGradient(
                                      colors: [Colors.grey[300]!, Colors.grey[400]!],
                                    ),
                                    borderRadius: BorderRadius.circular(60),
                                  ),
                                  child: Icon(
                                    Icons.task_outlined,
                                    size: 60,
                                    color: Colors.white,
                                  ),
                                ),
                                SizedBox(height: 24),
                                Text(
                                  'No todos yet',
                                  style: TextStyle(
                                    fontSize: 24,
                                    fontWeight: FontWeight.bold,
                                    color: Colors.grey[700],
                                  ),
                                ),
                                SizedBox(height: 8),
                                Text(
                                  'Add your first todo to get started',
                                  style: TextStyle(
                                    fontSize: 16,
                                    color: Colors.grey[500],
                                  ),
                                ),
                                SizedBox(height: 24),
                                ElevatedButton.icon(
                                  onPressed: () => _showAddDialog(),
                                  icon: Icon(Icons.add),
                                  label: Text('Add First Todo'),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.blue[600],
                                    foregroundColor: Colors.white,
                                    padding: EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(16),
                                    ),
                                    elevation: 4,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        )
                      : RefreshIndicator(
                          onRefresh: _fetchTodos,
                          color: Colors.blue[600],
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Section Header
                              Padding(
                                padding: EdgeInsets.symmetric(horizontal: 4, vertical: 8),
                                child: Row(
                                  children: [
                                    Container(
                                      padding: EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        gradient: LinearGradient(
                                          colors: [Colors.blue[400]!, Colors.blue[600]!],
                                        ),
                                        borderRadius: BorderRadius.circular(12),
                                      ),
                                      child: Icon(
                                        Icons.list_alt,
                                        color: Colors.white,
                                        size: 20,
                                      ),
                                    ),
                                    SizedBox(width: 12),
                                    Text(
                                      'My Tasks',
                                      style: TextStyle(
                                        fontSize: 20,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.grey[800],
                                      ),
                                    ),
                                    Spacer(),
                                    Container(
                                      padding: EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                      decoration: BoxDecoration(
                                        color: Colors.blue[50],
                                        borderRadius: BorderRadius.circular(12),
                                        border: Border.all(
                                          color: Colors.blue[200]!,
                                          width: 1,
                                        ),
                                      ),
                                      child: Text(
                                        '${_todos.length} tasks',
                                        style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.w600,
                                          color: Colors.blue[700],
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              
                              SizedBox(height: 16),
                              
                              // Tasks List
                              ListView.builder(
                                shrinkWrap: true,
                                physics: NeverScrollableScrollPhysics(),
                                itemCount: _todos.length,
                                itemBuilder: (context, index) {
                                  return _buildAnimatedTaskCard(_todos[index], index);
                                },
                              ),
                            ],
                          ),
                        ),
            ),
          ),
        ],
      ),
      
      // Enhanced Floating Action Button
            // Enhanced Floating Action Button
      floatingActionButton: AnimatedBuilder(
        animation: _fabAnimationController,
        builder: (context, child) {
          return Transform.scale(
            scale: _fabScaleAnimation.value,
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [Colors.blue[400]!, Colors.blue[600]!],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.blue.withOpacity(0.3),
                    blurRadius: 12,
                    offset: Offset(0, 6),
                    spreadRadius: 0,
                  ),
                ],
              ),
              child: FloatingActionButton.extended(
                onPressed: () => _showAddDialog(),
                backgroundColor: Colors.transparent,
                elevation: 0,
                icon: Container(
                  padding: EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(
                    Icons.add,
                    color: Colors.white,
                    size: 20,
                  ),
                ),
                label: Text(
                  'Add Task',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  void _showAddDialog() {
    _showTodoDialog();
  }

  void _showEditDialog(Map<String, dynamic> todo) {
    // Double check - jangan biarkan edit jika completed
    if (todo['status'].toString().toLowerCase() == 'completed') {
      _showCompletedTaskDialog();
      return;
    }
    _showTodoDialog(todo: todo);
  }

  void _showTodoDialog({Map<String, dynamic>? todo}) {
    final _titleController = TextEditingController(text: todo?['title'] ?? '');
    final _descriptionController = TextEditingController(text: todo?['description'] ?? '');
    final _deadlineController = TextEditingController(text: todo?['deadline'] ?? '');
    final _formKey = GlobalKey<FormState>();
    bool _isLoading = false;
    DateTime? _selectedDate;

    // Parse existing date if editing
    if (todo != null && todo['deadline'] != null) {
      try {
        _selectedDate = DateTime.parse(todo['deadline']);
      } catch (e) {
        _selectedDate = null;
      }
    }

    Future<void> _selectDate() async {
      final DateTime now = DateTime.now();
      final DateTime today = DateTime(now.year, now.month, now.day);
      
      final DateTime? picked = await showDatePicker(
        context: context,
        initialDate: _selectedDate ?? today,
        firstDate: today, // Tidak boleh pilih tanggal sebelum hari ini
        lastDate: DateTime(2030),
        helpText: 'Select deadline',
        cancelText: 'Cancel',
        confirmText: 'OK',
        builder: (context, child) {
          return Theme(
            data: Theme.of(context).copyWith(
              colorScheme: ColorScheme.light(
                primary: Colors.blue[600]!,
                onPrimary: Colors.white,
                surface: Colors.white,
                onSurface: Colors.black,
              ),
            ),
            child: child!,
          );
        },
      );

      if (picked != null) {
        setState(() {
          _selectedDate = picked;
          _deadlineController.text = DateFormat('yyyy-MM-dd').format(picked);
        });
      }
    }

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setState) {
            return Dialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(28),
              ),
              elevation: 16,
              backgroundColor: Colors.transparent,
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      Colors.white,
                      Colors.blue.shade50,
                    ],
                  ),
                  borderRadius: BorderRadius.circular(28),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 20,
                      offset: Offset(0, 10),
                    ),
                  ],
                ),
                child: SingleChildScrollView(
                  child: Padding(
                    padding: EdgeInsets.all(24),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Header
                        Container(
                          padding: EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              colors: todo == null 
                                  ? [Colors.green.shade400, Colors.green.shade600]
                                  : [Colors.blue.shade400, Colors.blue.shade600],
                            ),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Icon(
                            todo == null ? Icons.add_task : Icons.edit,
                            color: Colors.white,
                            size: 32,
                          ),
                        ),
                        
                        SizedBox(height: 16),
                        
                        Text(
                          todo == null ? 'Add New Task' : 'Edit Task',
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: Colors.grey[800],
                          ),
                        ),
                        
                        SizedBox(height: 8),
                        
                        Text(
                          todo == null 
                              ? 'Create a new task to stay organized'
                              : 'Update your task details',
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey[600],
                          ),
                        ),
                        
                        SizedBox(height: 24),
                        
                        // Form
                        Form(
                          key: _formKey,
                          child: Column(
                            children: [
                              // Title Field
                              Container(
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(
                                    color: Colors.grey.shade200,
                                    width: 1,
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.05),
                                      blurRadius: 8,
                                      offset: Offset(0, 2),
                                    ),
                                  ],
                                ),
                                child: TextFormField(
                                  controller: _titleController,
                                  decoration: InputDecoration(
                                    labelText: 'Task Title *',
                                    labelStyle: TextStyle(
                                      color: Colors.grey[600],
                                      fontWeight: FontWeight.w500,
                                    ),
                                    prefixIcon: Container(
                                      margin: EdgeInsets.all(12),
                                      padding: EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: Colors.blue.shade100,
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Icon(
                                        Icons.title,
                                        color: Colors.blue[600],
                                        size: 20,
                                      ),
                                    ),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                                  ),
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w500,
                                  ),
                                  validator: (value) {
                                    if (value == null || value.trim().isEmpty) {
                                      return 'Please enter a title';
                                    }
                                    return null;
                                  },
                                ),
                              ),
                              
                              SizedBox(height: 16),

                              // Description Field
                              Container(
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(
                                    color: Colors.grey.shade200,
                                    width: 1,
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.05),
                                      blurRadius: 8,
                                      offset: Offset(0, 2),
                                    ),
                                  ],
                                ),
                                child: TextFormField(
                                  controller: _descriptionController,
                                  decoration: InputDecoration(
                                    labelText: 'Description',
                                    labelStyle: TextStyle(
                                      color: Colors.grey[600],
                                      fontWeight: FontWeight.w500,
                                    ),
                                    prefixIcon: Container(
                                      margin: EdgeInsets.all(12),
                                      padding: EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: Colors.orange.shade100,
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Icon(
                                        Icons.description,
                                        color: Colors.orange[600],
                                        size: 20,
                                      ),
                                    ),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                                  ),
                                  maxLines: 3,
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ),
                              
                              SizedBox(height: 16),

                              // Deadline Field
                              Container(
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(
                                    color: Colors.grey.shade200,
                                    width: 1,
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.05),
                                      blurRadius: 8,
                                      offset: Offset(0, 2),
                                    ),
                                  ],
                                ),
                                child: TextFormField(
                                  controller: _deadlineController,
                                  decoration: InputDecoration(
                                    labelText: 'Deadline *',
                                    labelStyle: TextStyle(
                                      color: Colors.grey[600],
                                      fontWeight: FontWeight.w500,
                                    ),
                                    prefixIcon: Container(
                                      margin: EdgeInsets.all(12),
                                      padding: EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: Colors.purple.shade100,
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Icon(
                                        Icons.calendar_today,
                                        color: Colors.purple[600],
                                        size: 20,
                                      ),
                                    ),
                                    suffixIcon: IconButton(
                                      icon: Container(
                                        padding: EdgeInsets.all(8),
                                        decoration: BoxDecoration(
                                          color: Colors.purple.shade100,
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: Icon(
                                          Icons.date_range,
                                          color: Colors.purple[600],
                                          size: 20,
                                        ),
                                      ),
                                      onPressed: _selectDate,
                                    ),
                                    border: InputBorder.none,
                                    contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                                    hintText: 'Select deadline date',
                                    hintStyle: TextStyle(
                                      color: Colors.grey[400],
                                    ),
                                  ),
                                  readOnly: true,
                                  onTap: _selectDate,
                                  style: TextStyle(
                                    fontSize: 16,
                                    fontWeight: FontWeight.w500,
                                  ),
                                  validator: (value) {
                                    if (value == null || value.isEmpty) {
                                      return 'Please select a deadline';
                                    }
                                    
                                    // Validasi tambahan: cek apakah tanggal tidak kurang dari hari ini
                                    try {
                                      final selectedDate = DateTime.parse(value);
                                      final today = DateTime.now();
                                      final todayOnly = DateTime(today.year, today.month, today.day);
                                      final selectedOnly = DateTime(selectedDate.year, selectedDate.month, selectedDate.day);
                                      
                                      if (selectedOnly.isBefore(todayOnly)) {
                                        return 'Deadline cannot be before today';
                                      }
                                    } catch (e) {
                                      return 'Invalid date format';
                                    }
                                    
                                    return null;
                                  },
                                ),
                              ),
                              
                              // Selected Date Info
                              if (_selectedDate != null) ...[
                                SizedBox(height: 12),
                                Container(
                                  padding: EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    gradient: LinearGradient(
                                      colors: [Colors.blue.shade50, Colors.blue.shade100],
                                    ),
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(
                                      color: Colors.blue.shade200,
                                      width: 1,
                                    ),
                                  ),
                                  child: Row(
                                    children: [
                                      Container(
                                        padding: EdgeInsets.all(6),
                                        decoration: BoxDecoration(
                                          color: Colors.blue.shade200,
                                          borderRadius: BorderRadius.circular(6),
                                        ),
                                        child: Icon(
                                          Icons.info,
                                          size: 16,
                                          color: Colors.blue[700],
                                        ),
                                      ),
                                      SizedBox(width: 8),
                                      Expanded(
                                        child: Text(
                                          'Selected: ${DateFormat('EEEE, MMM dd, yyyy').format(_selectedDate!)}',
                                          style: TextStyle(
                                            fontSize: 12,
                                            fontWeight: FontWeight.w600,
                                            color: Colors.blue[700],
                                                                                 ),
                                       ),
                                     ),
                                    ],
                                  ),
                                ),
                              ],
                            ],
                          ),
                        ),
                        
                        SizedBox(height: 32),
                        
                        // Action Buttons
                        Row(
                          children: [
                            // Cancel Button
                            Expanded(
                              child: Container(
                                decoration: BoxDecoration(
                                  color: Colors.grey.shade100,
                                  borderRadius: BorderRadius.circular(16),
                                  border: Border.all(
                                    color: Colors.grey.shade300,
                                    width: 1,
                                  ),
                                ),
                                child: TextButton(
                                  onPressed: _isLoading ? null : () => Navigator.pop(context),
                                  style: TextButton.styleFrom(
                                    padding: EdgeInsets.symmetric(vertical: 16),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(16),
                                    ),
                                  ),
                                  child: Text(
                                    'Cancel',
                                    style: TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.w600,
                                      color: Colors.grey[700],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            
                            SizedBox(width: 16),
                            
                            // Save Button
                            Expanded(
                              child: Container(
                                decoration: BoxDecoration(
                                  gradient: LinearGradient(
                                    colors: todo == null 
                                        ? [Colors.green.shade400, Colors.green.shade600]
                                        : [Colors.blue.shade400, Colors.blue.shade600],
                                  ),
                                  borderRadius: BorderRadius.circular(16),
                                  boxShadow: [
                                    BoxShadow(
                                      color: (todo == null ? Colors.green : Colors.blue).withOpacity(0.3),
                                      blurRadius: 8,
                                      offset: Offset(0, 4),
                                    ),
                                  ],
                                ),
                                child: ElevatedButton(
                                  onPressed: _isLoading
                                      ? null
                                      : () async {
                                          if (_formKey.currentState!.validate()) {
                                            setState(() {
                                              _isLoading = true;
                                            });

                                            try {
                                              SharedPreferences prefs = await SharedPreferences.getInstance();
                                              String? token = prefs.getString('token');

                                              final url = todo == null
                                                  ? '$base_url/todos'
                                                  : '$base_url/todos/${todo['id']}';

                                              final method = todo == null ? 'POST' : 'PUT';

                                              final response = method == 'POST'
                                                  ? await http.post(
                                                      Uri.parse(url),
                                                      headers: {
                                                        'Content-Type': 'application/json',
                                                        'Accept': 'application/json',
                                                        'Authorization': 'Bearer $token',
                                                      },
                                                      body: json.encode({
                                                        'title': _titleController.text.trim(),
                                                        'description': _descriptionController.text.trim(),
                                                        'deadline': _deadlineController.text,
                                                      }),
                                                    )
                                                  : await http.put(
                                                      Uri.parse(url),
                                                      headers: {
                                                        'Content-Type': 'application/json',
                                                        'Accept': 'application/json',
                                                        'Authorization': 'Bearer $token',
                                                      },
                                                      body: json.encode({
                                                        'title': _titleController.text.trim(),
                                                        'description': _descriptionController.text.trim(),
                                                        'deadline': _deadlineController.text,
                                                      }),
                                                    );

                                              if (response.statusCode == 200) {
                                                Navigator.pop(context);
                                                _showSnackBar(
                                                  todo == null ? 'Todo added successfully' : 'Todo updated successfully',
                                                  Colors.green,
                                                );
                                                _fetchTodos();
                                              } else {
                                                final errorData = json.decode(response.body);
                                                _showSnackBar(
                                                  errorData['message'] ?? 'Failed to save todo',
                                                  Colors.red,
                                                );
                                              }
                                            } catch (e) {
                                              _showSnackBar('Network error: $e', Colors.red);
                                            } finally {
                                              setState(() {
                                                _isLoading = false;
                                              });
                                            }
                                          }
                                        },
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: Colors.transparent,
                                    foregroundColor: Colors.white,
                                    elevation: 0,
                                    padding: EdgeInsets.symmetric(vertical: 16),
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(16),
                                    ),
                                  ),
                                  child: _isLoading
                                      ? Row(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            SizedBox(
                                              width: 20,
                                              height: 20,
                                              child: CircularProgressIndicator(
                                                strokeWidth: 2,
                                                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                              ),
                                            ),
                                            SizedBox(width: 12),
                                            Text(
                                              'Saving...',
                                              style: TextStyle(
                                                fontSize: 16,
                                                fontWeight: FontWeight.w600,
                                              ),
                                            ),
                                          ],
                                        )
                                      : Row(
                                          mainAxisAlignment: MainAxisAlignment.center,
                                          children: [
                                            Icon(
                                              todo == null ? Icons.add : Icons.save,
                                              size: 20,
                                            ),
                                            SizedBox(width: 8),
                                            Text(
                                              todo == null ? 'Add Todo' : 'Update Todo',
                                              style: TextStyle(
                                                fontSize: 16,
                                                fontWeight: FontWeight.bold,
                                              ),
                                            ),
                                          ],
                                        ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }
}




