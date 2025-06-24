// submitITPApproval.js
function submitITPApproval(appID) {
  fetch("./supervisor_php/approveITPApplication.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ appID: appID })
  })
  .then(response => response.text())
  .then(text => {
    console.log("Raw response:", text);
    try {
      const data = JSON.parse(text);
      if (data.success) {
        alert("ITP application approved.");
        window.location.href = "comITPApplication.php";
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
    alert("Something went wrong while approving the ITP application.");
  });
}
