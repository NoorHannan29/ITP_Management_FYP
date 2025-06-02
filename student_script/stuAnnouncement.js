fetch('../php_files/get_announcements.php')
    .then(response => response.json())
    .then(data => {
        const container = document.querySelector('.announcement-body');
        container.innerHTML = ''; // Clear placeholder

        // Create table header
        const table = document.createElement('table');
        table.classList.add('announcement-table');

        const header = document.createElement('tr');
        header.innerHTML = `
            <th>Title</th>
            <th>Poster Name</th>
            <th>Date</th>
        `;
        table.appendChild(header);

        // Populate table rows
        data.forEach(item => {
            const row = document.createElement('tr');

            const date = new Date(item.Timestamp);
            const formattedDate = date.toLocaleDateString('en-MY', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            });

            row.innerHTML = `
                <td><a href="stuAnnouncementDisplay.html?id=${item.Announcement_ID}">${item.Title}</a></td>
                <td>${item.Poster_Name}</td>
                <td>${formattedDate}</td>
            `;

            table.appendChild(row);
        });

        container.appendChild(table);
    })
    .catch(error => {
        console.error('Failed to fetch announcements:', error);
    });
