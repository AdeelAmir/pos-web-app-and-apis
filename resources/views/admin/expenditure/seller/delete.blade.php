<div class="modal fade" id="deleteExpenditureSellerModel" tabindex="200" role="dialog" aria-labelledby="deleteExpenditureSellerModelLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteExpenditureSellerModalLabel">Delete Expenditure Seller</h5>
            </div>
            <div class="modal-body">
                <form action="" id="deleteExpenditureOfficeForm">
                    <div class="form-row">
                        <div class="col-md-12">
                            <p style="font-size: 1.5em;">Are you sure you want to delete this expenditure seller?</p>
                        </div>
                        {{--Hidden Field for Id--}}
                        <input type="hidden" name="deleteExpenditureSellerId" id="deleteExpenditureSellerId" value="0" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="ConfirmDeleteExpenditureSeller(this);" type="button">
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