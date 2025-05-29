<script>
    $(document).ready(() => {
        $("#success-message").hide();
        $("#error-message").hide();
        MakeUsersTable();

        $("#userName").on('keyup', function () {
            $.ajax({
                type: "post",
                url: "{{url('/users/username/check')}}",
                data: { Username: this.value }
            }).done(function (data) {
                data = JSON.parse(data);
                if (data.message !== 'success') {
                    $("#usernameCheck").css('visibility', 'initial');
                    $("#saveUserBtn").prop('disabled', true);
                } else {
                    $("#usernameCheck").css('visibility', 'hidden');
                    $("#saveUserBtn").prop('disabled', false);
                }
            });
        });

        $("#userPassword").on('keyup', function () {
            if(this.value !== $("#userConfirmPassword").val()){
                $("#passwordCheck").css('visibility', 'initial');
                $("#saveUserBtn").prop('disabled', true);
            }
            else{
                $("#passwordCheck").css('visibility', 'hidden');
                $("#saveUserBtn").prop('disabled', false);
            }
        });

        $("#userConfirmPassword").on('keyup', function () {
            if(this.value !== $("#userPassword").val()){
                $("#passwordCheck").css('visibility', 'initial');
                $("#saveUserBtn").prop('disabled', true);
            }
            else{
                $("#passwordCheck").css('visibility', 'hidden');
                $("#saveUserBtn").prop('disabled', false);
            }
        });
    });

    function MakeUsersTable() {
        let Table = $("#usersTable");
        if (Table.length > 0) {
            Table.DataTable({
                "processing": true,
                "serverSide": true,
                "paging": true,
                "bPaginate": true,
                "ordering": true,
                "ajax": {
                    "url": "{{url('/users/all')}}",
                    "type": "POST"
                },
                'columns': [
                    { data: 'id' },
                    { data: 'user_title' },
                    { data: 'user_role' },
                    { data: 'createP' },
                    { data: 'viewP' },
                    { data: 'updateP' },
                    { data: 'deleteP' },
                    { data: 'action', orderable: false },
                ],
                'order': [0, 'asc']
            });
        }
    }

    function DeleteUser(id) {
        id = id.split('_')[1];
        $("#deleteUserId").val(id);
        $("#deleteUserModel").modal('toggle');
    }

    function ConfirmDeleteUser(e) {
        $.ajax({
            type: "post",
            url: "{{url('/users/delete')}}",
            data: { UserId: $("#deleteUserId").val() }
        }).done(function (data) {
            $(e).attr('disabled', false);
            data = JSON.parse(data);
            if (data.message === 'success') {
                $("#deleteUserModel").modal('toggle');
                $("#success-message-content").text("User deleted successfully");
                $("#success-message").show();
                $("#error-message").hide();
                $('#usersTable').DataTable().ajax.reload();
                setTimeout(function () {
                    $("#success-message-content").text("");
                    $("#success-message").slideUp();
                    $("#error-message").hide();
                }, 2500);
            } else {
                $("#deleteUserModel").modal('toggle');
                $("#error-message-content").text("An unhandled error occurred");
                $("#success-message").hide();
                $("#error-message").show();
                $('#carBrandsTable').DataTable().ajax.reload();
                setTimeout(function () {
                    $("#error-message-content").text("");
                    $("#success-message").hide();
                    $("#error-message").slideUp();
                }, 2500);
            }
        });
    }

    function EditUser(id) {
        id = id.split('_')[1];
        @php            
            $Url = url('/users/edit/');
        @endphp
        window.location.href = '{{$Url}}' + "/" + id;
    }
</script>
