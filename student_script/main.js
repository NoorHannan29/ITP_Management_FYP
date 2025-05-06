function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const button = sidebar.querySelector('.toggle-btn');
    
    sidebar.classList.toggle('collapsed');
    
    // Change button text
    button.innerHTML = sidebar.classList.contains('collapsed') ? '&gt; Show' : '&#60; Hide';
}

