document.addEventListener("DOMContentLoaded", function() {
    const branchSelect = document.getElementById('branch');  
    const semesterSelect = document.getElementById('semester');
    const courseSelect = document.getElementById('course');
    const divisionSelect = document.getElementById('division');
    const batchesSelect = document.getElementById('batch'); 
    const theoryLabSelect = document.getElementById('TL');  // Theory/Lab dropdown
    const facultySelect = document.getElementById('faculty');

    // Function to handle populating dropdowns
    function populateDropdown(selectElement, data, property) {
        selectElement.innerHTML = '<option value="">Select</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[property]; // Use the specified property dynamically
            option.text = item[property];
            selectElement.add(option);
        });
    }
    

    // Fetch and populate the branch automatically for the logged-in user
    fetch('get-branches.php')
    .then(response => response.json())
    .then(data => {
        if (data.branch) {
            // If the branch is correctly fetched, add it to the dropdown
            branchSelect.innerHTML = `<option value="${data.branch}">${data.branch}</option>`;
            branchSelect.setAttribute('readonly', 'readonly'); 
        } else {
            console.error('Error fetching branch:', data.error); // Log any error received from the server
        }
    })
    .catch(error => console.error('Error fetching branch:', error));


    // Fetch and populate semesters
    fetch('get-semesters.php')
        .then(response => response.json())
        .then(data => populateDropdown(semesterSelect, data, 'name'))
        .catch(error => console.error('Error fetching semesters:', error));

    // Fetch and populate courses
    fetch('get-courses.php')
        .then(response => response.json())
        .then(courses => {
            courseSelect.innerHTML = '<option value="">Select</option>'; // Clear existing options
            courses.forEach(course => {
                let option = document.createElement('option');
                option.value = course;
                option.textContent = course;
                courseSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching courses:', error));

    // Fetch and populate divisions
    fetch('get-divisions.php')
        .then(response => response.json())
        .then(data => populateDropdown(divisionSelect, data, 'name'))
        .catch(error => console.error('Error fetching divisions:', error));

        
 // Enable/disable batch dropdown based on Theory/Lab selection
 theoryLabSelect.addEventListener('change', function() {
    if (theoryLabSelect.value === 'Lab') {
        batchesSelect.disabled = false;
    } else {
        batchesSelect.disabled = true;
        batchesSelect.value = '';
    }
});

batchesSelect.disabled = true;  // Initially disable batch dropdown

// Fetch and populate batches
fetch('get-batch.php')
    .then(response => response.json())
    .then(data => populateDropdown(batchesSelect, data, 'batch'))
    .catch(error => console.error('Error fetching batches:', error));
});

// Redirect based on Theory/Lab selection
document.getElementById('reportForm').addEventListener('submit', function(event) {
event.preventDefault(); // Prevent default form submission

const selectedValue = document.getElementById('TL').value;

if (selectedValue === 'Theory') {
    this.action = 'generate_T_report.php';  // Redirect to Theory report file
} else if (selectedValue === 'Lab') {
    this.action = 'generate_L_report.php';  // Redirect to Lab report file
} else {
    alert('Please select either Theory or Lab to generate a report.');
    return;
}

this.submit();  


fetch('get-faculties.php')
        .then(response => response.json())
        .then(data => populateDropdown(facultySelect, data))
        .catch(error => console.error('Error fetching faculty:', error));
});

