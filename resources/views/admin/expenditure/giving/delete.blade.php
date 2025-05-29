<div class="modal fade" id="deleteExpenditureGivingModel" tabindex="200" role="dialog" aria-labelledby="deleteExpenditureGivingModelLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteExpenditureGivingModalLabel">Delete Expenditure Giving</h5>
            </div>
            <div class="modal-body">
                <form action="" id="deleteExpenditureGivingForm">
                    <div class="form-row">
                        <div class="col-md-12">
                            <p style="font-size: 1.5em;">Are you sure you want to delete this expenditure giving?</p>
                        </div>
                        {{--Hidden Field for Id--}}
                        <input type="hidden" name="deleteExpenditureGivingId" id="deleteExpenditureGivingId" value="0" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="ConfirmDeleteExpenditureGiving(this);" type="button">
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