document.addEventListener("DOMContentLoaded", () => {
    fetch('php_files/get_application_status.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const app = data.data;

                document.querySelector('.application-status').textContent =
                    `APPLICATION STATUS : ${app.Application_Status}`;

                const detailsHTML = `
                    <p><strong>Internship Period:</strong> ${app.Internship_Start_Date} to ${app.Internship_End_Date}</p>
                    <p><strong>Allowance:</strong> RM ${app.Allowance_Amount || 'N/A'}</p>
                    <p><strong>Job Description:</strong> ${app.Job_Description}</p>
                    <p><strong>Company Name:</strong> ${app.Company_Name}</p>
                    <p><strong>Company Address:</strong> ${app.Company_Address}</p>
                    <p><strong>State:</strong> ${app.Company_State}</p>
                    <p><strong>Contact Person:</strong> ${app.Company_Contact_Name}</p>
                    <p><strong>Designation:</strong> ${app.Company_Designation || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${app.Company_Phone || 'N/A'}</p>
                    <p><strong>Email:</strong> ${app.Company_Email || 'N/A'}</p>
                    <p><strong>Website:</strong> ${app.Company_Website || 'N/A'}</p>
                `;

                document.querySelector('.filled-form-body').innerHTML = detailsHTML;
            } else if (data.status === 'no_application') {
                document.querySelector('.filled-form-body').innerHTML = '<p>No application found.</p>';
                document.querySelector('.application-status').textContent = 'APPLICATION STATUS : N/A';
            } else {
                document.querySelector('.filled-form-body').innerHTML = '<p>Error loading application.</p>';
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            document.querySelector('.filled-form-body').innerHTML = '<p>Could not fetch application data.</p>';
        });
});
