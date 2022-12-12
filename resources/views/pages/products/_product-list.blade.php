@include('components.assets._datatable')
@include('components.assets._select2')

<div id="productsCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Product List') }}
            <button type="button" class="ml-2 btn btn-success addProductButton" data-toggle="modal"
                    data-target="#productFormModal">
                <i class="fas fa-plus-circle"></i> {{ __('Add') }}
            </button>
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                   <table class="table table-striped" id="productDatatable" style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <div class="modal fade" id="productFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="productFormModalLabel">{{ __('Add new Product') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_product">

                    <form method="POST" id="productForm">
                        @csrf

                        <input type="hidden" name="id" id="idInput">

                        <div class="form-group">
                            <label for="codeInput">{{ __('validation.attributes.code') }}</label>
                            <input type="text" class="form-control" name="code" id="codeInput">
                        </div>

                        <div class="form-group">
                            <label for="nameInput">{{ __('validation.attributes.name') }}</label>
                            <input type="text" class="form-control" name="name" required id="nameInput">
                        </div>

                        <div class="form-group">
                            <label for="unitInput">{{ __('Unit') }}</label>
                            <input type="text" class="form-control" name="unit" required id="unitInput">
                        </div>

                        <div class="form-group">
                            <label for="priceInput">{{ __('validation.attributes.default_price') }}</label>
                            <input type="number" min="0" class="form-control" name="default_price" required id="priceInput">
                        </div>

                        <div class="form-group">
                            <label for="tagsSelect">{{ __('Tags') }}</label>
                            <select id="tagsSelect" name="tags[]" class="form-control select2" multiple
                                data-select2-opts='{"tags": "true", "tokenSeparators": [",", " "]}'>
                            </select>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between">
                        <button type="submit" form="productForm" class="btn btn-primary">{{ __('Save') }}</button>

                        <button id="deleteFormManufacture" type="submit" class="btn btn-icon btn-outline-danger"
                            data-toggle="tooltip" title="{{ __('Delete') }}"
                            onclick="$('#productDeleteConfirmationModal').modal('show');">
                            <i class="fas fa-trash" style="font-size: 1rem !important"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productDeleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="productFormModalLabel">{{ __('Are you sure') }}?</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_product" style="font-size: 1.1rem">
                    {{ __('This action can not be undone') }}.
                    {{ __('Do you still want to delete') }} <b style="font-size: 1.5rem" id="deleteProductName"></b>
                    <form method="post" id="deleteForm">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="id" id="deleteId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="deleteForm" class="btn btn-danger"
                        id="">{{ __('Yes') }}</button>
                    <button data-dismiss="modal" class="btn btn-secondary" id="">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let products
        const tagsSelect = $('#tagsSelect')
        let productDatatable = $('#productDatatable')

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInput = () => {
            $('#productForm').append($('@method('put')'))
        }

        const setFormValue = product => {
            const selectOpts = tagsSelect.find('option');
            const optValues = selectOpts.map((i, select) => select.innerHTML);

            product.tags?.map(tag => {
                if ($.inArray(tag, optValues) === -1) {
                    tagsSelect.append(`<option>${tag}</option>`);
                };
            })

            tagsSelect.val(product.tags || []).change();
            idInput.value = product.id || null
            nameInput.value = product.name || null
            unitInput.value = product.unit || null
            priceInput.value = product.default_price || null
            codeInput.value = product.code || null
            deleteId.value = product.id
        }


        const datatableSearch = tag =>
            productDatatable.DataTable().search(tag).draw()


        $(document).on('click', '.addProductButton', function() {
            productFormModalLabel.innerHTML = '{{ __('Add new product') }}';


            deletePutMethodInput();
            setFormValue({});

            deleteFormManufacture.style.display = "none";
            productForm.action = "{{ route('products.store') }}";
        })

        $(document).on('click', '.editProductButton', function() {
            productFormModalLabel.innerHTML = '{{ __('Edit Product') }}';

            const productId = $(this).data('product-id');
            const product = productsCrudDiv.products.find(product => product.id === productId);

            setFormValue(product);
            deletePutMethodInput();
            addPutMethodInput();

            deleteFormManufacture.style.display = "block";

            productForm.action = "{{ route('products.update', '') }}/" +
                product
                .id;

            deleteProductName.innerHTML = product.name
            deleteForm.action = "{{ route('products.destroy', '') }}/" + product
                .id;
        });

        $(document).ready(function() {
            productsCrudDiv.productDatatable = $(productDatatable).dataTable({
                processing: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                },
                serverSide: true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'Product') }}',
                    dataSrc: json => {
                        productsCrudDiv.products = json.data;
                        return json.data;
                    },
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            "Authorization",
                            'Bearer {{ Auth::user()->createToken('user_' . Auth::user()->id)->plainTextToken }}'
                        )
                    },
                    cache: true
                },
                columns: [{
                    data: 'code',
                    title: '{{ __('validation.attributes.code') }}'
                }, {
                    data: 'name',
                    title: '{{ __('validation.attributes.name') }}'
                }, {
                    data: 'unit',
                    title: '{{ __('Unit') }}'
                }, {
                    data: 'default_price',
                    title: '{{ __('validation.attributes.default_price') }}'
                }, {
                    data: 'tags',
                    name: 'tags_json',
                    title: '{{ __('Tags') }}',
                    render: data => data?.map(tag =>
                        `<a href="#" onclick="datatableSearch('${tag}')" class="m-1 badge badge-success">${tag}</a>`
                    ).join('') || null,
                }, {
                    render: function(data, type, row) {
                        const editButton = $(
                            '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                        )
                        editButton.attr('data-toggle', 'modal')
                        editButton.attr('data-target', '#productFormModal')
                        editButton.addClass('editProductButton');
                        editButton.attr('data-product-id', row.id)
                        return editButton.prop('outerHTML')
                    },
                    orderable: false
                }]
            });
        });
    </script>
@endpush
