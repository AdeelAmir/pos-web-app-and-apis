<div class="modal fade" id="deleteUserModel" tabindex="200" role="dialog" aria-labelledby="deleteUserModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Delete</h5>
            </div>
            <div class="modal-body">
                <form action="" id="deleteUserForm">
                    <div class="form-row">
                        <!-- Brand start here -->
                        <div class="col-md-12 ">
                            <p style="font-size: 1.5em;">Are you sure you want to delete this item?</p>
                        </div>
                        {{--Hidden Field for Id--}}
                        <input type="hidden" name="deleteUserId" id="deleteUserId" value="0" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="ConfirmDeleteUser(this);" type="button">
                    <i class="fa fa-trash"></i>
                    Delete
                </button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>