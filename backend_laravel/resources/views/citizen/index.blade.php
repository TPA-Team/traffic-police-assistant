<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search Traffic Violations</title>
  <link rel="stylesheet" href="{{ asset('citizen/style.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<div class="container glass">
  <h2>Search for Traffic Violations</h2>

  <div class="search-box">
    <input type="text" id="plateInput" placeholder="Enter vehicle plate number">
    <button id="searchBtn">Search</button>
  </div>

  <div id="results"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

  async function searchViolations() {
    const plateInput = document.getElementById("plateInput");
    const results = document.getElementById("results");
    const plate = plateInput.value.trim();

    if (!plate) {
      results.innerHTML = "<p>Please enter a plate number.</p>";
      return;
    }

    try {
      const res = await fetch(`/citizen/violations?plate=${encodeURIComponent(plate)}`, {
        headers: { "Accept": "application/json" }
      });
      if (!res.ok) throw new Error("Network response was not ok: " + res.status);
      const data = await res.json();
      const violations = data.data ?? data;

      if (!violations || violations.length === 0) {
        results.innerHTML = "<p>No violations found.</p>";
        return;
      }

      let html = `<p>You have <strong>${violations.length}</strong> violations:</p>`;

      violations.forEach(v => {
        html += `<div class="result-card">`;
        html += `<p><strong>Violation Type:</strong> ${v.violation_type?.name ?? "-"}</p>`;
        html += `<p><strong>Fine amount:</strong> ${v.violation_type?.fine_amount ?? "-"}</p>`;
        html += `<p><strong>Plate Number:</strong> ${v.vehicle_snapshot?.plate_number ?? v.vehicle?.plate_number ?? "-"}</p>`;
        html += `<p><strong>City:</strong> ${v.violation_location?.city?.name ?? "-"}</p>`;
        html += `<p><strong>street:</strong> ${v.violation_location?.street_name ?? v.location?.street_name ?? "-"}</p>`;
        html += `<p><strong>Description:</strong> ${v.description ?? "-"}</p>`;
        html += `<p><strong>Date:</strong> ${v.occurred_at ?? "-"}</p>`;

        if (v.appeal) {
          // إذا هناك اعتراض سابق — عرض تفاصيل الاعتراض
          html += `<div class="appeal-info" style="margin-top: 10px; padding: 10px; ; border: 1px solid #ccc;">`;
          html += `<p><strong>Appeal Status:</strong> ${v.appeal.status}</p>`;
          html += `<p><strong>Reason:</strong> ${v.appeal.reason}</p>`;
          html += `<p><strong>Decision Note:</strong> ${v.appeal.decision_note ?? '-'}</p>`;
          html += `<p><small>Submitted: ${v.appeal.created_at}</small></p>`;
          html += `</div>`;
        } else {
          // لا اعتراض — عرض زر اعتراض
          html += `<button class="Appeal-btn" data-id="${v.id}">Submit Appeal</button>`;
        }

        html += `</div>`; // end result-card
      });

      results.innerHTML = html;

      // ربط أزرار الاعتراض
      document.querySelectorAll(".Appeal-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
          const id = e.currentTarget.getAttribute("data-id");
          if (!id) return;
          localStorage.setItem("violation_id", id);
          window.location.href = "/citizen/appeal-form";
        });
      });

    } catch (err) {
      console.error("Fetch error:", err);
      results.innerHTML = "<p>Error loading violations.</p>";
    }
  }

  const searchBtn = document.getElementById("searchBtn");
  if (searchBtn) {
    searchBtn.addEventListener("click", searchViolations);
  }

});
</script>

</body>
</html>
