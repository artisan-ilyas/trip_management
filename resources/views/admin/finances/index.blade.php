@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
    <div class="container pt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4">Manage Finances</h2>
            <a href="{{ route('agents.create') }}" class="btn btn-primary">Create Finances</a>
        </div>

            @if(session('success'))
            <div class="alert alert-success" id="success-message">
                {{ session('success') }}
            </div>
            @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-uppercase small">
                            <tr>
                                <th>#</th>
                                <!-- <th>First name</th>
                                 <th>Last name</th> -->
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                           </tbody>

                    </table>

                   
                    <!-- /.modal -->

                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
$(document).ready(function() {
  $('#editUserModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    
    // Get data attributes from the Edit button
    var id = button.data('id');
    var firstName = button.data('first_name');
    var lastName = button.data('last_name');


    // Fill the form inside the modal
    var modal = $(this);
    modal.find('#edit-user-first-name').val(firstName);
    modal.find('#edit-user-last-name').val(lastName);
   
    
    // Set form action
    modal.find('#editUserForm').attr('action', '/users/' + id);
  });
});


setTimeout(function() {
        let message = document.getElementById('success-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 2000); // 3000 milliseconds = 3 seconds
</script>


@endsection
