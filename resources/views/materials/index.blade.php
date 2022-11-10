@extends('layouts.main')

@section('title', __('Material'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')

    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material Insert List') }}
            <button type="button" class="ml-2 btn btn-success addMaterialInsButton" data-toggle="modal"
                data-target="#materialInsertFormModal">
                <i class="fas fa-plus-circle"></i> Tambah
            </button>
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="materialInsert" style="width:100%">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

    <div class="modal fade" id="materialInsertFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
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

                    <form method="POST" id="materiInsertForm">
                        @csrf

                        <input type="hidden" name="id" id="idIns">
                        <input type="hidden" name="last_updated_by_user_id" id="last_updated_by_user_id" value="{{Auth::user()->id}}">

                        <div class="mb-3">
                            <label for="materialCode">{{ __('Code') }}</label>
                            <input type="text" class="form-control" name="code" id="codeInsInput">
                        </div>

                        <div class="form-group">
                            <label for="typeSelect">{{ __('Type') }}</label>
                            <select id="typeSelect" name="type" class="form-control select2" data-select2-opts='{"tags": "true"}'>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="materialName">{{ __('Note') }}</label>
                            <textarea class="form-control" name="note" required id="noteInsInput"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="materialUnit">{{ __('Description') }}</label>
                            <input type="text" class="form-control" name="desc" required id="descInsInput">
                        </div>
                    </form>
                    <div class="d-flex justify-content-between">
                        <div>
                            <button type="submit" form="materiInsertForm" class="btn btn-outline-success">{{ __('Save') }}</button>
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
    </div>

    <script>
        let materials
        const typeSelect = $('#typeSelect')
        let materialDatatable = $('#materialDatatable')

        const materialFormModalLabel = $('#materialFormModalLabel')

        let materialInsert = $('#materialInsert')

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInputInsert = () => {
            $('#materiInsertForm').append($('@method('put')'))
        }

        const setMaterialInsertValue = material_ins => {
            idIns.value = material_ins.id || null
            last_updated_by_user_id.value = material_ins.last_updated_by_user_id || null
            typeSelect.val('asdasdasd').change();
            codeInsInput.value = material_ins.code || null
            noteInsInput.value = material_ins.note || null
            descInsInput.value = material_ins.desc || null
            deleteInsId.value = material_ins.id
        }
        

        const datatableSearch = tag =>  
            materialDatatable.DataTable().search(tag).draw()

        $(document).on('click', '.addMaterialInsButton', function(){
            deletePutMethodInput();
            setMaterialInsertValue({});
            deleteForm.style.display = "none";

            materiInsertForm.action = "{{ route('material_ins.store') }}";
        });

        $(document).on('click', '.editMaterialInsertButton', function(){
            const materialId = $(this).data('material-id');
            const material = materials.find(material => material.id === materialId);
            deleteForm.style.display = "block";

            
            
            addPutMethodInputInsert();
            setMaterialInsertValue(material);
            materiInsertForm.action = "{{ route('material_ins.update', '') }}/"+material.id;

            deleteForm.action = "{{ route('material_ins.destroy', '') }}/" + material
                .id;
        })

        $(document).ready(function() {
            $('#typeSelect').select2('destroy').select2({
                tags:true,
                dropdownParent:$('#modal_body_material')
            })

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

            materialInsert = materialInsert.dataTable({
                processing:true,
                serverSide:true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'Material_ins') }}',
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
                columns:[{
                    data: 'code',
                    title: '{{__('Code')}}'
                },{
                    data:'at',
                    title:'{{__('At')}}'
                },{
                    data:'type',
                    title: '{{__('Type')}}',
                },{
                    data: 'created_by_user_id',
                    title: '{{__('Creater')}}'
                },{
                    data: 'last_updated_by_user_id',
                    title: '{{__('last_updater')}}'
                },{
                    render: function(data, type, row) {
                        const editButton = $('<a href="#"><i class="fas fa-cog"></i></a>')
                        editButton.attr('data-toggle', 'modal')
                        editButton.attr('data-target', '#materialInsertFormModal')
                        editButton.addClass('editMaterialInsertButton');
                        editButton.attr('data-material-id', row.id)
                        return editButton.prop('outerHTML')
                    },
                    orderable:false
                }]
            });
        });
    </script>
@endpush

