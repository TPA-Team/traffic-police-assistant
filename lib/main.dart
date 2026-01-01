import 'package:flutter/material.dart';
import 'core/police_theme.dart';
import 'pages/auth/login_page.dart';
import 'pages/profile/profile_page.dart';  // تأكد من إضافة هذه الصفحة أو أي صفحة أخرى تحتاجها

void main() {
  runApp(const PoliceAssistantApp());
}

class PoliceAssistantApp extends StatelessWidget {
  const PoliceAssistantApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Police Assistant',
      debugShowCheckedModeBanner: false,
      theme: PoliceTheme.theme,
      initialRoute: '/login',  // تعيين الصفحة الأولية عند بدء التطبيق
      routes: {
        '/login': (context) => const LoginPage(),  // المسار لصفحة تسجيل الدخول
        '/profile': (context) => ProfilePage(token: 'your_token_here'),  // المسار لصفحة البروفايل
      },
      onUnknownRoute: (settings) {
        return MaterialPageRoute(builder: (context) => NotFoundPage()); // صفحة غير موجودة
      },
    );
  }
}

class NotFoundPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Page Not Found')),
      body: Center(child: const Text('This page does not exist.')),
    );
  }
}
