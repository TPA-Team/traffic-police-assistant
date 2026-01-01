function searchViolations() {
    const plate = document.getElementById("plateInput").value;
    const results = document.getElementById("results");

    if (plate.trim() === "") {
        results.innerHTML = "<p>Please enter a plate number.</p>";
        return;
    }

    // ============================
    // MOCK DATA (بيانات تجريبية)
    // ============================
    const fakeData = [
        {
            id: 1,
            plate: plate,
            category: "Speed Violation",          // ← نوع المخالفة الرئيسي
            type: "Overspeed (110 km/h in 80 km/h zone)", // ← تفاصيل المخالفة
            car_type: "Kia Rio",
            officer_name: "Officer Ahmad",
            location: "City Center",
            time: "2024-11-25 14:30"
        },
        {
            id: 2,
            plate: plate,
            category: "Parking Violation",
            type: "Wrong Parking - Blocking Road",
            car_type: "Kia Rio",
            officer_name: "Officer Samer",
            location: "Mall Road",
            time: "2024-11-20 09:10"
        }
    ];

    let html = `<p>You have <strong>${fakeData.length}</strong> violations:</p>`;

    fakeData.forEach(v => {
        html += `
            <div class="result-card">
                <p><strong>Violation Category:</strong> ${v.category}</p>
                <p><strong>Violation Type:</strong> ${v.type}</p>
                <p><strong>Plate Number:</strong> ${v.plate}</p>
                <p><strong>Car Type:</strong> ${v.car_type}</p>
                <p><strong>Officer:</strong> ${v.officer_name}</p>
                <p><strong>Location:</strong> ${v.location}</p>
                <p><strong>Date:</strong> ${v.time}</p>

                <button onclick="goToObjection(${v.id})">Submit Objection</button>
            </div>
        `;
    });

    results.innerHTML = html;
}

function goToObjection(id) {
    localStorage.setItem("violation_id", id);
    window.location.href = "/citizen/objection";
}
// ============================
// LOAD VIOLATION ON OBJECTION PAGE
// ============================
document.addEventListener("DOMContentLoaded", () => {
    const violationId = localStorage.getItem("violation_id");

    if (violationId && window.location.pathname.includes("objection")) {

        // Fake violation data (same mock data as search)
        const fakeData = [
            {
                id: 1,
                plate: "201045",
                category: "Speed Violation",
                type: "Overspeed (110 km/h in 80 km/h zone)",
                car_type: "Kia Rio",
                officer_name: "Officer Ahmad",
                location: "City Center",
                time: "2024-11-25 14:30"
            },
            {
                id: 2,
                plate: "201045",
                category: "Parking Violation",
                type: "Wrong Parking - Blocking Road",
                car_type: "Kia Rio",
                officer_name: "Officer Samer",
                location: "Mall Road",
                time: "2024-11-20 09:10"
            }
        ];

        // Find the selected violation
        const selected = fakeData.find(v => v.id == violationId);

        if (selected) {
            document.getElementById("violationDetails").innerHTML = `
                <p><strong>Violation Category:</strong> ${selected.category}</p>
                <p><strong>Violation Type:</strong> ${selected.type}</p>
                <p><strong>Plate Number:</strong> ${selected.plate}</p>
                <p><strong>Car Type:</strong> ${selected.car_type}</p>
                <p><strong>Officer:</strong> ${selected.officer_name}</p>
                <p><strong>Location:</strong> ${selected.location}</p>
                <p><strong>Date:</strong> ${selected.time}</p>
            `;

            document.getElementById("violationId").value = selected.id;
        }
    }
});