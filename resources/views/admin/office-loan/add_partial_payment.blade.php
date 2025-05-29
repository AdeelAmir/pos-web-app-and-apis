<div class="modal fade" id="changeOfficeLoanPartialPaymentModal" tabindex="200" role="dialog" aria-labelledby="changeOfficeLoanPartialPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('loan.office.partial.payment.store')}}" id="changeOfficeLoanPartialPaymentModalForm" method="post" enctype="multipart/form-data">
                @csrf
                {{--Hidden Field for Id--}}
                <input type="hidden" name="id" id="changeOfficeLoanPartialPaymentId"/>
                <div class="modal-body mb-0">
                    <div class="row mb-0">
                        <div class="col-12 mb-3 text-center">
                            <h5 class="modal-title" id="changeOfficeLoanPartialPaymentModalLabel">{{__('messages.modals.partial_payment_modal.title')}}</h5>
                        </div>
                        <div class="col-md-12">
                            <title for="amount" class="d-block">{{ __('messages.modals.partial_payment_modal.amount') }}</title>
                            <input type="number" name="amount" id="amount" class="form-control">
                        </div>
                        <div class="col-12 d-flex justify-content-center mb-0"  style="margin-top: 10px">
                            <button class="btn btn-primary me-3" type="submit">
                                {{__('messages.modals.partial_payment_modal.yes')}}
                            </button>
                            <button class="btn btn-secondary" type="button" onclick="closeModal('changeOfficeLoanPartialPaymentModal');">
                                {{__('messages.modals.partial_payment_modal.no')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
