@include('components.assets._datatable')
@include('components.assets._select2')

<div id="manufactureCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Manufacture List') }}
            <button type="button" class="ml-2 btn btn-primary addManufactureButton" data-toggle="modal"
                data-target="#manufactureFormModal">
                <i class="fas fa-plus-circle"></i> Tambah
            </button>
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="materialOutDatatable" style="width:100%">
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modal')
    <x-_modal id="manufactureFormModal" size="xl" :title="__('Add new manufacture')" centered>
        <form method="POST" id="manufactureform">
            @csrf
            <input type="hidden" name="manufacture[id]" id="manufactureId">

            <div class="row">
                <div class="col form-group">
                    <label for="codeManufactureInput">{{ __('Code') }}</label>
                    <input type="text" class="form-control" name="manufacture[code]" id="codeManufactureInput">
                </div>

                <div class="col form-group">
                    <label for="atManufactureInput">{{ __('Date') }}</label>
                    <input type="date" class="form-control" name="manufacture[at]" required id="atManufactureInput">
                </div>
            </div>

            <div class="form-group">
                <label for="noteManufactureInput">{{ __('Note') }}</label>
                <textarea class="form-control" name="manufacture[note]" id="noteManufactureInput" rows="3" style="height:100%;"></textarea>
            </div>


            <div class="row">
                <div class="col">
                    <h5 class="mb-3">{{ __('Materials') }}</h5>

                    <div class="px-1" style="overflow-x: auto">
                        <div id="materialOutDetailsParent" style="width: 100%">
                            <div class="row m-0">
                                <label class="col-7">{{ __('Name') }}</label>
                                <label class="col-4">{{ __('Qty') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="">
                        <a href="#" id="addMaterialOutDetailButton" class="btn btn-success btn-sm mr-2"><i
                                class="fas fa-plus"></i> {{ __('More') }}</a>
                        <a href="{{ route('materials.index') }}">{{ __('New material') }}?</a>
                    </div>
                </div>

                <div class="col">
                    <h5 class="mb-3">{{ __('Products') }}</h5>

                    <div class="px-1" style="overflow-x: auto">
                        <div id="productInDetailsParent" style="width: 100%">
                            <div class="row m-0">
                                <label class="col-7">{{ __('Name') }}</label>
                                <label class="col-4">{{ __('Qty') }}</label>
                            </div>
                        </div>
                    </div>


                    <div class="">
                        <a href="#" id="addProductInsButton" class="btn btn-success btn-sm mr-2"><i
                                class="fas fa-plus"></i> {{ __('More') }}</a>
                        <a href="{{ route('products.index') }}">{{ __('New product') }}?</a>
                    </div>
                </div>

            </div>


            @slot('footer')
                <div>
                    <button type="submit" form="manufactureform" class="btn btn-outline-success">{{ __('Save') }}</button>
                </div>
                <form action="" method="post" id="deleteManufacture">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-icon btn-outline-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endslot
        </form>
    </x-_modal>

    {{-- <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="manufactureFormModalLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="manufactureModalBody">


                </div>
                <div class="modal-footer d-flex justify-content-between">

                </div>
            </div>
        </div>
    </div> --}}
@endpush

@push('js')
    <script>
        {
            if (manufactureCrudDiv) {
                // const manufactureFormModalLabel = $('#manufactureFormModalLabel')

                // $(function() {
                //     $('#materialOutTypeSelect').select2({
                //         tags: true,
                //         dropdownParent: $('#manufactureModalBody')
                //     })
                // })

                const renderTagMaterialOutButton = text =>
                    `<a href="#" onclick="datatableSearch('${text.split(' ')[0]}')" class="m-1 badge badge-danger">${text}</a>`

                const renderTagProductInButton = text =>
                    `<a href="#" onclick="datatableSearch('${text.split(' ')[0]}')" class="m-1 badge badge-success">${text}</a>`

                const initMaterialSelects = $selectDom => $selectDom.select2({
                    dropdownParent: $('#manufactureFormModal .modal-body'),
                    placeholder: '{{ __('Material') }}',

                    ajax: {
                        url: '/api/select2/MaterialInDetail',
                        dataType: 'json',
                        beforeSend: function(request) {
                            request.setRequestHeader(
                                "Authorization",
                                'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                            )
                        },
                        processResults: function(data) {
                            const theResults = data.map(materialInDetail => {

                                return {
                                    id: materialInDetail.id,
                                    text: `${materialInDetail.material?.name} (${parseInt(materialInDetail.stock?.qty)}) ${moment(materialInDetail.material_in.at).format('DD-MM-YYYY')}`
                                }
                            })

                            return {
                                results: theResults
                            };
                        }
                    },
                    minimumInputLength: 3
                });

                function addMaterialOutDetailRow(detail) {
                    const nDetailInputSetMaterialOut = $('.materialOutDetailRowDiv').length
                    const materialSelectParentDiv = document.createElement('div')
                    materialSelectParentDiv.setAttribute('class', 'col-6 pl-0 pr-2')
                    const $selectDomMaterialOut = $(`<select required placeholder="{{ __('Material name') }}"></select>`)
                        .addClass('form-control select2 listSelect')
                        .attr('name', `detailsMaterialOut[${nDetailInputSetMaterialOut}][material_in_detail_id]`)
                    $(materialSelectParentDiv).append($selectDomMaterialOut)

                    if (detail.material_in_detail_id) {
                        $selectDomMaterialOut.append(
                            `<option value="${detail.material_in_detail_id}" selected>${detail.material_in_detail?.material.name}</option>`
                        );
                    }

                    initMaterialSelects($selectDomMaterialOut);
                    $selectDomMaterialOut.val(detail.material_in_detail_id).change();

                    const qtyInputParentDiv = document.createElement('div')
                    qtyInputParentDiv.setAttribute('class', 'col-5 px-2')
                    qtyInputParentDiv.innerHTML = `<input class="form-control" name="detailsMaterialOut[${nDetailInputSetMaterialOut}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class',
                        'form-group row materialOutDetailRowDiv mx-0 align-items-center')
                    $(detailRowDiv).append(materialSelectParentDiv)
                    $(detailRowDiv).append(qtyInputParentDiv)
                    // $(detailRowDiv).append(`<input type="hidden" name="" value="${detail.id}">`)

                    materialOutDetailsParent.append(detailRowDiv);
                    if (nDetailInputSetMaterialOut !== 0) {
                        const removeRowButtonParentDiv = document.createElement('div')
                        removeRowButtonParentDiv.setAttribute('class', 'col-1 pl-2 pr-0')
                        $(removeRowButtonParentDiv).append($(
                            '<button class="btn btn-outline-danger btn-icon" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>'
                        ))

                        $(detailRowDiv).append(removeRowButtonParentDiv)
                    }
                }


                const setManufactureFormValue = manufacture => {
                    codeManufactureInput.value = manufacture.code || null
                    noteManufactureInput.value = manufacture.note || null

                    atManufactureInput.value = moment(manufacture.at).format('YYYY-MM-DD')

                    $('div .detailInputSetProductInDiv').remove()
                    $('div .materialOutDetailRowDiv').remove()

                    if (manufacture.material_out?.details) {
                        manufacture.material_out.details.map(function(detail) {
                            addMaterialOutDetailRow(detail)
                        })
                    } else {
                        addMaterialOutDetailRow({})
                    }

                    $('div .addProductInDetailRow').remove()
                    if (manufacture.product_in?.details) {
                        manufacture.product_in.details?.map(function(detail) {
                            addProductInDetailRow(detail)
                        })
                    } else {
                        addProductInDetailRow({})
                    }
                }

                function addProductInDetailRow(detail) {

                    const nDetailInputSetProductIn = $('.detailInputSetProductInDiv').length

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class', 'form-group row mx-0 align-items-center detailInputSetProductInDiv')
                    productInDetailsParent.append(detailRowDiv);

                    function getProductSelect() {
                        const products = {{ Js::from(App\Models\Product::all()) }};

                        const initProductsSelect = $selectDomProductIn => $selectDomProductIn.select2({
                            dropdownParent: $('#manufactureFormModal .modal-body'),
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
                        const $selectDomProductIn = $(`<select required placeholder="{{ __('Product name') }}"></select>`)
                            .addClass('form-control productSelect')
                            .attr('name', `detailsProductIn[${nDetailInputSetProductIn}][product_id]`)
                        $(productSelectParentDiv).append($selectDomProductIn)
                        initProductsSelect($selectDomProductIn);
                        $selectDomProductIn.val(detail.product_id).change();

                        return productSelectParentDiv
                    }


                    $(detailRowDiv).append(getProductSelect())


                    const qtyInputParentDiv = document.createElement('div')
                    qtyInputParentDiv.setAttribute('class', 'col-4 px-2')
                    $(qtyInputParentDiv).append(
                        `<input class="form-control" name="detailsProductIn[${nDetailInputSetProductIn}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
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

                    if (nDetailInputSetProductIn !== 0) {
                        $(detailRowDiv).append(getRemoveRowButtonParentDiv())
                    }
                }


                $(document).on('click', '.addManufactureButton', function() {
                    $('[name="_method"][value="put"]').remove()

                    setManufactureFormValue({});
                    deleteManufacture.style.display = "none";

                    manufactureform.action = "{{ route('manufactures.store') }}";
                });

                $(document).on('click', '.editManufactureButton', function() {
                    const manufactureId = $(this).data('manufacture-id');
                    const manufacture = manufactureCrudDiv.manufactures.find(manufacture => manufacture.id ===
                        manufactureId);

                    deleteManufacture.style.display = "block";

                    $('#manufactureform').append($('@method('put')'))

                    setManufactureFormValue(manufacture);

                    manufactureform.action = `{{ route('manufactures.update', '') }}/${manufacture.id}`;
                    deleteManufacture.action = `{{ route('manufactures.destroy', '') }}/${manufacture.id}`;
                })

                $(document).on('click', '#addMaterialOutDetailButton', function() {
                    addMaterialOutDetailRow({})
                })

                $(document).on('click', '#addProductInsButton', function() {
                    addProductInDetailRow({})
                })

                // ################## DATATABLE SECTION

                const datatableSearch = tag => manufactureCrudDiv.materialOutDatatable.DataTable().search(tag).draw()

                manufactureCrudDiv.materialOutDatatable = $(materialOutDatatable).dataTable({
                    processing: true,
                    search: {
                        return: true,
                    },
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                    },
                    serverSide: true,
                    ajax: {
                        url: "/api/datatable/Manufacture?with=productIn.details.product,materialOut.details.materialInDetail.material",
                        dataSrc: json => {
                            manufactureCrudDiv.manufactures = json.data;
                            return json.data;
                        },
                        beforeSend: function(request) {
                            request.setRequestHeader(
                                "Authorization",
                                'Bearer {{ decrypt(request()->cookie('api-token')) }}'
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
                        orderable: false,
                        title: '{{ __('Material Out') }}',
                        data: 'material_out.details',
                        name: 'details',
                        width: '20%',
                        render: details => details.map(detail => renderTagMaterialOutButton(
                            `${detail.material_in_detail.material.name} (${detail.qty})`)).join('')
                    }, {
                        orderable: false,
                        title: '{{ __('Product In') }}',
                        data: 'product_in.details',
                        name: 'details',
                        width: '20%',
                        render: details => details.map(detail => renderTagProductInButton(
                            `${detail.product.name} (${detail.qty})`)).join('')
                    }, {
                        render: function(data, type, row) {
                            const editButton = $(
                                '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                            )
                            editButton.attr('data-toggle', 'modal')
                            editButton.attr('data-target', '#manufactureFormModal')
                            editButton.addClass('editManufactureButton');
                            editButton.attr('data-manufacture-id', row.id)
                            return editButton.prop('outerHTML')
                        },
                        orderable: false
                    }]
                })
            }
        }
    </script>
@endpush
