function openDenyPopup() {
  document.getElementById("denyPopup").style.display = "flex";
}

function closeDenyPopup() {
  document.getElementById("denyPopup").style.display = "none";
}

function submitITPDenial() {
  const reason = document.getElementById("denyReason").value.trim();
  const urlParams = new URLSearchParams(window.location.search);
  const appID = urlParams.get("itp_application_id");

  if (!reason) {
    alert("Please provide a reason for denial.");
    return;
  }

  if (!appID) {
    alert("Missing ITP application ID from URL.");
    return;
  }

  fetch("supervisor_php/denyITPApplication.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      appID: appID,
      reason: reason
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert("ITP application denied successfully.");
      window.location.href = "comITPApplication.php";
    } else {
      alert("Error denying application: " + data.message);
    }
  })
  .catch(error => {
    console.error("Fetch error:", error);
    alert("An error occurred while denying the ITP application.");
  });

  closeDenyPopup();
}
