async function searchViolations() {
  const plate = document.getElementById("plateInput").value.trim();
  const results = document.getElementById("results");
  if (!plate) {
    results.innerHTML = "<p>Please enter a plate number.</p>";
    return;
  }

  try {
    const res = await fetch(`/citizen/violations?plate=${encodeURIComponent(plate)}`, {
      headers: { "Accept": "application/json" }
    });
    const data = await res.json();
    console.log("Received violations data:", data);

    const violations = data.data ?? [];
    if (violations.length === 0) {
      results.innerHTML = "<p>No violations found.</p>";
      return;
    }

    let html = `<p>You have <strong>${violations.length}</strong> violations:</p>`;

    violations.forEach(v => {
      console.log("Violation:", v);
      html += `<div class="result-card">` +
              `<p><strong>Violation Type:</strong> ${v.violation_type?.name ?? "-"}</p>` +
              `<p><strong>Plate Number:</strong> ${v.vehicle_snapshot?.plate_number ?? v.vehicle?.plate_number ?? "-"}</p>` +
              `<p><strong>Location:</strong> ${v.violation_location?.street_name ?? "-"}</p>` +
              `<p><strong>Date:</strong> ${v.occurred_at ?? "-"}</p>`;

      if (v.appeal) {
        console.log("Has appeal:", v.appeal);
        html += `<div class="appeal-info" style="margin-top:10px;padding:10px;background:#f5f5f5;border:1px solid #ccc;">` +
                `<p><strong>Appeal Status:</strong> ${v.appeal.status}</p>` +
                `<p><strong>Reason:</strong> ${v.appeal.reason}</p>` +
                `<p><strong>Decision Note:</strong> ${v.appeal.decision_note ?? '-'}</p>` +
                `<p><small>Submitted: ${v.appeal.created_at}</small></p>` +
                `</div>`;
      } else {
        html += `<button class="Appeal-btn" data-id="${v.id}">Submit Appeal</button>`;
      }

      html += `</div>`;
    });

    results.innerHTML = html;
    console.log("Rendered HTML:", html);

    document.querySelectorAll(".Appeal-btn").forEach(btn => {
      btn.addEventListener("click", e => {
        const id = e.currentTarget.getAttribute("data-id");
        if (!id) return;
        localStorage.setItem("violation_id", id);
        window.location.href = "/citizen/appeal-form";
      });
    });

  } catch (err) {
    console.error("Error fetching violations:", err);
    results.innerHTML = "<p>Error loading violations.</p>";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.goToAppeal = function(id) {
    if (!id) return;
    localStorage.setItem("violation_id", id);
    window.location.href = "/citizen/appeal-form";
  };

  const searchBtn = document.getElementById("searchBtn");
  if (searchBtn) {
    searchBtn.addEventListener("click", async () => {
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
          html += `
            <div class="result-card">
              <p><strong>Violation Type:</strong> ${v.violation_type?.name ?? "-"}</p>
              <p><strong>Plate Number:</strong> ${v.vehicle_snapshot?.plate_number ?? v.vehicle?.plate_number ?? "-"}</p>
              <p><strong>Location:</strong> ${v.violation_location?.street_name ?? v.location?.street_name ?? "-"}</p>
              <p><strong>Date:</strong> ${v.occurred_at ?? "-"}</p>
              <button class="Appeal-btn" data-id="${v.id}">Submit Appeal</button>
            </div>
          `;
        });
        results.innerHTML = html;

        document.querySelectorAll(".Appeal-btn").forEach(btn => {
          btn.addEventListener("click", (e) => {
            const id = e.currentTarget.getAttribute("data-id");
            window.goToAppeal(id);
          });
        });

      } catch (err) {
        console.error("Fetch error:", err);
        results.innerHTML = "<p>Error loading violations.</p>";
      }
    });
  }

  const form = document.getElementById("AppealForm");
  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const violationId = localStorage.getItem("violation_id");
      const reason = document.getElementById("reason")?.value;

      if (!violationId || !reason) {
        alert("Violation ID or reason is missing");
        return;
      }

      const formData = new FormData();
      formData.append("violation_id", parseInt(violationId, 10));
      formData.append("reason", reason);

      const files = document.getElementById("media")?.files;
      if (files) {
        for (let i = 0; i < files.length; i++) {
          formData.append("media[]", files[i]);
        }
      }

      try {
  const res = await fetch("/citizen/appeals", {
    method: "POST",
    headers: {
      "Accept": "application/json",
      "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
    },
    body: formData
  });

  // always read the response body (as text), even on error
  const contentType = res.headers.get("content-type") || "";
  const text = await res.text();

  if (!res.ok) {
    // هناك خطأ (status غير 2xx) — حاول استخراج رسالة من JSON أو استخدم النص الخام
    let errMsg = text;
    if (contentType.includes("application/json")) {
      try {
        const errJson = JSON.parse(text);
        if (errJson.message) {
          errMsg = errJson.message;
        } else {
          // إذا تود عرض كل الأخطاء
          errMsg = JSON.stringify(errJson);
        }
      } catch (parseErr) {
        // JSON غير صحيح — خليه كما هو
      }
    }
    document.getElementById("responseMessage").innerHTML =
      `<p style="color: red;">${errMsg}</p>`;
    return;
  }

  // إذا OK — parse JSON ونفّذ
  if (!contentType.includes("application/json")) {
    // المفروض JSON، لكن غير — هذا غير متوقع
    console.error("Expected JSON response, got:", text);
    document.getElementById("responseMessage").innerHTML =
      `<p style="color: red;">Unexpected server response.</p>`;
    return;
  }

  const data = JSON.parse(text);
  if (data.success) {
    document.getElementById("responseMessage").innerHTML =
      `<p style="color: lightgreen;">Appeal submitted! (ID: ${data.appeal_id})</p>`;
  } else {
    // حالة success = false
    const msg = data.message || "Error submitting Appeal";
    document.getElementById("responseMessage").innerHTML =
      `<p style="color: red;">${msg}</p>`;
  }

} catch (err) {
  console.error("Submit Appeal error:", err);
  document.getElementById("responseMessage").innerHTML =
    `<p style="color: red;">Error submitting Appeal</p>`;
}

    });
  }
});
