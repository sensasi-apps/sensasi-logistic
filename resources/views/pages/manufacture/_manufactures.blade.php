@include('components.assets._datatable')
@include('components.assets._select2')

<div id="manufactureCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material Out List') }}
            <button type="button" class="ml-2 btn btn-success addManufactureButton" data-toggle="modal"
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
    <div class="modal fade" id="manufactureFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="manufactureFormModalLabel">{{ __('Add new manufacture') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="manufactureModalBody">

                    <form method="POST" id="manufactureform">
                        @csrf
                        <h4 class="text-center">{{__('Manufacture')}}</h4>
                        <hr>
                        <input type="hidden" name="manufacture[id]" id="manufactureId">


                        <div class="row">
                            <div class="col form-group">
                                <label for="codeManufactureInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="manufacture[code]" id="codeManufactureInput">
                            </div>

                            <div class="form-group">
                                <label for="atManufactureInput">{{ __('Date') }}</label>
                                <input type="date" class="form-control" name="manufacture[at]" required id="atManufactureInput">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="noteManufactureInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="manufacture[note]" id="noteManufactureInput" rows="3" style="height:100%;"></textarea>
                        </div><hr>

                        <h4 class="text-center">{{__('Material Out')}}</h4>

                        <input type="hidden" name="materialOut[id]" id="materialOutIdInput">


                        <div class="row">
                            <div class="col form-group">
                                <label for="materialOutCodeInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="materialOut[code]" id="materialOutCodeInput">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="materialOutAtInput">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="materialOut[at]" required id="materialOutAtInput">
                        </div>

                        <div class="form-group">
                            <label for="materialOutNoteInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="materialOut[note]" id="materialOutNoteInput" rows="3" style="height:100%;"></textarea>
                        </div>

                        <div class="row">
                            <div class="col form-group">
                                <label for="materialOutDescInput">{{ __('Desc') }}</label>
                                <input type="text" class="form-control" name="materialOut[desc]" id="materialOutDescInput">
                            </div>
                        </div>

                        <div class="px-1" style="overflow-x: auto">
                            <div id="materialOutDetailsParent" style="width: 100%">
                                <div class="row m-0">
                                    <label class="col-6">{{ __('Name') }}</label>
                                    <label class="col-5">{{ __('Qty') }}</label>
                                </div>
                            </div>
                        </div>


                        <div class="">
                            <a href="#" id="addMaterialOutDetailButton" class="btn btn-success btn-sm mr-2"><i
                                    class="fas fa-plus"></i> {{ __('More') }}</a>
                            <a href="{{ route('materials.index') }}">{{ __('New material') }}?</a>
                        </div><hr>

                        <h4 class="text-center">{{__('Product In')}}</h4>

                        <input type="hidden" name="productIn[id]" id="idIns">


                        <div class="row">
                            <div class="col form-group">
                                <label for="codeInsInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="productIn[code]" id="codeInsInput">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="atInput">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="productIn[at]" required id="atInput">
                        </div>

                        <div class="form-group">
                            <label for="noteInsInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="productIn[note]" id="noteInsInput" rows="3" style="height:100%;"></textarea>
                        </div>

                        <div class="row">
                            <div class="col form-group">
                                <label for="descInsInput">{{ __('Desc') }}</label>
                                <input type="text" class="form-control" name="productIn[desc]" id="descInsInput">
                            </div>
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
                            <a href="#" id="addProductInsButton" class="btn btn-success btn-sm mr-2"><i
                                    class="fas fa-plus"></i> {{ __('More') }}</a>
                            <a href="{{ route('products.index') }}">{{ __('New product') }}?</a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" form="manufactureform"
                            class="btn btn-outline-success">{{ __('Save') }}</button>
                    </div>
                    <form action="" method="post" id="deleteManufacture">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-icon btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('js')
    <script>
        {
            if (manufactureCrudDiv) {
                const manufactureFormModalLabel = $('#manufactureFormModalLabel')

                $(function() {
                    $('#materialOutTypeSelect').select2({
                        tags: true,
                        dropdownParent: $('#manufactureModalBody')
                    })
                })

                const renderTagMaterialOutButton = text =>
                    `<a href="#" onclick="datatableSearch('${text.split(' ')[0]}')" class="m-1 badge badge-danger">${text}</a>`

                const renderTagProductInButton = text =>
                    `<a href="#" onclick="datatableSearch('${text.split(' ')[0]}')" class="m-1 badge badge-primary">${text}</a>`

                const initMaterialSelects = $selectDom => $selectDom.select2({
                    dropdownParent: $('#manufactureModalBody'),
                    placeholder: '{{ __('Material') }}',
                    ajax: {
                        url: '/api/select2/MaterialInDetail',
                        dataType: 'json',
                        beforeSend: function(request) {
                            request.setRequestHeader(
                                "Authorization",
                                'Bearer {{ Auth::user()->createToken('user_' . Auth::user()->id)->plainTextToken }}'
                            )
                        },
                        processResults: function(data) {
                            const theResults = data.map(materialInDetail => {

                                return {
                                    id: materialInDetail.id,
                                    text: `${materialInDetail.material?.name} (${materialInDetail.stock?.qty}) ${moment(materialInDetail.material_in.at).format('DD-MM-YYYY')}`
                                }
                            })

                            return {
                                results: theResults
                            };
                        }
                    },
                    minimumInputLength: 3
                });

                function removeMaterialOutDetails() {
                }

                function addMaterialOutDetailRow(detail) {
                    const nDetailInputSetMaterialOut = $('.detailInputSetMaterialOutDiv').length
                    const materialSelectParentDiv = document.createElement('div')
                    materialSelectParentDiv.setAttribute('class', 'col-6 pl-0 pr-2')
                    const $selectDomMaterialOut = $(`<select required placeholder="{{ __('Material name') }}"></select>`)
                        .addClass('form-control select2 listSelect')
                        .attr('name', `detailsMaterialOut[${nDetailInputSetMaterialOut}][material_in_detail_id]`)
                    $(materialSelectParentDiv).append($selectDomMaterialOut)

                    if (detail.material_in_detail_id) {
                        $selectDomMaterialOut.append(
                            `<option value="${detail.material_in_detail_id}">${detail.material_in_detail?.material.name}</option>`
                        );
                    }

                    initMaterialSelects($selectDomMaterialOut);
                    $selectDomMaterialOut.val(detail.material_in_detail_id).change();


                    const qtyInputParentDiv = document.createElement('div')
                    qtyInputParentDiv.setAttribute('class', 'col-5 px-2')
                    $(qtyInputParentDiv).append(
                        `<input class="form-control" name="detailsMaterialOut[${nDetailInputSetMaterialOut}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
                    )

                    const removeRowButtonParentDiv = document.createElement('div')
                    removeRowButtonParentDiv.setAttribute('class', 'col-1 pl-2 pr-0')
                    $(removeRowButtonParentDiv).append($(
                        '<button class="btn btn-outline-danger btn-icon" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>'
                    ))

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class', 'form-group row materialOutDetailRowDiv mx-0 align-items-center detailInputSetMaterialOutDiv')
                    $(detailRowDiv).append(materialSelectParentDiv)
                    $(detailRowDiv).append(qtyInputParentDiv)
                    // $(detailRowDiv).append(removeRowButtonParentDiv)
                    $(detailRowDiv).append(`<input type="hidden" name="" value="${detail.id}">`)

                    materialOutDetailsParent.append(detailRowDiv);
                    if (nDetailInputSetMaterialOut !== 0) {
                        $(detailRowDiv).append(removeRowButtonParentDiv)
                    }
                }


                const setmanufactureFormValue = manufacture => {
                    // const materialOutTypeSelect = $('#materialOutTypeSelect');
                    // materialOutTypeSelect.val(manufacture.type || null).change();

                    manufactureId.value = manufacture.id || null
                    codeManufactureInput.value = manufacture.code || null
                    noteManufactureInput.value = manufacture.note || null

                    if (manufacture.at) {
                        let dateObjManufacture = new Date(manufacture.at);

                        let monthManufacture = dateObjManufacture.getMonth() + 1; //months from 1-12
                        let dayManufacture = dateObjManufacture.getDate();
                        let yearManufacture = dateObjManufacture.getFullYear();

                        if (dayManufacture < 10) {
                            dayManufacture = '0'+ dayManufacture
                        }

                        atManufactureInput.value = `${yearManufacture}-${monthManufacture}-${dayManufacture}`
                    } else {
                        atManufactureInput.value = '{{ date('Y-m-d') }}'
                    }

                    materialOutIdInput.value = manufacture.material_out.id
                    materialOutNoteInput.value = manufacture.material_out.note
                    materialOutCodeInput.value = manufacture.material_out.code
                    materialOutAtInput.value = manufacture.material_out.at
                    materialOutDescInput.value = manufacture.material_out.desc
                    
                    if (manufacture.material_out.at) {
                        let dateObjMaterialOut = new Date(manufacture.material_out.at);

                        let monthMaterialOut = dateObjMaterialOut.getMonth() + 1; //months from 1-12
                        let dayMaterialOut = dateObjMaterialOut.getDate();
                        let yearMaterialOut = dateObjMaterialOut.getFullYear();

                        if (dayMaterialOut < 10) {
                            dayMaterialOut = '0' + dayMaterialOut
                        }

                        materialOutAtInput.value = `${yearMaterialOut}-${monthMaterialOut}-${dayMaterialOut}`
                    } else {
                        materialOutAtInput.value = '{{ date('Y-m-d') }}'
                    }

                    idIns.value = manufacture.product_in.id
                    codeInsInput.value = manufacture.product_in.code
                    atInput.value = manufacture.product_in.at
                    noteInsInput.value = manufacture.product_in.note
                    descInsInput.value = manufacture.product_in.desc

                    if (manufacture.material_out.at) {
                        let dateObjProductIn = new Date(manufacture.material_out.at);

                        let monthProductIn = dateObjProductIn.getMonth() + 1; //months from 1-12
                        let dayProductIn = dateObjProductIn.getDate();
                        let yearProductIn = dateObjProductIn.getFullYear();

                        if (dayProductIn < 10) {
                            dayProductIn = '0' + dayProductIn
                        }

                        atInput.value = `${yearProductIn}-${monthProductIn}-${dayProductIn}`
                    } else {
                        atInput.value = '{{ date('Y-m-d') }}'
                    }

                    manufacture.material_out.details?.map(function(detail) {
                        addMaterialOutDetailRow(detail)
                    })

                    manufacture.product_in.details?.map(function(detail) {
                        addProductInDetailRow(detail)
                    })
                }

                function addProductInDetailRow(detail) {
                    const nDetailInputSetProductIn = $('.detailInputSetProductInDiv').length

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class', 'form-group row mx-0 align-items-center detailInputSetProductInDiv')
                    productInDetailsParent.append(detailRowDiv);

                    function getProductSelect () {
                        const products = {{ Js::from(App\Models\Product::all()) }};

                        const initProductsSelect = $selectDomProductIn => $selectDomProductIn.select2({
                            dropdownParent: $('#manufactureModalBody'),
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
                        console.log(detail)
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

                    setmanufactureFormValue({});
                    deleteManufacture.style.display = "none";

                    $('div .materialOutDetailRowDiv').remove()
                    $('div .addProductInDetailRow').remove()


                    addMaterialOutDetailRow({})
                    addMaterialOutDetailRow({})
                    addMaterialOutDetailRow({})

                    addProductInDetailRow({})
                    addProductInDetailRow({})
                    addProductInDetailRow({})

                    manufactureform.action = "{{ route('manufactures.store') }}";
                });

                $(document).on('click', '.editManufactureButton', function() {
                    const manufactureId = $(this).data('manufacture-id');
                    const manufacture = manufactureCrudDiv.manufactures.find(manufacture => manufacture.id ===
                        manufactureId);
                    
                    deleteManufacture.style.display = "block";

                    $('div .materialOutDetailRowDiv').remove()
                    $('div .detailInputSetProductInDiv').remove()
                    


                    $('#manufactureform').append($('@method('put')'))

                    setmanufactureFormValue(manufacture);
                    
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
                        url: "/api/datatable/manufacture?with=productIn.details.product,materialOut.details.materialInDetail.material",
                        dataSrc: json => {
                            manufactureCrudDiv.manufactures = json.data;
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
                    },{
                    orderable: false,
                    title: '{{ __('Material Out') }}',
                    data: 'material_out.details',
                    name: 'details',
                    width: '20%',
                    render: details => details.map(detail => renderTagMaterialOutButton(
                        `${detail.material_in_detail.material.name} (${detail.qty})`)).join('')
                    },{
                    orderable: false,
                    title: '{{ __('Product In') }}',
                    data: 'product_in.details',
                    name: 'details',
                    width: '20%',
                    render: details => details.map(detail => renderTagProductInButton(
                        `${detail.product.name} (${detail.qty})`)).join('')
                    },{
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
