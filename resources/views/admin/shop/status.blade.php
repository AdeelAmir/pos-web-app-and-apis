<div class="modal fade" id="changeSellerStatusModal" tabindex="200" role="dialog" aria-labelledby="changeSellerStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('sellers.statusUpdate')}}" id="changeSellerStatusModalForm" method="post"
                  enctype="multipart/form-data">
                @csrf
                {{--Hidden Field for Id--}}
                <input type="hidden" name="id" id="changeSellerStatusId"/>
                <div class="modal-body mb-0">
                    <div class="row mb-0">
                        <div class="col-12 mb-3 text-center">
                            <h5 class="modal-title" id="changeSellerStatusModalLabel">Change Seller Status</h5>
                        </div>
                        <div class="col-md-12">
                            <select id="statusOption" class="form-control select2" name="status" required>

                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-center mb-0"  style="margin-top: 10px">
                            <button class="btn btn-primary me-3" type="submit">
                                Yes
                            </button>
                            <button class="btn btn-secondary" type="button" onclick="closeModal('changeSellerStatusModal');">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
