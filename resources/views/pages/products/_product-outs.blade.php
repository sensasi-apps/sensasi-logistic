@include('components.assets._datatable')
@include('components.assets._select2')

<div id="productOutCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Product Out List') }}
            <button type="button" class="ml-2 btn btn-danger addProductOutsButton" data-toggle="modal"
                data-target="#productOutFormModal">
                <i class="fas fa-plus-circle"></i> {{ __('Add') }}
            </button>
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="productOutDatatable" style="width:100%">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modal')
    <x-_modal size="lg" id="productOutFormModal" centered>
        <form method="POST" id="productOutForm" onsubmit="return validateInputs();">
            @csrf

            <div class="row">
                <div class="col form-group">
                    <label for="codeOutsInput">{{ __('Code') }}</label>
                    <input type="text" class="form-control" name="code" id="codeOutsInput">
                </div>

                <div class="col form-group">
                    <label for="typeProductOutSelect">{{ __('Type') }}</label>
                    <select id="typeProductOutSelect" name="type" required class="form-control select2"
                        data-select2-opts='{"tags": "true"}'>
                        @foreach ($productOutTypes as $type)
                            <option>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="atProductOutInput">{{ __('Date') }}</label>
                <input type="date" class="form-control" name="at" required id="atProductOutInput">
            </div>

            <div class="form-group">
                <label for="noteOutsInput">{{ __('Note') }}</label>
                <textarea class="form-control" name="note" id="noteOutsInput" rows="3" style="height:100%;"></textarea>
            </div>

            <div class="px-1" style="overflow-x: auto">
                <div id="productOutDetailsParent" style="width: 100%">
                    <div class="row m-0">
                        <label class="col-4">{{ __('Name') }}</label>
                        <label class="col-2">{{ __('Qty') }}</label>
                        <label class="col-3">{{ __('Price') }}</label>
                        <label class="col-2">{{ __('Subtotal') }}</label>
                    </div>
                </div>
            </div>


            <div class="">
                <a href="#" id="addProductOutsButton" class="btn btn-success btn-sm mr-2"><i class="fas fa-plus"></i>
                    {{ __('More') }}</a>
                <a href="#" data-toggle="modal" data-target="#productInFormModal">{{ __('New product in') }}?</a>
            </div>
        </form>

        @slot('footer')
            <div>
                <button type="submit" form="productOutForm" class="btn btn-outline-success">{{ __('Save') }}</button>
            </div>
            <form action="" method="post" id="deleteFormOuts">
                @csrf
                @method('delete')
                <input type="hidden" name="id" id="deleteOutsId">
                <button type="submit" class="btn btn-icon btn-outline-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        @endslot
    </x-_modal>
@endpush

