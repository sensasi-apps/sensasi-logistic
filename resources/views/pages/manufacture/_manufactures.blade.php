@include('components.assets._datatable')
@include('components.assets._select2')

<div id="materialOutsCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material Out List') }}
            <button type="button" class="ml-2 btn btn-success addMaterialOutsButton" data-toggle="modal"
                data-target="#materialOutFormModal">
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
    <div class="modal fade" id="materialOutFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="materialOutFormModalLabel">{{ __('Add new material out') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="manufactureModalBody">

                    <form method="POST" id="manufacture">
                        @csrf
                        <h4 class="text-center">Manufacture</h4>
                        <hr>
                        <input type="hidden" name="id" id="idOuts">


                        <div class="row">
                            <div class="col form-group">
                                <label for="codeInsInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="code" id="codeInsInput">
                            </div>

                            <div class="form-group">
                                <label for="atInput">{{ __('Date') }}</label>
                                <input type="date" class="form-control" name="at" required id="atInput">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="noteInsInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="note" id="noteInsInput" rows="3" style="height:100%;"></textarea>
                        </div><hr>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" form="manufacture"
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
            if (materialOutsCrudDiv) {
                const materialOutFormModalLabel = $('#materialOutFormModalLabel')

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
                    const nDetailInputSet = $('.detailInputSetDiv').length
                    const materialSelectParentDiv = document.createElement('div')
                    materialSelectParentDiv.setAttribute('class', 'col-6 pl-0 pr-2')
                    const $selectDom = $(`<select required placeholder="{{ __('Material name') }}"></select>`)
                        .addClass('form-control select2 listSelect')
                        .attr('name', `details[${nDetailInputSet}][material_in_detail_id]`)
                    $(materialSelectParentDiv).append($selectDom)

                    if (detail.material_in_detail_id) {
                        $selectDom.append(
                            `<option value="${detail.material_in_detail_id}">${detail.material_in_detail?.material.name}</option>`
                        );
                    }

                    initMaterialSelects($selectDom);
                    $selectDom.val(detail.material_in_detail_id).change();


                    const qtyInputParentDiv = document.createElement('div')
                    qtyInputParentDiv.setAttribute('class', 'col-5 px-2')
                    $(qtyInputParentDiv).append(
                        `<input class="form-control" name="details[${nDetailInputSet}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
                    )

                    const removeRowButtonParentDiv = document.createElement('div')
                    removeRowButtonParentDiv.setAttribute('class', 'col-1 pl-2 pr-0')
                    $(removeRowButtonParentDiv).append($(
                        '<button class="btn btn-outline-danger btn-icon" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>'
                    ))

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class', 'form-group row materialOutDetailRowDiv mx-0 align-items-center detailInputSetDiv')
                    $(detailRowDiv).append(materialSelectParentDiv)
                    $(detailRowDiv).append(qtyInputParentDiv)
                    $(detailRowDiv).append(removeRowButtonParentDiv)
                    $(detailRowDiv).append(`<input type="hidden" name="" value="${detail.id}">`)

                    materialOutDetailsParent.append(detailRowDiv);
                }


                const setMaterialOutFormValue = materialOut => {
                    const materialOutTypeSelect = $('#materialOutTypeSelect');
                    materialOutTypeSelect.val(materialOut.type || null).change();
                    idOuts.value = materialOut.id || null
                    codeInsInput.value = materialOut.code || null
                    noteInsInput.value = materialOut.note || null

                    if (materialOut.at) {
                        const dateObj = new Date(materialOut.at);

                        const month = dateObj.getMonth() + 1; //months from 1-12
                        const day = dateObj.getDate();
                        const year = dateObj.getFullYear();

                        atInput.value = `${year}-${month}-${day}`
                    } else {
                        atInput.value = '{{ date('Y-m-d') }}'
                    }


                    materialOut.details?.map(function(detail) {
                        addMaterialOutDetailRow(detail)
                    })
                }


                $(document).on('click', '.addMaterialOutsButton', function() {
                    $('[name="_method"][value="put"]').remove()

                    setMaterialOutFormValue({});
                    deleteManufacture.style.display = "none";

                    $('div .materialOutDetailRowDiv').remove()


                    addMaterialOutDetailRow({})
                    addMaterialOutDetailRow({})
                    addMaterialOutDetailRow({})

                    manufacture.action = "{{ route('material-outs.store') }}";
                });

                $(document).on('click', '.editMaterialOutButton', function() {
                    const materialOutId = $(this).data('material-id');
                    const materialOut = materialOutsCrudDiv.materialOuts.find(materialOut => materialOut.id ===
                        materialOutId);
                    deleteManufacture.style.display = "block";

                    $('div .materialOutDetailRowDiv').remove()


                    $('#manufacture').append($('@method('put')'))

                    setMaterialOutFormValue(materialOut);

                    manufacture.action = `{{ route('material-outs.update', '') }}/${materialOut.id}`;
                    deleteManufacture.action = `{{ route('material-outs.destroy', '') }}/${materialOut.id}`;
                })

                $(document).on('click', '#addMaterialOutDetailButton', function() {
                    addMaterialOutDetailRow({})
                })


                // ################## DATATABLE SECTION

                const datatableSearch = tag => materialOutsCrudDiv.materialOutDatatable.DataTable().search(tag).draw()

                materialOutsCrudDiv.materialOutDatatable = $(materialOutDatatable).dataTable({
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
                            materialOutsCrudDiv.materialOuts = json.data;
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
                            editButton.attr('data-target', '#materialOutFormModal')
                            editButton.addClass('editMaterialOutButton');
                            editButton.attr('data-material-id', row.id)
                            return editButton.prop('outerHTML')
                        },
                        orderable: false
                    }]
                })
            }
        }
    </script>
@endpush
