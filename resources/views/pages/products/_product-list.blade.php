@include('components.assets._datatable')
@include('components.assets._select2')

<div id="productsCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Product List') }}
            <button type="button" class="ml-2 btn btn-primary addProductButton" data-toggle="modal"
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

@push('modal')
    <x-_modal id="productFormModal" centered>
        <form method="POST" id="productForm">
            @csrf

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
                <label for="lowQty">{{ __('validation.attributes.low_qty') }}</label>
                <input type="number" min="0" class="form-control" name="low_qty" required id="lowQty">
            </div>
            

            <div class="form-group">
                <label for="tagsSelect">{{ __('Tags') }}</label>
                <select id="tagsSelect" name="tags[]" class="form-control select2" multiple
                    data-select2-opts='{"tags": "true", "tokenSeparators": [",", " "]}'>
                </select>
            </div>
        </form>

        @slot('footer')
            <button type="submit" form="productForm" class="btn btn-primary">{{ __('Save') }}</button>

            <button id="deleteFormManufacture" type="submit" class="btn btn-icon btn-outline-danger" data-toggle="tooltip"
                title="{{ __('Delete') }}" onclick="$('#productDeleteConfirmationModal').modal('show');">
                <i class="fas fa-trash" style="font-size: 1rem !important"></i>
            </button>
        @endslot
    </x-_modal>

    <x-_modal id="productDeleteConfirmationModal" :title="__('Are you sure')" color="danger">
        {{ __('This action can not be undone') }}.
        {{ __('Do you still want to delete') }} <b style="font-size: 1.5rem" id="deleteProductName"></b>
        <form method="post" id="deleteForm">
            @csrf
            @method('delete')
            <input type="hidden" name="id" id="deleteId">
        </form>

        @slot('footer')
            <button type="submit" form="deleteForm" class="btn btn-danger" id="">{{ __('Yes') }}</button>
            <button data-dismiss="modal" class="btn btn-secondary" id="">{{ __('Cancel') }}</button>
        @endslot
    </x-_modal>
@endpush

@push('js')
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
            const optValues = selectOpts.map((i, select) => select.innerText);

            product.tags?.map(tag => {
                if ($.inArray(tag, optValues) === -1) {
                    tagsSelect.append(`<option>${tag}</option>`);
                };
            })

            tagsSelect.val(product.tags || []).change()
            nameInput.value = product.name || null
            unitInput.value = product.unit || null
            priceInput.value = product.default_price || null
            lowQty.value = product.low_qty || null
            codeInput.value = product.code || null
            deleteId.value = product.id
        }


        const datatableSearch = tag =>
            productDatatable.search(tag).draw()


        $(document).on('click', '.addProductButton', function() {

            deletePutMethodInput();
            setFormValue({});

            productFormModal.setTitle('{{ __('Add new product') }}')
            deleteFormManufacture.style.display = "none";
            productForm.action = "{{ route('products.store') }}";
        })

        $(document).on('click', '.editProductButton', function() {
            const productId = $(this).data('product-id');
            const product = productsCrudDiv.products.find(product => product.id === productId);

            setFormValue(product);
            deletePutMethodInput();
            addPutMethodInput();

            productFormModal.setTitle(`{{ __('Edit') }} ${product.name}`)
            deleteFormManufacture.style.display = "block";

            productForm.action = "{{ route('products.update', '') }}/" + product.id;

            deleteProductName.innerText = product.name
            deleteForm.action = "{{ route('products.destroy', '') }}/" + product.id;
        });

        $(document).ready(function() {
            productsCrudDiv.productDatatable = $(productDatatable).DataTable({
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
                    title: '{{ __('validation.attributes.default_price') }}',
                    render: data => data.toLocaleString()
                }, {
                    data: 'tags',
                    name: 'tags_json',
                    title: '{{ __('Tags') }}',
                    render: data => data?.map(tag =>
                        `<a href="#" onclick="datatableSearch('${tag}')" class="m-1 badge badge-primary">${tag}</a>`
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