@push('js')
    <script>
        if (productOutCrudDiv) {
            let productOuts
            let productInDetails
            let productOutDatatable = $('#productOutDatatable')
            let selectedProductOutsIds = [];

            const typeProductOutSelect = $('#typeProductOutSelect')
            const productOutsFormModalLabel = $('#productOutsFormModalLabel')

            const datatableSearchProductOuts = tag => productOutDatatable.DataTable().search(tag).draw()
            const renderTagProductOutButton = text =>
                `<a href="#" onclick="datatableSearchProductOuts('${text.split(' ')[0]}')" class="m-1 badge badge-danger">${text}</a>`

            function addProductOutDetailRow(detail) {
                const nDetailInputSet = $('.detailInputSetDiv').length

                const detailRowDiv = document.createElement('div')
                detailRowDiv.setAttribute('class', 'form-group row mx-0 align-items-center detailInputSetDiv')
                productOutDetailsParent.append(detailRowDiv);

                function getProductSelect() {

                    const initProductsSelect = $selectDom => $selectDom.select2({
                        dropdownParent: $('#productOutForm'),
                        placeholder: '{{ __('Product') }}',
                        ajax: {
                            url: '/api/select2/ProductInDetail',
                            dataType: 'json',
                            beforeSend: function(request) {
                                request.setRequestHeader(
                                    "Authorization",
                                    'Bearer {{ Auth::user()->createToken('user_' . Auth::user()->id)->plainTextToken }}'
                                )
                            },
                            processResults: function(data) {
                                productInDetails = data;
                                const theResults = data.map(productInDetail => {

                                    return {
                                        id: productInDetail.id,
                                        text: `${productInDetail.product?.name} (${productInDetail.stock?.qty}) ${moment(productInDetail.at).format('DD-MM-YYYY')}`
                                    }
                                })

                                return {
                                    results: theResults
                                };
                            }
                        },
                        minimumInputLength: 3
                    });

                    const productSelectParentDiv = document.createElement('div')
                    productSelectParentDiv.setAttribute('class', 'col-4 pl-0 pr-2')
                    const $selectDom = $(`<select required placeholder="{{ __('Product name') }}"></select>`)
                        .addClass('form-control productSelect')
                        .attr('name', `details[${nDetailInputSet}][product_in_detail_id]`)
                    $(productSelectParentDiv).append($selectDom)

                    if (detail.product_in_detail_id) {
                        $selectDom.append(
                            `<option value="${detail.product_in_detail_id}">${detail.product_in_detail?.product.name}</option>`
                        );
                    }

                    initProductsSelect($selectDom);
                    $selectDom.val(detail.product_in_detail_id).change();

                    $selectDom.on('select2:select', function() {
                        const i = parseInt(this.getAttribute('name').replace('details[','').replace('][product_in_detail_id]',''))
                        const productInDetail = productInDetails.find(productInDetail => productInDetail.id === parseInt(this.value))

                        document.querySelector(`input[name="details[${i}][price]"]`).value = productInDetail.product?.default_price
                    })

                    return productSelectParentDiv
                }

                function getSubtotalDiv() {
                    const temp = document.createElement('div')
                    temp.setAttribute('class', 'col-2 px-2')

                    const subtotal = detail.qty * detail.price
                    if (subtotal) {
                        temp.innerHTML = subtotal.toLocaleString('id', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0
                        })
                    }

                    return temp;
                }


                $(detailRowDiv).append(getProductSelect())


                const qtyInputParentDiv = document.createElement('div')
                qtyInputParentDiv.setAttribute('class', 'col-2 px-2')
                $(qtyInputParentDiv).append(
                    `<input class="form-control" name="details[${nDetailInputSet}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
                )

                const priceInputParentDiv = document.createElement('div')
                priceInputParentDiv.setAttribute('class', 'col-3 px-2')
                $(priceInputParentDiv).append(
                    `<input class="form-control" name="details[${nDetailInputSet}][price]" min="0" type="number" required placeholder="{{ __('Price') }}" value="${detail.price || detail.productInDetail?.product.default_price || ''}">`
                )

                const getRemoveRowButtonParentDiv = () => {
                    const temp = document.createElement('div')
                    temp.setAttribute('class', 'col-1 pl-2 pr-0')
                    $(temp).append($(
                        `<button class="btn btn-outline-danger btn-icon" tabindex="-1" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>`
                    ))

                    return temp;
                }


                $(detailRowDiv).append(qtyInputParentDiv)
                    .append(priceInputParentDiv)
                    .append(getSubtotalDiv())

                if (nDetailInputSet !== 0) {
                    $(detailRowDiv).append(getRemoveRowButtonParentDiv())
                }
            }

            const deletePutMethodInputProductOuts = () => {
                $('[name="_method"][value="put"]').remove()
            }

            const addPutMethodProductOutsInputInsert = () => {
                $('#productOutForm').append($('@method('put')'))
            }


            const setProductOutFormValue = productOut => {

                if (productOut.type) {
                    const selectOpts = typeProductOutSelect.find('option');
                    const optValues = selectOpts.map((i, select) => select.innerHTML);
                    if ($.inArray(productOut.type, optValues) === -1) {
                        typeProductOutSelect.append(`<option>${productOut.type}</option>`);
                    };
                }

                typeProductOutSelect.val(productOut.type || null).trigger('change');
                codeOutsInput.value = productOut.code || null
                noteOutsInput.value = productOut.note || null
                deleteOutsId.value = productOut.id || null

                atProductOutInput.value = moment(productOut.at).format('YYYY-MM-DD')

                productOut.details?.map(function(detail) {
                    addProductOutDetailRow(detail)
                })
            }


            $(document).on('click', '.addProductOutsButton', function() {
                deletePutMethodInputProductOuts();
                setProductOutFormValue({});
                deleteFormOuts.style.display = "none";

                $('.detailInputSetDiv').remove()


                for (let i = 0; i < 3; i++) {
                    addProductOutDetailRow({})
                }

                productOutFormModal.setTitle('{{ __('Add product out') }}')

                productOutForm.action = "{{ route('product-outs.store') }}"
            });

            $(document).on('click', '.editProductOutButton', function() {
                const productOutId = $(this).data('product-id');
                const productOut = productOutCrudDiv.productOuts.find(productOut => productOut.id === productOutId);

                deleteFormOuts.style.display = "block";

                $('.detailInputSetDiv').remove()

                addPutMethodProductOutsInputInsert();
                setProductOutFormValue(productOut);

                productOutFormModal.setTitle(
                    `{{ __('Edit product out') }}: ${moment(productOut.at).format('DD-MM-YYYY')}`)

                productOutForm.action = "{{ route('product-outs.update', '') }}/" + productOut.id;

                deleteFormOuts.action = "{{ route('product-outs.destroy', '') }}/" + productOut
                    .id;
            })

            $(document).on('click', '#addProductOutsButton', function() {
                addProductOutDetailRow({})
            })

            function validateInputs() {
                const selectedProductOutsIds = []
                let isValid = true;

                $('.text-danger').remove();

                [...document.getElementsByClassName('productSelect')].map(selectEl => {
                    if (selectedProductOutsIds.includes(selectEl.value)) {
                        const errorTextDiv = document.createElement('div');
                        errorTextDiv.innerHTML = '{{ __('Product is duplicated') }}';
                        errorTextDiv.classList.add('text-danger')

                        selectEl.parentNode.append(errorTextDiv)
                        isValid = false;
                    } else {
                        selectedProductOutsIds.push(selectEl.value)
                    }
                })

                return isValid;
            }

            $(document).ready(function() {
                $('#typeProductOutSelect').select2('destroy').select2({
                    tags: true,
                    dropdownParent: $('#productOutForm')
                })

                productOutCrudDiv.productOutDatatable = productOutDatatable.dataTable({
                    processing: true,
                    search: {
                        return: true,
                    },
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'ProductOut') }}?with=details.productInDetail.product',
                        dataSrc: json => {
                            productOutCrudDiv.productOuts = json.data;
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
                    order: [1, 'desc'],
                    columns: [{
                        data: 'code',
                        title: '{{ __('Code') }}'
                    }, {
                        data: 'at',
                        title: '{{ __('At') }}',
                        render: at => moment(at).format('DD-MM-YYYY')
                    }, {
                        data: 'type',
                        title: '{{ __('Type') }}',
                    }, {
                        orderable: false,
                        title: '{{ __('Items') }}',
                        data: 'details',
                        name: 'details',
                        width: '20%',
                        render: details => details.map(detail => renderTagProductOutButton(
                            `${detail.product_in_detail?.product.name} (${detail.qty})`)).join(
                            '')
                    }, {
                        render: function(data, type, row) {
                            const editButton = $(
                                '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                            )
                            editButton.attr('data-toggle', 'modal')
                            editButton.attr('data-target', '#productOutFormModal')
                            editButton.addClass('editProductOutButton');
                            editButton.attr('data-product-id', row.id)
                            return editButton.prop('outerHTML')
                        },
                        orderable: false
                    }]
                });
            });
        }
    </script>
@endpush
