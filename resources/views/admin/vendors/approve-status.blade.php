<div class="modal fade" id="changeApproveStatusModal" tabindex="200" role="dialog" aria-labelledby="changeApproveStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('vendors.approveStatusUpdate')}}" id="changeApproveStatusModalForm" method="post"
                  enctype="multipart/form-data">
                @csrf
                {{--Hidden Field for Id--}}
                <input type="hidden" name="id" id="changeApproveUsersId"/>
                <div class="modal-body mb-0">
                    <div class="row mb-0">
                        <div class="col-12 mb-3">
                            <h5 class="modal-title" id="changeApproveStatusModal">Change User Approve Status</h5>
                        </div>
                        <div class="col-md-12">
                            <select id="approveStatusOption" class="form-control select2" name="approve_status" required>

                            </select>
                        </div>
                        <div class="col-12 text-right mb-0"  style="text-align: right !important; margin-top: 10px">
                            <button class="btn btn-primary mr-1" type="submit">
                                Yes
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="closeModal('changeApproveStatusModal');">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
