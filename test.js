document.addEventListener("DOMContentLoaded", function() {
    const batchesSelect = document.getElementById('batch');

    if (batchesSelect) {
        fetch('get-batch.php')
            .then(response => response.json())
            .then(data => {
                console.log('Fetched batch data:', data); // Log the fetched batch data
                populateDropdown(batchesSelect, data);
            })
            .catch(error => console.error('Error fetching batches:', error));
    } else {
        console.error('Batch dropdown not found in the DOM.');
    }

    // Function to populate dropdown
    function populateDropdown(selectElement, data) {
        selectElement.innerHTML = '<option value="">Select</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.batch; // Correctly use 'item.batch'
            option.text = item.batch;
            selectElement.add(option);
        });
    }
});
