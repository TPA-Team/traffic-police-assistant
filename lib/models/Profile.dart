class Profile {
  final int id;
  final String name;
  final String email;
  final String role;
  final bool isActive;
  final String? profileImage;

  Profile({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.isActive,
    this.profileImage,
  });

  factory Profile.fromJson(Map<String, dynamic> json) {
    return Profile(
      id: json["id"] != null ? json["id"] : 0,  // إذا كانت القيمة null، استخدم 0 كقيمة افتراضية
      name: json["name"] ?? 'N/A',  // إذا كانت القيمة null، استخدم 'N/A' كقيمة افتراضية
      email: json["email"] ?? 'N/A',  // نفس الشيء مع البريد الإلكتروني
      role: json["role"] ?? 'Unknown',  // إذا كانت القيمة null، استخدم 'Unknown' كقيمة افتراضية
      isActive: json["is_active"] != null ? json["is_active"] : false,  // إذا كانت القيمة null، استخدم false كقيمة افتراضية
      profileImage: json["profile_image"],  // إذا كانت الصورة null، ستظل null
    );
  }
}
