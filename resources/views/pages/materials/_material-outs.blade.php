@include('components.assets._datatable')
@include('components.assets._select2')

<div id="materialOutsCrudDiv">
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material Out List') }}
            <button type="button" id="addMaterialOutsButton" class="ml-2 btn btn-danger" data-toggle="modal"
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
                    <h5 class="modal-title" id="materialOutFormModalLabel"></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="materialOutFormModalBody">

                    <form method="POST" id="materialOutForm">
                        @csrf

                        <input type="hidden" name="id" id="materialOutIdInput">


                        <div class="row">
                            <div class="col form-group">
                                <label for="materialOutCodeInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="code" id="materialOutCodeInput">
                            </div>

                            <div class="col form-group">
                                <label for="materialOutTypeSelect">{{ __('Type') }}</label>
                                <select id="materialOutTypeSelect" name="type" required class="form-control select2"
                                    data-select2-opts='{"tags": "true"}'>
                                    @foreach ($materialOutTypes as $type)
                                        <option>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="materialOutAtInput">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="at" required id="materialOutAtInput">
                        </div>

                        <div class="form-group">
                            <label for="materialOutNoteInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="note" id="materialOutNoteInput" rows="3" style="height:100%;"></textarea>
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
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" form="materialOutForm"
                            class="btn btn-outline-success">{{ __('Save') }}</button>
                    </div>
                    <form method="post" id="deleteMaterialOutForm">
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
                $(function() {
                    $('#materialOutTypeSelect').select2({
                        tags: true,
                        dropdownParent: $('#materialOutFormModalBody')
                    })
                })

                const initMaterialSelects = $selectDom => $selectDom.select2({
                    dropdownParent: $('#materialOutFormModalBody'),
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
                                    text: `${materialInDetail.material?.name} (${materialInDetail.stock?.qty}) ${materialInDetail.material_in?.at ? moment(materialInDetail.material_in?.at).format('DD-MM-YYYY') : null}`
                                }
                            })

                            return {
                                results: theResults
                            }
                        }
                    },
                    minimumInputLength: 3
                })

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
                        )
                    }

                    initMaterialSelects($selectDom)
                    $selectDom.val(detail.material_in_detail_id).change()


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
                    detailRowDiv.setAttribute('class',
                        'form-group row materialOutDetailRowDiv mx-0 align-items-center detailInputSetDiv')
                    $(detailRowDiv).append(materialSelectParentDiv)
                    $(detailRowDiv).append(qtyInputParentDiv)

                    if (nDetailInputSet > 0) {
                        $(detailRowDiv).append(removeRowButtonParentDiv)
                    }

                    materialOutDetailsParent.append(detailRowDiv)
                }


                const setMaterialOutFormValue = materialOut => {
                    const materialOutTypeSelect = $('#materialOutTypeSelect')
                    materialOutTypeSelect.val(materialOut.type || null).change()
                    materialOutIdInput.value = materialOut.id || null
                    materialOutCodeInput.value = materialOut.code || null
                    materialOutNoteInput.value = materialOut.note || null

                    if (materialOut.at) {
                        materialOutAtInput.value = `${moment(materialOut.at).format('YYYY-MM-DD')}`
                    } else {
                        materialOutAtInput.value = '{{ date('Y-m-d') }}'
                    }


                    if (materialOut.details && materialOut.details.length > 0) {
                        materialOut.details.map(detail => addMaterialOutDetailRow(detail))
                    } else {
                        addMaterialOutDetailRow({})
                        addMaterialOutDetailRow({})
                        addMaterialOutDetailRow({})
                    }

                }


                $(document).on('click', '#addMaterialOutsButton', function() {
                    $('[name="_method"][value="put"]').remove()
                    $('.materialOutDetailRowDiv').remove()

                    setMaterialOutFormValue({})

                    deleteMaterialOutForm.style.display = "none"
                    materialOutFormModalLabel.innerHTML = '{{ __('Add New Material Out') }}'
                    materialOutForm.action = "{{ route('material-outs.store') }}"
                })

                $(document).on('click', '.editMaterialOutButton', function() {
                    $('.materialOutDetailRowDiv').remove()
                    $('[name="_method"][value="put"]').remove()
                    $('#materialOutForm').append($('@method('put')'))

                    const materialOutId = $(this).data('material-out-id')
                    const materialOut = materialOutsCrudDiv.materialOuts.find(materialOut => materialOut.id ===
                        materialOutId)

                    setMaterialOutFormValue(materialOut)

                    deleteMaterialOutForm.style.display = "block"
                    materialOutFormModalLabel.innerHTML =
                        `{{ __('Edit Material Out') }}: ${moment(materialOut.at).format('DD-MM-YYYY')}`
                    materialOutForm.action = `{{ route('material-outs.update', '') }}/${materialOut.id}`
                    deleteMaterialOutForm.action = `{{ route('material-outs.destroy', '') }}/${materialOut.id}`
                })

                const renderTagButton = text =>
                    `<a href="#" class="m-1 badge badge-danger materialOutDetailTag">${text}</a>`


                $(document).on('click', '.materialOutDetailTag', function() {
                    const materialName = this.innerHTML.split(' ')[0]
                    materialOutsCrudDiv.materialOutDatatable.DataTable().search(materialName).draw()
                })

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
                        url: '/api/datatable/MaterialOut?with=details.materialInDetail.material',
                        dataSrc: json => {
                            materialOutsCrudDiv.materialOuts = json.data
                            return json.data
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
                        title: '{{ __('validation.attributes.code') }}'
                    }, {
                        data: 'at',
                        title: '{{ __('validation.attributes.at') }}',
                        render: at => moment(at).format('DD-MM-YYYY')
                    }, {
                        data: 'type',
                        title: '{{ __('validation.attributes.type') }}',
                    }, {
                        data: 'note',
                        title: '{{ __('validation.attributes.note') }}',
                    }, {
                        orderable: false,
                        title: '{{ __('Items') }}',
                        data: 'details',
                        name: 'details.materialInDetail.material.name',
                        width: '20%',
                        render: details => details.map(detail => renderTagButton(
                                `${detail.material_in_detail?.material.name} (${detail.qty})`
                            )).join('')
                    }, {
                        render: function(data, type, row) {
                            const editButton = $(
                                '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                            )
                            editButton.attr('data-toggle', 'modal')
                            editButton.attr('data-target', '#materialOutFormModal')
                            editButton.addClass('editMaterialOutButton')
                            editButton.attr('data-material-out-id', row.id)
                            return editButton.prop('outerHTML')
                        },
                        orderable: false
                    }]
                })
            }
        }
    </script>
@endpush
