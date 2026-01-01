// lib/widgets/violation_card.dart
import 'package:flutter/material.dart';
import '../models/violation.dart';
import 'package:intl/intl.dart';

class ViolationCard extends StatelessWidget {
  final Violation violation;
  final VoidCallback? onTap;

  const ViolationCard({
    super.key,
    required this.violation,
    this.onTap,
  });

  String _formatDate(dynamic value) {
    try {
      final dt = DateTime.parse(value.toString());
      return DateFormat('yyyy-MM-dd – HH:mm').format(dt);
    } catch (_) {
      return value.toString();
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    final veh = violation.vehicle;
    final loc = violation.location;

    final id = violation.id.toString();

    final plate =
        violation.vehicleSnapshot?['plate_number']?.toString() ?? 'No Plate';

    final typeName = violation.violationType?['name']?.toString() ?? 'Unknown';

    final city = loc?['city']?['name']?.toString() ?? '';
    final street = loc?['street_name']?.toString() ?? '';
    final place = '$city, $street';

    return Card(
      margin: const EdgeInsets.symmetric(vertical: 6, horizontal: 12),
      child: ListTile(
        onTap: onTap,
        leading: CircleAvatar(
          radius: 22,
          backgroundColor: scheme.primary.withOpacity(0.2),
          child: Icon(Icons.local_police, color: scheme.secondary),
        ),
        title: Text(
          plate,
          style: const TextStyle(
            fontWeight: FontWeight.w700,
            letterSpacing: 1.2,
          ),
        ),
        subtitle: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 4),
            Text('Type: $typeName'),
            Text('Place: $place'),
            Text(
              _formatDate(violation.occurredAt),
              style: TextStyle(fontSize: 12, color: Colors.grey.shade500),
            ),
          ],
        ),
        trailing: const Icon(Icons.chevron_right),
      ),
    );
  }
}
