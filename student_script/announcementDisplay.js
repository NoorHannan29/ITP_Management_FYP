document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const announcementId = urlParams.get("id");

    if (!announcementId) {
        document.querySelector(".announcement-title").textContent = "Announcement Not Found";
        document.querySelector(".announcement-body").textContent = "Invalid announcement ID.";
        return;
    }

    fetch(`../php_files/get_single_announcement.php?id=${announcementId}`)
        .then(response => response.json())
        .then(data => {
            if (!data || !data.Title) {
                document.querySelector(".announcement-title").textContent = "Announcement Not Found";
                document.querySelector(".announcement-body").textContent = "This announcement does not exist.";
                return;
            }

            document.querySelector(".announcement-title").textContent = data.Title;
            document.querySelector(".announcement-body").innerHTML = `
                <p><strong>By:</strong> ${data.Poster_Name}</p>
                <p><strong>Date:</strong> ${new Date(data.Timestamp).toLocaleString()}</p>
                <hr>
                <p>${data.Content}</p>
            `;
        })
        .catch(err => {
            console.error("Error fetching announcement:", err);
        });
});
