function updateSliderLabel(value) {
          const label = document.getElementById('score_description');
          let text = "";
          if (value < 4) text = "Poor";
          else if (value < 8) text = "Lacking";
          else if (value < 12) text = "Decent";
          else if (value < 16) text = "Good";
          else text = "Outstanding";
          label.innerText = text;
        }

        document.addEventListener("DOMContentLoaded", function () {
          const companyRadios = document.querySelectorAll("input[name='company_presentation']");
          const facultyRadios = document.querySelectorAll("input[name='faculty_presentation']");
          const totalDisplay = document.getElementById("presentation_total");

          function updateTotal() {
            const c = document.querySelector("input[name='company_presentation']:checked");
            const f = document.querySelector("input[name='faculty_presentation']:checked");
            let total = 0;
            if (c) total += parseInt(c.value);
            if (f) total += parseInt(f.value);
            totalDisplay.textContent = total;
          }

          companyRadios.forEach(r => r.addEventListener("change", updateTotal));
          facultyRadios.forEach(r => r.addEventListener("change", updateTotal));
        });

function updateSliderLabel(value) {
  document.getElementById('score_value').innerText = value;
  const label = document.getElementById('score_description');
  let text = "";
  if (value < 4) text = "Poor";
  else if (value < 8) text = "Lacking";
  else if (value < 12) text = "Decent";
  else if (value < 16) text = "Good";
  else text = "Outstanding";
  label.innerText = text;
}
