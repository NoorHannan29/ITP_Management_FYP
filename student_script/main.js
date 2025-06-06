function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const content = document.getElementById("content");

    sidebar.classList.toggle("collapsed");

    // Adjust content margin based on sidebar state
    if (sidebar.classList.contains("collapsed")) {
        content.style.marginLeft = "40px";
    } else {
        content.style.marginLeft = "50px";
    }
}

