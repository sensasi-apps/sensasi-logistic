@extends('layouts.main')

@section('title', __('Material'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material list') }}
            <button type="button" class="ml-2 btn btn-success addMaterialButton" data-toggle="modal"
                data-target="#materialFormModal">
                <i class="fas fa-plus-circle"></i> Tambah
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
@endsection

@push('js')
    <div class="modal fade" id="materialFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="materialFormModalLabel">{{ __('Add new material') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_material">

                    <form method="POST" id="materialForm">
                        @csrf

                        <input type="hidden" name="id" id="idInput">

                        <div class="mb-3">
                            <label for="materialCode">{{ __('Code') }}</label>
                            <input type="text" class="form-control" name="code" id="codeInput">
                        </div>
                        <div class="mb-3">
                            <label for="materialName">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" required id="nameInput">
                        </div>

                        <div class="mb-3">
                            <label for="materialUnit">{{ __('Unit') }}</label>
                            <input type="text" class="form-control" name="unit" required id="unitInput">
                        </div>

                        <div class="form-group">
                            <label for="tags">{{ __('Tags') }}</label>
                            <select id="tagsSelect" name="tags[]" class="form-control select2" multiple
                                data-select2-opts='{"tags": "true", "tokenSeparators": [",", " "]}'>
                            </select>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" form="materialForm" class="btn btn-outline-success">{{ __('Save') }}</button>
                        </div>
                        <form action="" method="post" id="deleteForm">
                            @csrf
                            @method('delete')
                            <input type="hidden" name="id" id="deleteId">
                            <button type="submit" class="btn btn-icon btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
    
                </div>
            </div>
        </div>
    </div>

    <script>
        let materials
        const tagsSelect = $('#tagsSelect')
        let materialDatatable = $('#materialDatatable')

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInput = () => {
            $('#materialForm').append($('@method('put')'))
        }


        const setFormValue = material => {
            const selectOpts = tagsSelect.find('option');
            const optValues = selectOpts.map((i, select) => select.innerHTML);

            material.tags?.map(tag => {
                if ($.inArray(tag, optValues) === -1) {
                    tagsSelect.append(`<option>${tag}</option>`);
                };
            })

            tagsSelect.val(material.tags || []).change();
            idInput.value = material.id || null
            nameInput.value = material.name || null
            unitInput.value = material.unit || null
            codeInput.value = material.code || null
            deleteId.value = material.id
        }
        

        const datatableSearch = tag =>  
            materialDatatable.DataTable().search(tag).draw()
         

        $(document).on('click', '.addMaterialButton', function() {
            $(materialFormModalLabel).html("{{__('Add New Material Outs')}}")


            deletePutMethodInput();
            setFormValue({});

            deleteForm.style.display = "none";
            materialForm.action = "{{ route('materials.store') }}";
        })

        $(document).on('click', '.editMaterialButton', function() {
            $(materialFormModalLabel).html("{{__('Add New Material Outs')}}")

            const materialId = $(this).data('material-id');
            const material = materials.find(material => material.id === materialId);

            setFormValue(material);
            deletePutMethodInput();
            addPutMethodInput();

            deleteForm.style.display = "block";

            materialForm.action = "{{ route('materials.update', '') }}/" +
                material
                .id;

            deleteForm.action = "{{ route('materials.destroy', '') }}/" + material
                .id;
        });

        $(document).ready(function() {
            materialDatatable = materialDatatable.dataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'Material') }}',
                    dataSrc: json => {
                        materials = json.data;
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
                    title: '{{ __('Code') }}'
                }, {
                    data: 'name',
                    title: '{{ __('Name') }}'
                }, {
                    data: 'unit',
                    title: '{{ __('Unit') }}'
                }, {
                    data: 'tags',
                    name: 'tags_json',
                    title: '{{ __('Tags') }}',
                    render: data => data?.map(tag => `<a href="#" onclick="datatableSearch('${tag}')" class="m-1 badge badge-success">${tag}</a>`).join('') || null,
                }, {
                    render: function(data, type, row) {
                        const editButton = $('<a href="#"><i class="fas fa-cog"></i></a>')
                        editButton.attr('data-toggle', 'modal')
                        editButton.attr('data-target', '#materialFormModal')
                        editButton.addClass('editMaterialButton');
                        editButton.attr('data-material-id', row.id)
                        return editButton.prop('outerHTML')
                    },
                    orderable: false
                }]
            });
        });
    </script>
@endpush
