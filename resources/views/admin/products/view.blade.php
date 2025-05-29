<div class="modal fade" id="viewProductModal" tabindex="200" role="dialog" aria-labelledby="viewProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="fw-semibold fs-5">{{__('messages.table_elements.details_of')}}<span id="product-name-heading"></span></span>
            </div>
            <div class="modal-body">
                <input type="hidden" id="product_id" name="product_id" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-stripped w-100">
                                <tbody>
                                    <tr>
                                        <td>{{__('messages.table_elements.product_image')}}:</td>
                                        <td><span id="product-img" class="text-center"></span></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.table_elements.product')}}:</td>
                                        <td><span id="product-name" class="text-center"></span></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.table_elements.category')}}:</td>
                                        <td><span id="product-category" class="text-center"></span></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.table_elements.general_price')}}:</td>
                                        <td><span id="product-gen-price" class="text-center"></span></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.table_elements.wholesale_price')}}:</td>
                                        <td><span id="product-wholesale-price" class="text-center"></span></td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.table_elements.extra_price')}}:</td>
                                        <td><span id="product-ex-price" class="text-center"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="description">{{__('messages.table_elements.description')}}</label>
                        <textarea name="description" id="description" class="form-control" cols="30" rows="10"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary me-3" onclick="redirectToEdit();" type="button">
                        {{__('messages.btns.edit')}}
                    </button>
                    <button class="btn btn-secondary" type="button" onclick="closeModal('viewProductModal')">
                        {{__('messages.btns.close')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
