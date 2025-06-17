let pendingStudentId = null;
  let pendingSupervisorId = null;

  function addStudent(supervisorId) {
    openAddStudentModal();
  }
  function showConfirmation(studentId, supervisorId) {
    pendingStudentId = studentId;
    pendingSupervisorId = supervisorId;
    document.getElementById("confirmModal").style.display = "flex";
  }

  function closeModal() {
    document.getElementById("confirmModal").style.display = "none";
    pendingStudentId = null;
    pendingSupervisorId = null;
  }

  function confirmRemove() {
    if (pendingStudentId && pendingSupervisorId) {
      window.location.href = 'supervisor_php/removeStudentManual.php?student_id=' + pendingStudentId + '&supervisor_id=' + pendingSupervisorId;
    }
  }

  function openAddStudentModal() {
  document.getElementById("addStudentModal").style.display = "flex";
}

function closeAddModal() {
  document.getElementById("addStudentModal").style.display = "none";
  document.getElementById("studentSelect").value = "";
  document.getElementById("studentDetails").style.display = "none";
}

function updateStudentDetails() {
  const select = document.getElementById("studentSelect");
  const selectedOption = select.options[select.selectedIndex];

  if (select.value !== "") {
    document.getElementById("detailID").textContent = select.value;
    document.getElementById("detailProgram").textContent = selectedOption.getAttribute("data-program");
    const supervisor = selectedOption.getAttribute("data-supervisor");
    document.getElementById("detailSupervisor").textContent = supervisor && supervisor !== "" ? supervisor : "-";
    document.getElementById("studentDetails").style.display = "block";
  } else {
    document.getElementById("studentDetails").style.display = "none";
  }
}

function confirmAddStudent() {
  const selectedId = document.getElementById("studentSelect").value;
  if (selectedId) {
    window.location.href = "supervisor_php/assignStudentManual.php?student_id=" + selectedId + "&supervisor_id=" + currentSupervisorId;
  }
}

