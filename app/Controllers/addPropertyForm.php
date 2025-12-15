<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    <form method="POST" onsubmit="handleFormSubmit(event)" enctype="multipart/form-data" novalidate>
        <div class="row gutters">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="propertyPhotos">Property Photos</label>
                    <input type="file" class="form-control-file" id="propertyPhotos" name="photo[]" multiple="multiple">
                    <small id="propertyPhotosHelp" class="form-text text-muted">Upload photos of the
                        property.</small>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="floor">Floor</label>
                    <input type="number" class="form-control" id="floor" name="floor" required>
                    <div class="invalid-feedback">
                        Please enter number of floors.
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="floor">Apt Number</label>
                    <input type="number" class="form-control" id="num" name="num" required>
                    <div class="invalid-feedback">
                        Please enter apt number.
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="rooms">Number of Rooms</label>
                    <input type="number" class="form-control" id="rooms" name="rooms" required>
                    <div class="invalid-feedback">
                        Please enter number of rooms.
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="parking">Parking Spots (Covered/Uncovered) </label>
                    <select class="form-control" id="parking" name="parking" required>
                        <option value="u">Uncovered</option>
                        <option value="c">Covered</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="status">Rental Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="a">Available</option>
                        <option value="r">Rented</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="pet">Pets Allowed</label>
                    <select class="form-control" id="pet" name="pet" required>
                        <option value="y">Yes</option>
                        <option value="n">No</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="furnished">Furnished</label>
                    <select class="form-control" id="furnished" name="furnished" required>
                        <option value="y">Yes</option>
                        <option value="n">No</option>
                    </select>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="bathrooms">Number of Bathrooms</label>
                    <input type="number" class="form-control" id="bathrooms" name="bathrooms" required>
                    <div class="invalid-feedback">
                        Please enter number of bathrooms.
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                <div class="form-group">
                    <label for="area">Area (Square Meters)</label>
                    <input type="number" class="form-control" id="area" name="area" required>
                    <div class="invalid-feedback">
                        Please enter area size.
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                <div class="form-group">
                    <label for="details">Property Details</label>
                    <textarea class="form-control" id="details" name="details" rows="3" required></textarea>
                    <div class="invalid-feedback">
                        Please enter property details.
                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
            })
        })()

        document.addEventListener('DOMContentLoaded', function () {
            var form = document.querySelector('form');
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault(); // Prevent form submission
                    event.stopPropagation(); // Stop propagation of the event
                }
                form.classList.add('was-validated'); // Add this class to show validation feedback
            }, false);
        });
        function handleFormSubmit(event) {
            var form = event.target;
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
            // Add your code here for further processing if the form is valid
        }
</script>