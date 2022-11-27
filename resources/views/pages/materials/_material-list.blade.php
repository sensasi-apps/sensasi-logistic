@include('components.assets._datatable')
@include('components.assets._select2')

<div id="materialsCrudDiv">
    <h2 class="section-title">
        {{ __('Material List') }}
        <button id="addMaterialButton" type="button" class="ml-2 btn btn-success" data-toggle="modal"
            data-target="#materialFormModal">
            <i class="fas fa-plus-circle"></i> {{ __('Add') }}
        </button>
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="materialDatatable" style="width:100%">
                </table>
            </div>
        </div>
    </div>
</div>


@push('modal')
    <div class="modal fade" id="materialFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="materialFormModalLabel">{{ __('Add new material') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form method="POST" id="materialForm">
                        @csrf

                        <input type="hidden" name="id" id="materialIdInput">

                        <div class="form-group">
                            <label for="materialCodeInput">{{ __('Code') }}</label>
                            <input type="text" class="form-control" name="code" id="materialCodeInput">
                        </div>

                        <div class="form-group">
                            <label for="materialNameInput">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" required id="materialNameInput">
                        </div>

                        <div class="form-group">
                            <label for="materialUnitInput">{{ __('Unit') }}</label>
                            <input type="text" class="form-control" name="unit" required id="materialUnitInput">
                        </div>

                        <div class="form-group">
                            <label for="materialTagsSelect">{{ __('Tags') }}</label>
                            <select id="materialTagsSelect" name="tags[]" class="form-control select2" multiple
                                data-select2-opts='{"tags": "true", "tokenSeparators": [",", " "]}'>
                            </select>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between">
                        <button type="submit" form="materialForm" class="btn btn-primary">{{ __('Save') }}</button>

                        <button id="materialDeleteFormModalToggleButton" class="btn btn-icon btn-outline-danger"
                            data-toggle="tooltip" title="{{ __('Delete') }}">
                            <i class="fas fa-trash" style="font-size: 1rem !important"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="materialDeleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ __('Are you sure') }}?</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="font-size: 1.1rem">
                    {{ __('This action can not be undone') }}.
                    {{ __('Do you still want to delete') }} <b style="font-size: 1.5rem" id="deleteMaterialName"></b>
                    <form method="post" id="materialDeleteForm">
                        @csrf
                        @method('delete')
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="materialDeleteForm" class="btn btn-danger">{{ __('Yes') }}</button>
                    <button data-dismiss="modal" class="btn btn-secondary" id="">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@endpush



@push('js')
    <script>
        {
            if (materialsCrudDiv) {
                const deletePutMethodInput = () => {
                    $('[name="_method"][value="put"]').remove()
                }

                const addPutMethodInput = () => {
                    $('#materialForm').append($('@method('put')'))
                }

                const setFormValue = material => {
                    const materialTagsSelect = $('#materialTagsSelect')
                    const selectOpts = materialTagsSelect.find('option')
                    const optValues = selectOpts.map((i, select) => select.innerHTML)

                    material.tags?.map(tag => {
                        if ($.inArray(tag, optValues) === -1) {
                            materialTagsSelect.append(`<option>${tag}</option>`)
                        }
                    })

                    materialTagsSelect.val(material.tags || []).change()
                    materialIdInput.value = material.id || null
                    materialNameInput.value = material.name || null
                    materialUnitInput.value = material.unit || null
                    materialCodeInput.value = material.code || null
                }

                // ######## ONCLICK SECTION
                $(document).on('click', '#addMaterialButton', function() {
                    materialFormModalLabel.innerHTML = '{{ __('Add new material') }}'

                    deletePutMethodInput()
                    setFormValue({})

                    materialDeleteFormModalToggleButton.style.display = "none"
                    materialForm.action = "{{ route('materials.store') }}"
                })

                materialDeleteFormModalToggleButton.onclick = () => {
                    $('#materialDeleteConfirmationModal').modal('show')
                };

                $(document).on('click', '.editMaterialButton', function() {

                    const materialId = $(this).data('material-id')
                    const material = materialsCrudDiv.materials.find(material => material.id === materialId)

                    materialFormModalLabel.innerHTML = `{{ __('Edit') }} ${material.name}`

                    console.log(materialFormModalLabel.innerHTML + 'asd');

                    setFormValue(material)
                    deletePutMethodInput()
                    addPutMethodInput()

                    materialDeleteFormModalToggleButton.style.display = "block"

                    materialForm.action = "{{ route('materials.update', '') }}/" +
                        material
                        .id

                    deleteMaterialName.innerHTML = material.name
                    materialDeleteForm.action = `{{ route('materials.destroy', '') }}/${material.id}`
                });


                // ##### DATATABLE SECTION
                const datatableSearch = tag =>
                    materialsCrudDiv.materialDatatable.DataTable().search(tag).draw()

                materialsCrudDiv.materialDatatable = $(materialDatatable).dataTable({
                    processing: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                    },
                    serverSide: true,
                    ajax: {
                        url: 'api/datatable/Material',
                        dataSrc: json => {
                            materialsCrudDiv.materials = json.data
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
                    order: [],
                    columns: [{
                        data: 'code',
                        title: '{{ __('Code') }}'
                    }, {
                        data: 'name',
                        title: '{{ __('Name') }}'
                    }, {
                        data: 'qty',
                        title: '{{ __('Qty') }}'
                    }, {
                        data: 'unit',
                        title: '{{ __('Unit') }}'
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
                            editButton.attr('data-target', '#materialFormModal')
                            editButton.addClass('editMaterialButton')
                            editButton.attr('data-material-id', row.id)
                            return editButton.prop('outerHTML')
                        },
                        orderable: false
                    }]
                });
            }
        }
    </script>
@endpush
