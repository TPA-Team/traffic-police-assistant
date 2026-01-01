class Violation {
  final int id;
  final Map<String, dynamic>? vehicle;
  final Map<String, dynamic>? location;
  final Map<String, dynamic>? violationType;
  final Map<String, dynamic>? vehicleSnapshot;
  final String? description;
  final String occurredAt;
  final dynamic fineAmount; // <-- تمت إضافته

  Violation({
    required this.id,
    required this.vehicle,
    required this.location,
    required this.violationType,
    required this.vehicleSnapshot,
    required this.description,
    required this.occurredAt,
    this.fineAmount, // <-- تمت إضافته
  });

  factory Violation.fromJson(Map<String, dynamic> json) {
    return Violation(
      id: json['id'] ?? 0,
      vehicle: json['vehicle'],
      location: json['location'],
      violationType: json['violation_type'],
      vehicleSnapshot: json['vehicle_snapshot'],
      description: json['description']?.toString(),
      occurredAt: json['occurred_at']?.toString() ?? '',
      fineAmount: json['fine_amount'], // <-- تمت إضافته
    );
  }
}
