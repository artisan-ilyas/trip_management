<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trip Management | Guest Form</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="{{ asset('plugins/bs-stepper/css/bs-stepper.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
    <div class="container">
    <div class="navbar-brand">
        {{-- <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
        <span class="brand-text font-weight-light">Trip management</span>
    </div>
    </div>
  </nav>
  <!-- /.navbar -->
        @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            </script>
        @endif
  <!-- Content Wrapper -->
  <div class="content-wrapper mb-4">
    <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card mt-3 mb-5 card-default">
          <div class="card-header bg-primary">
            <h3 class="card-title">Guest information</h3>
          </div>
<form action="{{ route('guest.store') }}" method="POST" enctype="multipart/form-data" id="guestForm" novalidate>
    @csrf
    <div class="card-body p-0">
        <div class="bs-stepper">
            <div class="bs-stepper-header" role="tablist">
                <div class="step" data-target="#logins-part">
                    <button type="button" class="step-trigger" role="tab" aria-controls="logins-part" id="logins-part-trigger">
                        <span class="bs-stepper-circle bg-success">1</span>
                        <span class="bs-stepper-label">Guest Info</span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#personal-part">
                    <button type="button" class="step-trigger" role="tab" aria-controls="personal-part" id="personal-part-trigger">
                        <span class="bs-stepper-circle bg-success">2</span>
                        <span class="bs-stepper-label">Travel Details</span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#confirmation-part">
                    <button type="button" class="step-trigger" role="tab" aria-controls="confirmation-part" id="confirmation-part-trigger">
                        <span class="bs-stepper-circle bg-success">3</span>
                        <span class="bs-stepper-label">Preferences & Health</span>
                    </button>
                </div>
                <div class="line"></div>
                <div class="step" data-target="#guest-part">
                    <button type="button" class="step-trigger" role="tab" aria-controls="guest-part" id="guest-part-trigger">
                        <span class="bs-stepper-circle bg-success">4</span>
                        <span class="bs-stepper-label">File Uploads & Confirmation</span>
                    </button>
                </div>
            </div>

            <div class="bs-stepper-content">
                <input type="hidden" name="trip_id" id="tripIdInput">
                <input type="hidden" name="otherGuests" id="otherGuests" value="1">

                <!-- Step 1 -->
                <div id="logins-part" class="content" role="tabpanel" aria-labelledby="logins-part-trigger">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Name must contain only letters and spaces.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                                <option value="">Select gender</option>
                                <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                                <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                                <option value="other" {{ old('gender')=='other'?'selected':'' }}>Other</option>
                            </select>
                            <div class="invalid-feedback">Please select a gender.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            <div class="invalid-feedback">Enter a valid email address.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="dob">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}" required>
                            <div class="invalid-feedback">Please select your date of birth.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="passport">Passport Number <span class="text-danger">*</span></label>
                            <input type="text" name="passport" id="passport" class="form-control @error('passport') is-invalid @enderror" value="{{ old('passport') }}" required pattern="^[A-Za-z0-9]{6,20}$">
                            <div class="invalid-feedback">Passport must be 6–20 characters (letters/numbers).</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nationality">Nationality <span class="text-danger">*</span></label>
                            <input type="text" name="nationality" id="nationality" class="form-control @error('nationality') is-invalid @enderror" value="{{ old('nationality') }}" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Nationality must contain only letters.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="cabin">Cabin Type <span class="text-danger">*</span></label>
                            <select name="cabin" id="cabin" class="form-control" required>
                                <option value="">Select cabin type</option>
                                <option value="standard">Standard</option>
                                <option value="deluxe">Deluxe</option>
                                <option value="suite">Suite</option>
                            </select>
                            <div class="invalid-feedback">Please select a cabin type.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="surfLevel">Surf Level <span class="text-danger">*</span></label>
                            <select name="surfLevel" id="surfLevel" class="form-control" required>
                                <option value="">Select level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                            <div class="invalid-feedback">Please select your surf level.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="boardDetails">Board Details <span class="text-danger">*</span></label>
                        <textarea name="boardDetails" id="boardDetails" rows="2" class="form-control" required></textarea>
                        <div class="invalid-feedback">Please provide board details.</div>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="validateStep(this)">Next</button>
                </div>

                <!-- Step 2: Travel -->
                <div id="personal-part" class="content" role="tabpanel" aria-labelledby="personal-part-trigger">
                    <h5>PICK UP / ARRIVAL</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="arrivalFlightDate">Flight Date <span class="text-danger">*</span></label>
                            <input type="date" name="arrivalFlightDate" id="arrivalFlightDate" class="form-control" required>
                            <div class="invalid-feedback">Please select arrival flight date.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="arrivalFlightNumber">Flight Number <span class="text-danger">*</span></label>
                            <input type="text" name="arrivalFlightNumber" id="arrivalFlightNumber" class="form-control" required pattern="^[A-Za-z0-9]+$">
                            <div class="invalid-feedback">Enter a valid flight number (letters/numbers only).</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="arrivalAirport">Arrival Airport <span class="text-danger">*</span></label>
                            <input type="text" name="arrivalAirport" id="arrivalAirport" class="form-control" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Enter a valid airport name.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="arrivalTime">Arrival Time <span class="text-danger">*</span></label>
                            <input type="time" name="arrivalTime" id="arrivalTime" class="form-control" required>
                            <div class="invalid-feedback">Select arrival time.</div>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="hotelPickup">Hotel Pickup <span class="text-danger">*</span></label>
                            <textarea name="hotelPickup" id="hotelPickup" rows="2" class="form-control" required></textarea>
                            <div class="invalid-feedback">Enter hotel pickup details.</div>
                        </div>
                    </div>

                    <h5 class="mt-4">DROP OFF / DEPARTURE</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="departureFlightDate">Flight Date <span class="text-danger">*</span></label>
                            <input type="date" name="departureFlightDate" id="departureFlightDate" class="form-control" required>
                            <div class="invalid-feedback">Select departure flight date.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="departureFlightNumber">Flight Number <span class="text-danger">*</span></label>
                            <input type="text" name="departureFlightNumber" id="departureFlightNumber" class="form-control" required pattern="^[A-Za-z0-9]+$">
                            <div class="invalid-feedback">Enter valid flight number.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="departureAirport">Departure Airport <span class="text-danger">*</span></label>
                            <input type="text" name="departureAirport" id="departureAirport" class="form-control" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Enter valid airport name.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="departureTime">Departure Time <span class="text-danger">*</span></label>
                            <input type="time" name="departureTime" id="departureTime" class="form-control" required>
                            <div class="invalid-feedback">Select departure time.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                    <button type="button" class="btn btn-primary" onclick="validateStep(this)">Next</button>
                </div>

                <!-- Step 3: Preferences -->
                <div id="confirmation-part" class="content" role="tabpanel" aria-labelledby="confirmation-part-trigger">
                    <h5>Preferences</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="medicalDietary">Medical/Dietary Requirements <span class="text-danger">*</span></label>
                            <textarea name="medicalDietary" id="medicalDietary" rows="2" class="form-control" required></textarea>
                            <div class="invalid-feedback">Enter medical/dietary requirements.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="specialRequests">Special Requests <span class="text-danger">*</span></label>
                            <textarea name="specialRequests" id="specialRequests" rows="2" class="form-control" required></textarea>
                            <div class="invalid-feedback">Enter special requests.</div>
                        </div>
                    </div>

                    <h5>Insurance</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="insuranceName">Insurance Name <span class="text-danger">*</span></label>
                            <input type="text" name="insuranceName" id="insuranceName" class="form-control" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Enter a valid insurance name.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="policyNumber">Policy Number <span class="text-danger">*</span></label>
                            <input type="text" name="policyNumber" id="policyNumber" class="form-control" required pattern="^[A-Za-z0-9]+$">
                            <div class="invalid-feedback">Enter valid policy number.</div>
                        </div>
                    </div>

                    <h5>Emergency Contact</h5>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="emergencyName">Name <span class="text-danger">*</span></label>
                            <input type="text" name="emergencyName" id="emergencyName" class="form-control" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Enter a valid name.</div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="emergencyRelation">Relation <span class="text-danger">*</span></label>
                            <input type="text" name="emergencyRelation" id="emergencyRelation" class="form-control" required pattern="^[A-Za-z\s]+$">
                            <div class="invalid-feedback">Enter relation in letters only.</div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="emergencyPhone">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="emergencyPhone" id="emergencyPhone" class="form-control" required pattern="^\+?[0-9]{10,15}$">
                            <div class="invalid-feedback">Enter valid phone number (10–15 digits).</div>
                        </div>
                    </div>

                    <h5>Guest Contact</h5>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="guestWhatsapp">WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" name="guestWhatsapp" id="guestWhatsapp" class="form-control" required pattern="^\+?[0-9]{10,15}$">
                            <div class="invalid-feedback">Enter valid WhatsApp number.</div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="guestEmail">Email <span class="text-danger">*</span></label>
                            <input type="email" name="guestEmail" id="guestEmail" class="form-control" required>
                            <div class="invalid-feedback">Enter valid email.</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                    <button type="button" class="btn btn-primary" onclick="validateStep(this)">Next</button>
                </div>

                <!-- Step 4: Upload -->
                <div id="guest-part" class="content" role="tabpanel" aria-labelledby="guest-part-trigger">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="imageUpload">Upload Image <span class="text-danger">*</span></label>
                            <input type="file" name="imageUpload" id="imageUpload" class="form-control-file" accept="image/*" required>
                            <div class="invalid-feedback d-block">Please upload an image file.</div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="pdfUpload">Upload PDF <span class="text-danger">*</span></label>
                            <input type="file" name="pdfUpload" id="pdfUpload" class="form-control-file" accept="application/pdf" required>
                            <div class="invalid-feedback d-block">Please upload a PDF file.</div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="excelUpload">Upload Excel <span class="text-danger">*</span></label>
                            <input type="file" name="excelUpload" id="excelUpload" class="form-control-file" accept=".xlsx,.xls" required>
                            <div class="invalid-feedback d-block">Please upload an Excel file.</div>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input type="checkbox" name="terms" id="terms" class="form-check-input" required>
                        <label class="form-check-label" for="terms">I agree to the terms and conditions <span class="text-danger">*</span></label>
                        <div class="invalid-feedback d-block">You must accept the terms.</div>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="stepper.previous()">Previous</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>



          <!-- <div class="card-footer">
            Visit <a href="https://github.com/Johann-S/bs-stepper/#how-to-use-it">bs-stepper documentation</a> for more examples and information.
          </div> -->
        </div>
      </div>
    </div>
    </div>
  </div>
    <!-- Main Footer -->
  <footer class="main-footer fixed-bottom mt-5">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2025 <a href="https://127.0.0.1">Trip Management</a>.</strong> All rights reserved.
  </footer>
</div>

<!-- Scripts -->
<script src="{{ asset('plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
<script>
 //form steps

  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
    const urlParams = new URLSearchParams(window.location.search);
    const tripId = urlParams.get('trip_id');

    if (tripId) {
        const tripIdInput = document.getElementById('tripIdInput');
        if (tripIdInput) {
            tripIdInput.value = tripId;
        }
    }
  });
  //add guest-form
