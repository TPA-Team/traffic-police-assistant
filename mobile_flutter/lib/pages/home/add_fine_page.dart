import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../services/secure_storage.dart';

import 'dart:io';
import 'package:image_picker/image_picker.dart';

class AddViolationPage extends StatefulWidget {
  const AddViolationPage({super.key});

  @override
  State<AddViolationPage> createState() => _AddViolationPageState();
}

class _AddViolationPageState extends State<AddViolationPage> {
  final _formKey = GlobalKey<FormState>();
  final plateController = TextEditingController();
  final ownerController = TextEditingController();
  final streetController = TextEditingController();
  final landmarkController = TextEditingController();
  final descriptionController = TextEditingController();

  String? selectedCityId;
  String? selectedViolationTypeId;

  List<Map<String, dynamic>> cities = [];
  List<Map<String, dynamic>> violationTypes = [];

  bool _loading = false;

  // 🔹 OCR variables (إضافة فقط)
  File? _selectedImage;
  final ImagePicker _picker = ImagePicker();
  bool _ocrLoading = false;

  @override
  void initState() {
    super.initState();
    _loadLookups();
  }

  @override
  void dispose() {
    plateController.dispose();
    ownerController.dispose();
    streetController.dispose();
    landmarkController.dispose();
    descriptionController.dispose();
    super.dispose();
  }

  Future<void> _loadLookups() async {
    final token = await SecureStorage.readToken();
    if (token == null) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Please login first.')));
      return;
    }
    try {
      final c = await ApiService.getCities(token);
      final t = await ApiService.getViolationTypes(token);
      setState(() {
        cities = c.map((e) => e as Map<String, dynamic>).toList();
        violationTypes = t.map((e) => e as Map<String, dynamic>).toList();

        if (cities.isNotEmpty) {
          selectedCityId = cities[0]['id'].toString();
        }
        if (violationTypes.isNotEmpty) {
          selectedViolationTypeId = violationTypes[0]['id'].toString();
        }
      });
    } catch (e) {
      ScaffoldMessenger.of(context)
          .showSnackBar(SnackBar(content: Text('Error loading lookups: $e')));
    }
  }



Future<void> _pickImage() async {
  final XFile? pickedFile =
      await _picker.pickImage(source: ImageSource.gallery);

  if (pickedFile == null) return;

  setState(() {
    _selectedImage = File(pickedFile.path);
    _ocrLoading = true;
  });

  final token = await SecureStorage.readToken();
  if (token == null) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Please login first.')),
    );
    setState(() => _ocrLoading = false);
    return;
  }

  try {
    final plate = await ApiService.readPlateFromImage(token, _selectedImage!);

    if (plate.isEmpty) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Plate unreadable, try a clearer image.')),
      );
    } else {
      setState(() {
        plateController.text = plate.toUpperCase();
      });
    }
  } catch (e) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('OCR error: $e')),
    );
  } finally {
    if (mounted) setState(() => _ocrLoading = false);
  }
}


  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    final token = await SecureStorage.readToken();
    if (token == null) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Please login first.')));
      return;
    }

    setState(() {
      _loading = true;
    });

    final body = {
      'vehicle_plate': plateController.text.trim().toUpperCase(),
      'vehicle_owner': ownerController.text.trim().isEmpty
          ? null
          : ownerController.text.trim(),
      'city_id': selectedCityId,
      'street_name': streetController.text.trim(),
      'landmark': landmarkController.text.trim().isEmpty
          ? null
          : landmarkController.text.trim(),
      'violation_type_id': selectedViolationTypeId,
      'description': descriptionController.text.trim().isEmpty
          ? null
          : descriptionController.text.trim(),
      'occurred_at': DateTime.now().toIso8601String(),
    };

    try {
      final res = await ApiService.createViolation(token, body);
      if ((res['status_code'] == 200) ||
          (res['status'] != null &&
              res['status'].toString().toLowerCase() == 'success')) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Violation created successfully')),
        );
        Navigator.of(context).pop(true);
      } else {
        final msg = res['message'] ?? 'Unknown error';
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $msg')),
        );
      }
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Request error: $e')),
      );
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Register Violation')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              ElevatedButton(
                onPressed: _ocrLoading ? null : _pickImage,
                child: _ocrLoading
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    : const Text('Pick Plate Image'),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: plateController,
                textCapitalization: TextCapitalization.characters,
                decoration: const InputDecoration(
                  labelText: 'Car Plate',
                  prefixIcon: Icon(Icons.directions_car),
                ),
                validator: (v) =>
                    v == null || v.isEmpty ? 'Enter car plate' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: ownerController,
                decoration: const InputDecoration(
                  labelText: 'Car Owner (optional)',
                  prefixIcon: Icon(Icons.person),
                ),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<String>(
                value: selectedCityId,
                decoration: const InputDecoration(
                  labelText: 'City',
                  prefixIcon: Icon(Icons.location_city),
                ),
                items: cities
                    .map((c) => DropdownMenuItem<String>(
                          value: c['id'].toString(),
                          child: Text(c['name']?.toString() ?? ''),
                        ))
                    .toList(),
                onChanged: (v) => setState(() => selectedCityId = v),
                validator: (v) =>
                    v == null || v.isEmpty ? 'Select a city' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: streetController,
                decoration: const InputDecoration(
                  labelText: 'Street Name',
                  prefixIcon: Icon(Icons.map),
                ),
                validator: (v) =>
                    v == null || v.isEmpty ? 'Enter street name' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: landmarkController,
                decoration: const InputDecoration(
                  labelText: 'Landmark (optional)',
                  prefixIcon: Icon(Icons.place_outlined),
                ),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<String>(
                value: selectedViolationTypeId,
                decoration: const InputDecoration(
                  labelText: 'Violation Type',
                  prefixIcon: Icon(Icons.warning_amber_rounded),
                ),
                items: violationTypes
                    .map((t) => DropdownMenuItem<String>(
                          value: t['id'].toString(),
                          child: Text(t['name']?.toString() ?? ''),
                        ))
                    .toList(),
                onChanged: (v) => setState(() => selectedViolationTypeId = v),
                validator: (v) =>
                    v == null || v.isEmpty ? 'Select violation type' : null,
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: descriptionController,
                decoration: const InputDecoration(
                  labelText: 'Description (optional)',
                  prefixIcon: Icon(Icons.notes),
                ),
                maxLines: 2,
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _loading ? null : _submit,
                  child: _loading
                      ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            color: Colors.white,
                          ),
                        )
                      : const Text('Submit'),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
