fetch('../php_files/get_company_list.php')
    .then(response => response.json())
    .then(data => {
        const container = document.querySelector('.comp-body');
        container.innerHTML = ''; // Clear placeholder

        // Create table header
        const table = document.createElement('table');
        table.classList.add('company-table');

        const header = document.createElement('tr');
        const headers = ['Company_ID', 'Company_Name', 'Company_Address', 'Company_City', 'Company_State', 'Company_Contact_No', 'Company_Contact_Email', 'Company_Website'];
        headers.forEach(headerText => {
            const th = document.createElement('th');
            th.textContent = headerText;
            header.appendChild(th);
        });
        table.appendChild(header);

        // Populate table rows
        data.forEach(company => {
            const row = document.createElement('tr');
            Object.values(company).forEach(value => {
                const td = document.createElement('td');
                td.textContent = value;
                row.appendChild(td);
            });
            table.appendChild(row);
        });

        container.appendChild(table);
    })
    .catch(error => {
        console.error('Failed to fetch companies:', error);
    });
