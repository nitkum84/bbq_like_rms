<div class="modal fade" id="quickUserModal" tabindex="-1" aria-labelledby="quickUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickUserModalLabel">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" data-quick-user-error></div>
                <form data-quick-user-form data-quick-user-url="{{ route('admin.users.quick-create') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>
