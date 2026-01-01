import 'package:flutter/material.dart';
import 'package:police_traffic_assistant/config.dart';
import '../../services/api_service.dart';
import '../../models/profile.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;



class ProfilePage extends StatefulWidget {
  final String token; 

  const ProfilePage({super.key, required this.token});

  @override
  State<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  final nameController = TextEditingController();
  final emailController = TextEditingController();

  late Future<Profile> profileFuture;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  Future<void> _loadProfile() async {
    profileFuture = ApiService.getProfile(widget.token); // استخدام التوكن الممرر للـ API

    profileFuture.then((profile) {
      // قم بتحديث الحقول بعد تحميل البيانات
      nameController.text = profile.name ?? 'N/A';
      emailController.text = profile.email ?? 'N/A';

      // تحقق من البيانات التي تم تحميلها
      print("Loaded profile data: $profile");

      setState(() {}); // تأكد من تحديث واجهة المستخدم
    }).catchError((error) {
      print("Error loading profile: $error");
    });
  }

  Future<void> _logout() async {
    try {
      final url = Uri.parse("${Config.baseUrl}/logout");
      final res = await http.post(
        url,
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer ${widget.token}",
        },
      );

      // تحقق من أن الاستجابة هي JSON وليست HTML
      if (res.statusCode == 200) {
        // إذا كانت الاستجابة سليمة
        final response = jsonDecode(res.body);
        print("Logout response: $response");

        // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
        if (mounted) {
          Navigator.pushReplacementNamed(context, '/login');
        }
      } else {
        // في حال كان الكود غير 200
        print("Failed to logout, status code: ${res.statusCode}");
        print("Response body: ${res.body}");
      }
    } catch (e) {
      print("Error during logout: $e");
    }
  }

  @override
  void dispose() {
    nameController.dispose();
    emailController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: FutureBuilder<Profile>(
        future: profileFuture,
        builder: (context, snapshot) {
          if (!snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }

          // تحقق من البيانات التي تم تحميلها عند بناء الـ FutureBuilder
          print("Profile Data from FutureBuilder: ${snapshot.data}");

          return CustomScrollView(
            slivers: [
              SliverAppBar(
                expandedHeight: 190,
                pinned: true,
                flexibleSpace: FlexibleSpaceBar(
                  background: Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Color(0xFF0D47A1), Color(0xFF000000)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                    child: Align(
                      alignment: Alignment.bottomLeft,
                      child: Padding(
                        padding: const EdgeInsets.only(
                          left: 24,
                          bottom: 24,
                          right: 24,
                        ),
                        child: Row(
                          children: [
                            CircleAvatar(
                              radius: 32,
                              backgroundColor: Colors.white10,
                              child: Icon(
                                Icons.person,
                                size: 32,
                                color: Colors.white,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Text(
                                snapshot.data!.name, // عرض البيانات هنا
                                style: const TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w700,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    children: [
                      TextField(
                        controller: nameController,
                        decoration: const InputDecoration(
                          labelText: 'Full Name',
                          prefixIcon: Icon(Icons.person_outline),
                        ),
                      ),
                      const SizedBox(height: 16),
                      TextField(
                        controller: emailController,
                        decoration: const InputDecoration(
                          labelText: 'Email',
                          prefixIcon: Icon(Icons.email_outlined),
                        ),
                      ),
                      const SizedBox(height: 24),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.save_outlined),
                          label: const Text('Update Information'),
                          onPressed: () {
                            // مكان لتحديث البيانات إذا لزم الأمر
                          },
                        ),
                      ),
                      const SizedBox(height: 24),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          icon: const Icon(Icons.logout_outlined),
                          label: const Text('Logout'),
                          onPressed: _logout,  // عند الضغط على الزر، سيتم تنفيذ دالة `_logout`
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}