function addGuestForm() {
    const container = document.getElementById('guestContainer');
    const firstForm = container.querySelector('.guest-form');
    const clone = firstForm.cloneNode(true);

    // Clear all input/select/textarea values in the cloned form
    clone.querySelectorAll('input, select, textarea').forEach(el => el.value = '');

    // Add remove button if not already present
    if (!clone.querySelector('.remove-btn')) {
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn btn-sm btn-danger remove-btn mb-3';
      removeBtn.textContent = 'Remove This Guest';
      removeBtn.onclick = () => clone.remove();
      clone.appendChild(removeBtn);
    }

    container.appendChild(clone);
  }

    //file upload
  function previewFile(event, type) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById(`${type}Preview`);
    previewContainer.innerHTML = ''; // clear previous

    if (!file) return;

    let preview;

    if (type === 'image') {
      preview = document.createElement('img');
      preview.src = URL.createObjectURL(file);
      preview.style.maxWidth = '100%';
      preview.style.maxHeight = '150px';
    } else if (type === 'pdf') {
      preview = document.createElement('embed');
      preview.src = URL.createObjectURL(file);
      preview.type = 'application/pdf';
      preview.width = '100%';
      preview.height = '150px';
    } else if (type === 'video') {
      preview = document.createElement('video');
      preview.src = URL.createObjectURL(file);
      preview.controls = true;
      preview.style.width = '100%';
      preview.style.maxHeight = '150px';
    }

    const deleteBtn = document.createElement('button');
    deleteBtn.innerText = 'X';
    deleteBtn.className = 'btn btn-sm btn-danger mt-2 mx-2';
    deleteBtn.onclick = function () {
      document.getElementById(`${type}Upload`).value = ''; // reset input
      previewContainer.innerHTML = ''; // remove preview
    };

    previewContainer.appendChild(preview);
    previewContainer.appendChild(deleteBtn);
  }
 
function validateStep(button) {
    let stepContent = button.closest('.content');
    let inputs = stepContent.querySelectorAll('input, select, textarea');
    let valid = true;

    inputs.forEach(input => {
        if (!input.checkValidity()) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    if (valid) stepper.next();
}

document.querySelectorAll('#guestForm input, #guestForm select, #guestForm textarea').forEach(input => {
    input.addEventListener('input', () => {
        if (!input.checkValidity()) {
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });
});


</script>
</body>
</html>
