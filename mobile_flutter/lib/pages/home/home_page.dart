import 'package:flutter/material.dart';
import 'package:police_traffic_assistant/pages/home/add_fine_page.dart';
import 'package:police_traffic_assistant/pages/home/violation_details_page.dart';
import '../../models/violation.dart';
import '../../services/api_service.dart';
import '../../services/secure_storage.dart';
import '../../widgets/violation_card.dart';
import '../profile/profile_page.dart';
import 'add_fine_page.dart';
import 'violation_details_page.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  late Future<List<Violation>> _violationsFuture;

  @override
  void initState() {
    super.initState();
    _loadViolations();
  }

  Future<void> _loadViolations() async {
    final token =
        await SecureStorage.readToken(); // قراءة التوكن من SecureStorage
    if (token == null) return;

    setState(() {
      _violationsFuture = ApiService.getViolations(token);
    });
  }

  Future<void> _refresh() async {
    await _loadViolations();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          IconButton(
            icon: const Icon(Icons.person, size: 28),
            onPressed: () async {
              // قراءة التوكن عند الضغط على أيقونة البروفايل
              final token = await SecureStorage.readToken();
              if (token != null) {
                Navigator.of(context).push(
                  MaterialPageRoute(
                    builder: (_) => ProfilePage(token: token), // تمرير التوكن
                  ),
                );
              } else {
                // إذا لم يكن هناك توكن، يمكن إظهار رسالة أو التعامل مع الحالة
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("No token found, please login")),
                );
              }
            },
          ),
        ],
      ),
      floatingActionButton: SizedBox(
        height: 62,
        width: 180,
        child: FloatingActionButton.extended(
          backgroundColor: Colors.blue,
          elevation: 6,
          icon: const Icon(Icons.add, size: 30),
          label: const Text(
            "Add Violation",
            style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold),
          ),
          onPressed: () async {
            final created = await Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const AddViolationPage()),
            );

            if (created == true) {
              _refresh();
            }
          },
        ),
      ),
      body: FutureBuilder<List<Violation>>(
        future: _violationsFuture,
        builder: (context, snapshot) {
          if (!snapshot.hasData)
            return const Center(child: CircularProgressIndicator());

          final list = snapshot.data!;
          if (list.isEmpty) {
            return const Center(
              child:
                  Text("No violations found", style: TextStyle(fontSize: 18)),
            );
          }

          return RefreshIndicator(
            onRefresh: _refresh,
            child: ListView.builder(
              padding: const EdgeInsets.only(bottom: 100),
              itemCount: list.length,
              itemBuilder: (_, i) {
                final v = list[i];
                return ViolationCard(
                  violation: v,
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => ViolationDetailsPage(violation: v),
                      ),
                    );
                  },
                );
              },
            ),
          );
        },
      ),
    );
  }
}
