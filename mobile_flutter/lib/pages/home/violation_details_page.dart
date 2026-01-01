import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/violation.dart';
class ViolationDetailsPage extends StatelessWidget {
  final Violation violation;

  const ViolationDetailsPage({super.key, required this.violation});

  String _format(dynamic value) {
    try {
      if (value is DateTime) {
        return DateFormat('yyyy-MM-dd – HH:mm').format(value);
      }

      final dt = DateTime.tryParse(value.toString());
      if (dt != null) {
        return DateFormat('yyyy-MM-dd – HH:mm').format(dt);
      }

      return value.toString();
    } catch (_) {
      return value.toString();
    }
  }

  Widget infoRow(IconData icon, String label, dynamic value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        children: [
          Icon(icon, color: Colors.blueAccent, size: 22),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label,
                    style: const TextStyle(fontSize: 12, color: Colors.grey)),
                Text(
                  value?.toString() ?? "—",
                  style: const TextStyle(fontSize: 16, color: Colors.white),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final snap = violation.vehicleSnapshot;
    final loc = violation.location;

    return Scaffold(
      backgroundColor: const Color(0xFF050814),
      appBar: AppBar(
        title: const Text("Violation Details"),
        backgroundColor: const Color(0xFF050814),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(22),
        child: Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: const Color(0xFF101424),
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: Colors.white10),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // -------- Title ----------
              Row(
                children: [
                  const Icon(Icons.local_police, color: Colors.amber, size: 30),
                  const SizedBox(width: 12),
                  Text(
                    snap?['plate_number']?.toString() ?? "No Plate",
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                    ),
                  )
                ],
              ),

              const SizedBox(height: 20),
              const Divider(color: Colors.white12),

              // --------- Vehicle Info ----------
              Text("Vehicle Information",
                  style: TextStyle(
                      fontSize: 14,
                      color: Colors.blue.shade200,
                      fontWeight: FontWeight.bold)),
              const SizedBox(height: 10),

              infoRow(Icons.directions_car, "Plate", snap?['plate_number']),
              infoRow(Icons.person, "Owner", snap?['owner_name']),
            
              const SizedBox(height: 18),
              const Divider(color: Colors.white12),

              // ---------- Location ----------
              Text("Location",
                  style: TextStyle(
                      fontSize: 14,
                      color: Colors.blue.shade200,
                      fontWeight: FontWeight.bold)),
              const SizedBox(height: 10),

              infoRow(Icons.location_city, "City", loc?['city']?['name']),
              infoRow(Icons.map, "Street", loc?['street_name']),
              infoRow(Icons.place, "Landmark", loc?['landmark']),

              const SizedBox(height: 18),
              const Divider(color: Colors.white12),

              // ---------- Violation Info ----------
              Text("Violation Details",
                  style: TextStyle(
                      fontSize: 14,
                      color: Colors.blue.shade200,
                      fontWeight: FontWeight.bold)),
              const SizedBox(height: 10),

              infoRow(Icons.warning, "Type", violation.violationType?['name']),
              infoRow(Icons.money, "Fine Amount", violation.fineAmount?.toString() ?? "—"),
              infoRow(Icons.description, "Description", violation.description),
              infoRow(
                  Icons.calendar_today, "Date", _format(violation.occurredAt)),
            ],
          ),
        ),
      ),
    );
  }
}
