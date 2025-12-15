
// This function triggers when the 'Edit User' modal is about to be shown.
$('#editUserModal').on('show.bs.modal', function (event) {
    // Get the button that triggered the modal
    var button = $(event.relatedTarget);

    // Extract data attributes from the button
    var userId = button.data('userid');
    var name = button.data('name');
    var surname = button.data('surname');
    var phone1 = button.data('phone_1');  // Ensure data attribute names are consistent
    var phone2 = button.data('phone_2');
    var email = button.data('email');
    var nationality = button.data('nationality');


    console.log("Nationality picked for edit:", nationality); // Check what is received.

    // Get a reference to the modal about to be shown
    var modal = $(this);

    // Populate the form fields in the modal with the data from the selected user
    modal.find('#user_id').val(userId);
    modal.find('#edit_name').val(name);
    modal.find('#edit_surname').val(surname);
    modal.find('#edit_phone_1').val(phone1);
    modal.find('#edit_phone_2').val(phone2);
    modal.find('#edit_email').val(email);
    modal.find('#edit_nationality').val(nationality);
});

$(document).ready(function () {
    // Initialize DataTables for userTable if it exists on the current page
    if ($('#userTable').length) {
        $('#userTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": 7 }, // Disabling ordering for column 8
                { "searchable": false, "targets": 7 } // Disabling searching for column 8
            ]
        });
    }

    // Initialize DataTables for propertiesTable if it exists on the current page
    if ($('#propertiesTable').length) {
        $('#propertiesTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": 8 }, // Disabling ordering for column 9
                { "searchable": false, "targets": 8 } // Disabling searching for column 9
            ]
        });
    }

    // Initialize DataTables for buildingTable if it exists on the current page
    if ($('#buildingTable').length) {
        $('#buildingTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": 9 }, // Disabling ordering for column 10
                { "searchable": false, "targets": 9 } // Disabling searching for column 10
            ]
        });
    }

    // Initialize DataTables for billTable if it exists on the current page
    if ($('#billTable').length) {
        $('#billTable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                { "orderable": false, "targets": [ 5, 6, 7] }, // Disabling ordering for columns 4, 5, 6
                { "searchable": false, "targets": [ 5, 6, 7] } // Disabling searching for columns 4, 5, 6
            ]
        });
    }
});


// Validate that input contains only letters
function validateLetters(input) {
    const errorDiv = input.nextElementSibling; // Get the next sibling element for displaying the error message
    // Updated regex pattern to include Greek letters
    if (input.value.match(/^[A-Za-z\u0370-\u03FF\u1F00-\u1FFF\s]+$/)) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorDiv.textContent = ''; // Clear the error message if validation is successful
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorDiv.textContent = 'Only letters are allowed.'; // Display error message
    }
}

// Validate that input contains only numbers
function validateNumbers(input) {
    const errorDiv = input.nextElementSibling; // Get the next sibling element for displaying the error message
    if (input.value.match(/^\d*$/)) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorDiv.textContent = ''; // Clear the error message if validation is successful
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorDiv.textContent = 'Only numbers are allowed.'; // Display error message
    }
}

// Validate email format
function validateEmail(input) {
    const errorDiv = input.nextElementSibling;
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (emailRegex.test(input.value)) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorDiv.textContent = ''; // Clear the error message if validation is successful
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorDiv.textContent = 'Invalid email format'; // Display error message
    }
}

/*dashboard_admin_properties ..................................*/

function loadPropertyDetails(propertyId) {
    $.ajax({
        url: "fetchPropertyDetails.php",  // Make sure the path is correct relative to where the JS file is included.
        type: "POST",
        data: { property_id: propertyId },
        success: function (response) {
            $('#modalContent').html(response);  // Assuming the server sends back formatted HTML
        },
        error: function () {
            $('#modalContent').html('<p>Error loading details. Please try again.</p>');
        }
    });
}

/*dashboard_admin_settings ..................................*/

function validatePostalCode(input) {
    const errorDiv = input.nextElementSibling; // Get the next sibling element for displaying the error message
    if (input.value.match(/^\d{4}$/)) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorDiv.textContent = ''; // Clear the error message if validation is successful
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorDiv.textContent = 'Postal Code must be exactly 4 digits.'; // Display error message
    }
}
// Function to check if any form inputs are invalid
function checkFormBeforeSubmit(form) {
    const inputs = form.querySelectorAll('.form-control');
    let isFormValid = true;

    inputs.forEach(input => {
        if (input.classList.contains('is-invalid')) {
            isFormValid = false;
        }
    });

    return isFormValid;
}

// Event listener for form submission
document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', function (event) {
        if (!checkFormBeforeSubmit(form)) {
            event.preventDefault(); // Prevent form submission
            event.stopPropagation(); // Stop propagation of the event
            alert('Please correct the errors in the form before submitting.');
        }
    });
});


