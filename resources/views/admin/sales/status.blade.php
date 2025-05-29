<div class="modal fade" id="changeSaleStatusModal" tabindex="200" role="dialog" aria-labelledby="changeSaleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('sales.statusUpdate')}}" id="changeSaleStatusModalForm" method="post"
                  enctype="multipart/form-data">
                @csrf
                {{--Hidden Field for Id--}}
                <input type="hidden" name="id" id="changeSaleStatusId"/>
                <div class="modal-body mb-0">
                    <div class="row mb-0">
                        <div class="col-12 mb-3 text-center">
                            <h5 class="modal-title" id="changeSaleStatusModalLabel">{{__('messages.modals.sale_modal.title')}}</h5>
                        </div>
                        <div class="col-md-12">
                            <select id="statusOption" class="form-control select2" name="status" required>

                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-center mb-0"  style="margin-top: 10px">
                            <button class="btn btn-primary me-3" type="submit">
                                {{__('messages.modals.sale_modal.yes')}}
                            </button>
                            <button class="btn btn-secondary" type="button" onclick="closeModal('changeSaleStatusModal');">
                                {{__('messages.modals.sale_modal.no')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
