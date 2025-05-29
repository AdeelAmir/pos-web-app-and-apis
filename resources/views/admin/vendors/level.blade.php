<div class="modal fade" id="changeBusinessUsersLevelModal" tabindex="200" role="dialog" aria-labelledby="changeBusinessUsersLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('vendors.levelUpdate')}}" id="changeBusinessUsersLevelModalForm" method="post" enctype="multipart/form-data">
                @csrf
                {{--Hidden Field for Id--}}
                <input type="hidden" name="id" id="businessUsersId"/>
                <div class="modal-body mb-0">
                    <div class="row mb-0">
                        <div class="col-12 mb-3">
                            <h5 class="modal-title" id="changeBusinessUsersLevelModalLabel">Change Level</h5>
                        </div>
                        <div class="col-md-12">
                            <select id="level" class="form-control select2" name="level" required>
                                <option value="">Select</option>
                                <option value="Zero level">Zero level</option>
                                <option value="Level 1">Level 1</option>
                                <option value="Level 2">Level 2</option>
                                <option value="Top Rated">Top Rated</option>
                                <option value="Pro level">Pro level</option>
                            </select>
                        </div>
                        <div class="col-12 text-right mb-0"  style="text-align: right !important; margin-top: 10px">
                            <button class="btn btn-primary mr-1" type="submit">
                                Yes
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="closeModal('changeBusinessUsersLevelModal');">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
