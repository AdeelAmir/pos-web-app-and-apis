<div class="modal custom-modal fade" id="printSaleModel" tabindex="200" role="dialog" aria-labelledby="printSaleLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body d-flex flex-column justify-content-center">
                <i class="fa fa-print text-primary modal-icon text-center mb-3"></i>
                <span class="text-dark fs-5 font-bold text-center mb-3">{{__('messages.modals.print_modal.title')}}</span>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary me-3" onclick="" type="button">
                        {{__('messages.modals.print_modal.yes')}}
                    </button>
                    <button class="btn btn-secondary" type="button" onclick="closeModal('printSaleModel')">
                        {{__('messages.modals.print_modal.no')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>