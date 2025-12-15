
// This function triggers when the 'Edit User' modal is about to be shown.

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
        url: "../../app/Models/addProperties.php",  // Make sure the path is correct relative to where the JS file is included.
        type: "POST",
        data: { property_id: propertyId },
        success: function(response) {
            $('#modalContent').html(response);  // Assuming the server sends back formatted HTML
        },
        error: function() {
            $('#modalContent').html('<p>Error loading details. Please try again.</p>');
        }
    });
}

// Event listener for form submission
document.querySelectorAll('.needs-validation').forEach(form => {
    form.addEventListener('submit', function(event) {
        if (!checkFormBeforeSubmit(form)) {
            event.preventDefault(); // Prevent form submission
            event.stopPropagation(); // Stop propagation of the event
            alert('Please correct the errors in the form before submitting.');
        }
    });
});
