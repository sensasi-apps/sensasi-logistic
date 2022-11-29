@include('components.assets._datatable')
@include('components.assets._select2')

    <div class="section-body">
        <h2 class="section-title">
            {{ __('Product In List') }}
            <button type="button" class="ml-2 btn btn-success addProductInsButton" data-toggle="modal"
                data-target="#productInsertFormModal">
                <i class="fas fa-plus-circle"></i> Tambah
            </button>
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="productInDatatable" style="width:100%">
                    </table>
                </div>
            </div>
        </div>
    </div>

@push('js')
    <div class="modal fade" id="productInsertFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="productInsFormModalLabel">{{ __('Add new Product in') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_product">

                    <form method="POST" id="productInForm" onsubmit="return validateInputs();">
                        @csrf

                        <input type="hidden" name="id" id="idIns">


                        <div class="row">
                            <div class="col form-group">
                                <label for="codeInsInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="code" id="codeInsInput">
                            </div>

                            <div class="col form-group">
                                <label for="typeProductInsSelect">{{ __('Type') }}</label>
                                <select id="typeProductInsSelect" name="type" required class="form-control select2"
                                    data-select2-opts='{"tags": "true"}'>
                                    @foreach ($productInTypes as $type)
                                        <option>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="atInput">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="at" required id="atInput">
                        </div>

                        <div class="form-group">
                            <label for="noteInsInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="note" id="noteInsInput" rows="3" style="height:100%;"></textarea>
                        </div>

                        <div class="px-1" style="overflow-x: auto">
                            <div id="productInDetailsParent" style="width: 100%">
                                <div class="row m-0">
                                    <label class="col-7">{{ __('Name') }}</label>
                                    <label class="col-4">{{ __('Qty') }}</label>
                                </div>
                            </div>
                        </div>


                        <div class="">
                            <a href="#" id="addProductButton" class="btn btn-success btn-sm mr-2"><i
                                    class="fas fa-plus"></i> {{ __('More') }}</a>
                            <a href="{{ route('products.index') }}">{{ __('New product') }}?</a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" form="productInForm"
                            class="btn btn-outline-success">{{ __('Save') }}</button>
                    </div>
                    <form action="" method="post" id="deleteForm">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="id" id="deleteInsId">
                        <button type="submit" class="btn btn-icon btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let productIns
        let productInDatatable = $('#productInDatatable')
        let selectedProductInsIds = [];

        const typeProductInsSelect = $('#typeProductInsSelect')
        const productInsFormModalLabel = $('#productInsFormModalLabel')

        const datatableSearchProductIns = tag => productInDatatable.DataTable().search(tag).draw()
        const renderTagProductInsButton = text =>
            `<a href="#" onclick="datatableSearchProductIns('${text.split(' ')[0]}')" class="m-1 badge badge-primary">${text}</a>`

        function addProductInDetailRow(detail) {
            const nDetailInputSet = $('.detailInputSetDiv').length

            const detailRowDiv = document.createElement('div')
            detailRowDiv.setAttribute('class', 'form-group row mx-0 align-items-center detailInputSetDiv')
            productInDetailsParent.append(detailRowDiv);

            function getProductSelect () {
                const products = {{ Js::from(App\Models\Product::all()) }};

                const initProductsSelect = $selectDom => $selectDom.select2({
                    dropdownParent: $('#modal_body_product'),
                    placeholder: '{{ __('Product') }}',
                    data: [{
                        id: null,
                        text: null
                    }].concat(products.map(product => {
                        return {
                            id: product.id,
                            text: product.name
                        }
                    }))
                });

                const productSelectParentDiv = document.createElement('div')
                productSelectParentDiv.setAttribute('class', 'col-7 pl-0 pr-2')
                const $selectDom = $(`<select required placeholder="{{ __('Product name') }}"></select>`)
                    .addClass('form-control productSelect')
                    .attr('name', `details[${nDetailInputSet}][product_id]`)
                $(productSelectParentDiv).append($selectDom)
                console.log(detail)
                initProductsSelect($selectDom);
                $selectDom.val(detail.product_id).change();

                return productSelectParentDiv
            }


            $(detailRowDiv).append(getProductSelect())


            const qtyInputParentDiv = document.createElement('div')
            qtyInputParentDiv.setAttribute('class', 'col-4 px-2')
            $(qtyInputParentDiv).append(
                `<input class="form-control" name="details[${nDetailInputSet}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
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

            if (nDetailInputSet !== 0) {
                $(detailRowDiv).append(getRemoveRowButtonParentDiv())
            }
        }

        const deletePutMethodInputProductIns = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodProductInsInputInsert = () => {
            $('#productInForm').append($('@method('put')'))
        }


        const setProductInFormValue = productIn => {

            if (productIn.type) {
                const selectOpts = typeProductInsSelect.find('option');
                const optValues = selectOpts.map((i, select) => select.innerHTML);
                if ($.inArray(productIn.type, optValues) === -1) {
                    typeProductInsSelect.append(`<option>${productIn.type}</option>`);
                };
            }

            typeProductInsSelect.val(productIn.type || null).trigger('change');
            idIns.value = productIn.id || null
            codeInsInput.value = productIn.code || null
            noteInsInput.value = productIn.note || null
            deleteInsId.value = productIn.id || null

            if (productIn.at) {
                const dateObj = new Date(productIn.at);

                const month = dateObj.getMonth() + 1; //months from 1-12
                const day = dateObj.getDate();
                const year = dateObj.getFullYear();

                atInput.value = `${year}-${month}-${day}`
            } else {
                atInput.value = '{{ date('Y-m-d') }}'
            }

            productIn.details?.map(function(detail) {
                addProductInDetailRow(detail)
            })
        }


        $(document).on('click', '.addProductInsButton', function() {
            deletePutMethodInputProductIns();
            setProductInFormValue({});
            deleteForm.style.display = "none";

            $('.detailInputSetDiv').remove()


            addProductInDetailRow({})
            addProductInDetailRow({})
            addProductInDetailRow({})

            productInForm.action = "{{ route('product-ins.store') }}";
        });

        $(document).on('click', '.editProductInsertButton', function() {
            const productInId = $(this).data('product-id');
            const productIn = productIns.find(productIn => productIn.id === productInId);
            deleteForm.style.display = "block";

            $('.detailInputSetDiv').remove()

            addPutMethodProductInsInputInsert();
            setProductInFormValue(productIn);

            productInForm.action = "{{ route('product-ins.update', '') }}/" + productIn.id;

            deleteForm.action = "{{ route('product-ins.destroy', '') }}/" + productIn
                .id;
        })

        $(document).on('click', '#addProductButton', function() {
            addProductInDetailRow({})
        })

        function validateInputs() {
            const selectedProductInsIds = []
            let isValid = true;

            $('.text-danger').remove();

            [...document.getElementsByClassName('productSelect')].map(selectEl => {
                if (selectedProductInsIds.includes(selectEl.value)) {
                    const errorTextDiv = document.createElement('div');
                    errorTextDiv.innerHTML = '{{ __('Product is duplicated') }}';
                    errorTextDiv.classList.add('text-danger')

                    selectEl.parentNode.append(errorTextDiv)
                    isValid = false;
                } else {
                    selectedProductInsIds.push(selectEl.value)
                }
            })
            
            return isValid;
        }

        $(document).ready(function() {
            $('#typeProductInsSelect').select2('destroy').select2({
                tags: true,
                dropdownParent: $('#modal_body_product')
            })

            productInDatatable = productInDatatable.dataTable({
                processing: true,
                search: {
                    return: true,
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                },
                serverSide: true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'ProductIn') }}?with=details.product',
                    dataSrc: json => {
                        productIns = json.data;
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
                    name: 'details.product.name',
                    width: '20%',
                    render: details => details.map(detail => renderTagProductInsButton(
                        `${detail.product?.name} (${detail.qty})`)).join('')
                }, {
                    render: function(data, type, row) {
                        const editButton = $(
                            '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                        )
                        editButton.attr('data-toggle', 'modal')
                        editButton.attr('data-target', '#productInsertFormModal')
                        editButton.addClass('editProductInsertButton');
                        editButton.attr('data-product-id', row.id)
                        return editButton.prop('outerHTML')
                    },
                    orderable: false
                }]
            });
        });
    </script>
@endpush
