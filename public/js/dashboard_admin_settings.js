
var deleteBuildingId; // Variable to hold the user ID to delete

function setDeleteBuildingId(buildingId) {
    deleteBuildingId = buildingId; // Set the global user ID
}

$('#confirmDelete').click(function () {
    // Submit the form programmatically
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = ''; // Specify the action if needed

    var idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'building_id';
    idInput.value = deleteBuildingId;
    form.appendChild(idInput);

    var actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'deleteBuilding';
    actionInput.value = '1'; // Value that triggers the delete operation in your PHP
    form.appendChild(actionInput);

    document.body.appendChild(form);
    form.submit();
});


type = "text/javascript"
function updateFileName() {
    var addPhoto = document.getElementById('add-photo');
    var fileName = addPhoto.files[0].name; // Get the file name
    document.getElementById('add-file-name').value = fileName; // Set the file name in the text input
}

function fileName() {
    var addPhoto = document.getElementById('edit-photo');
    if (addPhoto.files && addPhoto.files[0]) {
        var fileName = addPhoto.files[0].name; // Get the file name
        document.getElementById('edit-file-name').value = fileName; // Set the file name in the text input
    }
}

$(document).ready(function () {
    // Function to adjust textarea height
    function adjustTextareaHeight(textarea) {
        textarea.style.height = 'auto'; // Reset height to calculate new scroll height
        textarea.style.height = textarea.scrollHeight + 'px'; // Set new height based on scroll height
    }

    // When the edit modal is shown, adjust the textarea height
    $('#editBuildingModal').on('shown.bs.modal', function () {
        adjustTextareaHeight(document.getElementById('edit_comment'));
    });

    // Also adjust on input in case you need it to expand as the user types
    $('#edit_comment').on('input', function () {
        adjustTextareaHeight(this);
    });

    // This function is triggered when an edit button is clicked
    $('#buildingTable').on('click', '.edit-btn', function () {
        // Retrieve data from the button
        var buildingId = $(this).data('buildingid');
        var name = $(this).data('name');
        var address = $(this).data('address');
        var postalCode = $(this).data('postal_code');
        var city = $(this).data('city');
        var county = $(this).data('county');
        var numFloors = $(this).data('num_floors');
        var numProperties = $(this).data('num_properties');
        var comment = $(this).data('comment');
        var photoFilename = $(this).data('photo'); // The photo file name should be stored here

        var fileNameOnly = photoFilename.split('/').pop();

        // Set the values in the edit modal form
        $('#editBuildingModal #building_id').val(buildingId);
        $('#editBuildingModal #edit_name').val(name);
        $('#editBuildingModal #edit_address').val(address);
        $('#editBuildingModal #edit_postal_code').val(postalCode);
        $('#editBuildingModal #edit_city').val(city);
        $('#editBuildingModal #edit_county').val(county);
        $('#editBuildingModal #edit_num_floors').val(numFloors);
        $('#editBuildingModal #edit_num_properties').val(numProperties);
        $('#editBuildingModal #edit_comment').val(comment.replace(/<br\s*[\/]?>/gi, "\n"));
        $('#editBuildingModal #edit-file-name').val(fileNameOnly);
    });
});
$(document).ready(function () {
    // Select all Bootstrap alerts
    $('.alert').each(function () {
        // Set a timeout to fade out the alert
        $(this).delay(3000).fadeOut('slow', function () {
            $(this).remove(); // Optional: remove the element after fading out
        });
    });
});

function displayBackupFiles() {
    fetch('list_backups.php')
        .then(response => response.json())
        .then(data => {
            const filesList = document.getElementById('filesList');
            filesList.innerHTML = ''; // Clear existing entries
            data.forEach(file => {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                listItem.innerHTML = `
                    <span>${file.date} - ${file.name}</span>
                    <div class="btn-group" role="group" aria-label="File Actions">
                        <a href="${file.url}" class="btn btn-sm" style="background-color: #086972; color: white;" title="Download">
                            <i class="bi bi-download"></i>
                        </a>
                        <button class="btn btn-danger btn-sm" onclick="deleteBackup('${file.name}')" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                filesList.appendChild(listItem);
            });
        })
        .catch(error => console.error('Error:', error));
}
// Attach event listener for modal show event
$('#filesModal').on('show.bs.modal', function () {
    displayBackupFiles(); // This function will fetch and display the backup files
});
function deleteBackup(fileName) {
    if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        $.ajax({
            url: 'delete_backup.php',
            type: 'POST',
            data: {
                file: fileName
            },
            success: function (response) {
                alert('Backup deleted successfully!');
                window.location.reload(); // Reload the page to update the list of backups
            },
            error: function () {
                alert('Error deleting backup. Please try again.');
            }
        });
    }
}
document.getElementById('deleteButton').addEventListener('click', function () {
    if (confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        deleteBackup(); // Function to handle the deletion
    }
});

