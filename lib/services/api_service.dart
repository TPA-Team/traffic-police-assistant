import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config.dart';
import '../models/violation.dart';
import '../models/profile.dart'; 
import 'dart:io';


class ApiService {
  // تسجيل الدخول
  static Future<Map<String, dynamic>> login(String email, String password) async {
    final url = Uri.parse("${Config.baseUrl}/login");
    final res = await http.post(
      url,
      headers: {"Content-Type": "application/json"},
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );
    return jsonDecode(res.body);
  }

  // جلب البروفايل
  static Future<Profile> getProfile(String token) async {
    final url = Uri.parse("${Config.baseUrl}/profile");
    final res = await http.get(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token",
      },
    );

    if (res.statusCode == 200) {
      final body = jsonDecode(res.body);

      // تحقق إذا كانت الحقول تحتوي على null
      final profileData = body['data'] ?? {}; // التأكد من أن `data` ليست null
      return Profile.fromJson({
        'id': profileData['id'] ?? 0,  // إذا كانت null، استخدم 0
        'name': profileData['name'] ?? 'N/A',  // إذا كانت null، استخدم 'N/A'
        'email': profileData['email'] ?? 'N/A',  // إذا كانت null، استخدم 'N/A'
        'role': profileData['role'] ?? 'Unknown',  // إذا كانت null، استخدم 'Unknown'
        'isActive': profileData['is_active'] ?? false,  // إذا كانت null، استخدم false
        'profile_image': profileData['profile_image'],  // إذا كانت null، سيكون null
      });
    } else {
      throw Exception("Failed to load profile");
    }
  }

  // جلب المخالفات
  static Future<List<Violation>> getViolations(String token) async {
    final url = Uri.parse("${Config.baseUrl}/violations");
    final res = await http.get(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token",
      },
    );

    if (res.statusCode != 200) {
      throw Exception('Failed to load violations (${res.statusCode})');
    }

    final body = jsonDecode(res.body);

    final List<dynamic> listJson = body['data'] ?? body;

    return listJson
        .map<Violation>((item) => Violation.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  // إنشاء مخالفة جديدة
  static Future<Map<String, dynamic>> createViolation(
      String token, Map<String, dynamic> data) async {
    final url = Uri.parse("${Config.baseUrl}/create");
    final res = await http.post(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token",
      },
      body: jsonEncode(data),
    );
    return jsonDecode(res.body);
  }

  // تسجيل الخروج
  static Future<Map<String, dynamic>> logout(String token) async {
    final url = Uri.parse("${Config.baseUrl}/logout");
    final res = await http.post(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token",
      },
    );
    return jsonDecode(res.body);
  }

  // جلب المدن
  static Future<List<dynamic>> getCities(String token) async {
    final url = Uri.parse("${Config.baseUrl}/cities");
    final res = await http.get(url, headers: {
      "Content-Type": "application/json",
      "Authorization": "Bearer $token",
    });
    final body = jsonDecode(res.body);
    if (body is List<dynamic>) {
      return body;
    } else {
      throw Exception("Unexpected response format: expected list of cities");
    }
  }

  // جلب أنواع المخالفات
  static Future<List<dynamic>> getViolationTypes(String token) async {
    final url = Uri.parse("${Config.baseUrl}/violation-types");
    final res = await http.get(url, headers: {
      "Content-Type": "application/json",
      "Authorization": "Bearer $token",
    });
    final body = jsonDecode(res.body);
    if (body is List<dynamic>) {
      return body;
    } else {
      throw Exception("Unexpected response format: expected list of violation types");
    }
  }

  // الرد على المخالفة
  static Future<http.Response> createViolationResponse(String token, Map<String, dynamic> data) async {
    final url = Uri.parse("${Config.baseUrl}/create");
    final res = await http.post(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token",
      },
      body: jsonEncode(data),
    );
    return res;
  }

    // ================= OCR =================

  // 1) إرسال صورة اللوحة -> يرجع job_id
  static Future<String> requestPlateOcr(String token, File imageFile) async {
    final url = Uri.parse("${Config.baseUrl}/ocr/plate");

    final req = http.MultipartRequest('POST', url);
    req.headers['Accept'] = 'application/json';
    req.headers['Authorization'] = 'Bearer $token';

    req.files.add(await http.MultipartFile.fromPath('image', imageFile.path));

    final streamed = await req.send();
    final body = await streamed.stream.bytesToString();

    if (streamed.statusCode != 200 && streamed.statusCode != 201) {
      throw Exception("OCR request failed (${streamed.statusCode}): $body");
    }

    final data = jsonDecode(body);
    final jobId = data['job_id']?.toString();

    if (jobId == null || jobId.isEmpty) {
      throw Exception("Missing job_id in response: $body");
    }

    return jobId;
  }

  // 2) جلب نتيجة OCR حسب job_id
  static Future<Map<String, dynamic>> getOcrResult(String token, String jobId) async {
    final url = Uri.parse("${Config.baseUrl}/ocr/result/$jobId");

    final res = await http.get(
      url,
      headers: {
        "Content-Type": "application/json",
        "Authorization": "Bearer $token",
        "Accept": "application/json",
      },
    );

    if (res.statusCode != 200) {
      throw Exception("Get OCR result failed (${res.statusCode}): ${res.body}");
    }

    return jsonDecode(res.body) as Map<String, dynamic>;
  }

  // 3) Polling لحد ما يصير success/failed
  static Future<String> pollPlateOcr(String token, String jobId,
      {int maxAttempts = 25, Duration delay = const Duration(seconds: 1)}) async {
    for (int i = 0; i < maxAttempts; i++) {
      final data = await getOcrResult(token, jobId);
      final status = (data['status'] ?? '').toString().toLowerCase();

      if (status == 'success') {
        final result = data['result'];

        // حسب الـ worker: result فيه plate_number
        if (result is Map && result['plate_number'] != null) {
          return result['plate_number'].toString();
        }

        // fallback لو رجعت plate مباشرة
        if (data['plate_number'] != null) {
          return data['plate_number'].toString();
        }

        return '';
      }

      if (status == 'failed') {
        final err = data['error']?.toString() ?? 'OCR failed';
        throw Exception(err);
      }

      await Future.delayed(delay);
    }

    throw Exception("OCR timed out (still queued).");
  }

  // 4) Convenience: upload + poll وترجع plate مباشرة
  static Future<String> readPlateFromImage(String token, File imageFile) async {
    final jobId = await requestPlateOcr(token, imageFile);
    return await pollPlateOcr(token, jobId);
  }

}
