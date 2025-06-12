function submitApproval(appID) {
  fetch("supervisor_php/approveApplication.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ appID: appID })
  })
  .then(response => response.text()) // Read raw text instead of JSON first
  .then(text => {
    console.log("Raw response:", text); // Debug raw output

    try {
      const data = JSON.parse(text); // Try parsing JSON manually

      if (data.success) {
        alert("Application approved and supervisor assigned.");
        window.location.href = "comApplication.php";
      } else {
        alert("Error: " + data.message);
      }
    } catch (e) {
      console.error("Invalid JSON returned:", e);
      alert("Unexpected server response. See console for details.");
    }
  })
  .catch(error => {
    console.error("Fetch error:", error);
    alert("Something went wrong while approving the application.");
  });
}
