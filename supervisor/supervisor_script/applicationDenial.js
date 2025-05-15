function openDenyPopup() {
  document.getElementById("denyPopup").style.display = "flex";
}

function closeDenyPopup() {
  document.getElementById("denyPopup").style.display = "none";
}

function submitDenial() {
  const reason = document.getElementById("denyReason").value;
  if (reason.trim() === "") {
    alert("Please provide a reason for denial.");
    return;
  }
  // Process the reason here (send to backend, etc.)
  alert("Denial reason submitted: " + reason);
  closeDenyPopup();
}
