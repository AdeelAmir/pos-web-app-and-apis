<div class="modal fade" id="addStockModal" tabindex="200" role="dialog" aria-labelledby="addStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="fw-semibold fs-5">{{__('messages.add_stock')}}</span>
            </div>
            <div class="modal-body">
                <form action="{{ route('products.stock.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="id" name="product_id">
                    <div class="col-md-12 mb-3">
                        <label for="box">{{__('messages.product_box')}} <span class="text-danger">*</span></label>
                        <input type="number" name="box" id="box" onblur="CalculateStock()" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="stock">{{__('messages.num_of_stock')}} <span class="text-danger">*</span></label>
                        <input type="number" name="stock" id="stock" class="form-control" min="0" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary me-3" type="submit">
                            {{__('messages.btns.add')}}
                        </button>
                        <button class="btn btn-secondary" type="button" onclick="closeModal('addStockModal')">
                            {{__('messages.btns.close')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>