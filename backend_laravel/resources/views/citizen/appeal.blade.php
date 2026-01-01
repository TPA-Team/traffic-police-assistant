<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Appeal</title>
  <link rel="stylesheet" href="{{ asset('citizen/style.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <div class="container glass">
    <h2>Violation Details</h2>
    <div id="violationDetails" class="result-card"></div>

    <h2 style="margin-top: 25px;">Submit an Appeal</h2>
    <form id="AppealForm">
      <input type="hidden" id="violation_id" name="violation_id">

      <label>Reason for Appeal:</label>
      <textarea id="reason" name="reason" required placeholder="Describe why you are objecting"></textarea>

      
      <button type="submit">Send Appeal</button>
    </form>

    <div id="responseMessage"></div>
  </div>

  <script src="{{ asset('citizen/script.js') }}"></script>
</body>
</html>
