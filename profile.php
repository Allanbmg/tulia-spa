<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Client Profile – Tulia Wellness Spa</title>

  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: url('https://i.pinimg.com/736x/3d/08/fd/3d08fd7ed897828f1bce2623fcd2c504.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      line-height: 1.6;
    }

    header {
      text-align: center;
      padding: 20px;
      color: #fff;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
    }

    .container {
      width: 90%;
      max-width: 1000px;
      margin: 40px auto;
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      color: #fff;
    }

    h2 {
      color: #ffeb99;
      margin-top: 0;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.5);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      background: rgba(0,0,0,0.25);
    }

    th, td {
      border: 1px solid rgba(255,255,255,0.5);
      padding: 12px;
      text-align: left;
    }

    th {
      background: rgba(255,184,71,0.5);
    }

    button {
      padding: 8px 12px;
      margin: 3px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .edit-btn {
      background: #ff8c00;
      color: white;
    }

    .edit-btn:hover {
      background: #e27700;
    }

    .cancel-btn {
      background: #d9534f;
      color: white;
    }

    .cancel-btn:hover {
      background: #b52b24;
    }

    /* Edit form modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.7);
    }

    .modal-content {
      background: rgba(255,255,255,0.25);
      backdrop-filter: blur(15px);
      margin: 10% auto;
      padding: 20px;
      border-radius: 12px;
      width: 90%;
      max-width: 400px;
      color: #fff;
    }

    input, select, button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid rgba(255,255,255,0.5);
      font-size: 16px;
      background: rgba(255,255,255,0.15);
      color: #fff;
    }

    button[type="submit"] {
      background: #28a745;
      color: white;
      margin-top: 5px;
    }

    button[type="submit"]:hover {
      background: #218838;
    }

    @media (max-width: 600px) {
      .container { padding: 20px; }
      .modal-content { margin-top: 30%; }
    }
  </style>
</head>

<body>

  <header>
    <h1>My Profile</h1>
    <p>Tulia Wellness Spa – Your Booked Services</p>
  </header>

  <div class="container">

    <h2>Client Information</h2>
    <p><strong>Name:</strong> Jane Doe</p>
    <p><strong>Email:</strong> janedoe@example.com</p>
    <p><strong>Phone:</strong> +254 700 000 000</p>

    <h2>My Booked Services</h2>
    <table id="appointmentsTable">
      <thead>
        <tr>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Appointments populated by JS -->
      </tbody>
    </table>
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <h3>Edit Appointment</h3>
      <form id="editForm">
        <label>Service</label>
        <select id="editService" required>
          <option value="">Choose...</option>
          <option value="Facial Massage">Facial Massage</option>
          <option value="Full Body Massage">Full Body Massage</option>
          <option value="Sauna & Steam">Sauna & Steam</option>
          <option value="Manicure & Pedicure">Manicure & Pedicure</option>
          <option value="Body Scrub">Body Scrub</option>
          <option value="Aromatherapy">Aromatherapy</option>
        </select>

        <label>Date</label>
        <input type="date" id="editDate" required />

        <label>Time</label>
        <input type="time" id="editTime" required />

        <button type="submit">Save Changes</button>
        <button type="button" onclick="closeModal()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    // Mock appointments data
    let appointments = [
      {id:1, service:"Facial Massage", date:"2025-11-25", time:"10:00"},
      {id:2, service:"Manicure & Pedicure", date:"2025-11-27", time:"14:00"}
    ];

    const tableBody = document.querySelector("#appointmentsTable tbody");

    function renderAppointments() {
      tableBody.innerHTML = "";
      appointments.forEach(app => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td>${app.service}</td>
          <td>${app.date}</td>
          <td>${app.time}</td>
          <td>
            <button class="edit-btn" onclick="editAppointment(${app.id})">Edit</button>
            <button class="cancel-btn" onclick="cancelAppointment(${app.id})">Cancel</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }

    function editAppointment(id) {
      const app = appointments.find(a => a.id === id);
      document.getElementById("editService").value = app.service;
      document.getElementById("editDate").value = app.date;
      document.getElementById("editTime").value = app.time;
      document.getElementById("editForm").dataset.id = id;
      document.getElementById("editModal").style.display = "block";
    }

    function closeModal() {
      document.getElementById("editModal").style.display = "none";
    }

    document.getElementById("editForm").addEventListener("submit", function(e){
      e.preventDefault();
      const id = parseInt(this.dataset.id);
      const index = appointments.findIndex(a => a.id === id);
      appointments[index].service = document.getElementById("editService").value;
      appointments[index].date = document.getElementById("editDate").value;
      appointments[index].time = document.getElementById("editTime").value;
      renderAppointments();
      closeModal();
    });

    function cancelAppointment(id) {
      if(confirm("Are you sure you want to cancel this appointment?")) {
        appointments = appointments.filter(a => a.id !== id);
        renderAppointments();
      }
    }

    renderAppointments();
  </script>

</body>
</html>
