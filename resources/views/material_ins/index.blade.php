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

                        <div class="mb-3 d-flex justify-content-between col-12">
                            <a href="{{route('materials.index')}}">Material Not Exist?</a>
                            <a href="#" id="addMaterial" class="btn btn-success">Add Material</a>
                        </div>

                        <div class="mb-3">
                            
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
        let materialIns
        const typeSelect = $('#typeSelect')
        let materialDatatable = $('#materialDatatable')

        const materialFormModalLabel = $('#materialFormModalLabel')

        let materialInsert = $('#materialInsert')

        function removeMaterialDetail(){
            $('div .details').remove()
        }

        function addMaterialDetail(detail){
            var MaterialInId = detail.id || ''
            var MaterialId = detail.material_id || ''
            var MaterialName = detail.material?.name || ''
            var qty = detail.qty || ''
            var price = detail.price || ''

            let div = document.createElement('div')
            div.setAttribute('class', 'mb-3 border border-1 p-1 d-flex details')

            let div2 = document.createElement('div')
            div2.setAttribute('class', 'mx-1')

            let div3 = document.createElement('div')
            div3.setAttribute('class', 'mx-1')

            let div4 = document.createElement('div')
            div4.setAttribute('class', 'mx-1')

            materiInsertForm.append(div)
            $(div).append(div2)
            $(div).append(div3)
            $(div).append(div4)
            $(div).append("<input type='hidden' name='idDetail[]' value="+MaterialInId+">")

            let select = $("<select><option value="+MaterialId+" selected>"+MaterialName+"</option></select>").addClass('form-control select2 listSelect').attr('name', 'material_id[]')

            $(div2).append("<label class='form-label'>Materi</label>")
            $(div2).append(select)
                
            $(div3).append("<label class='form-label'>Jumlah</label>")
            $(div3).append("<input class='form-control' name='qty[]' value="+qty+">")

            $(div4).append("<label class='form-label'>Harga</label>")
            $(div4).append("<input class='form-control' name='price[]'value="+price+">")

            
        }

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInputInsert = () => {
            $('#materiInsertForm').append($('@method('put')'))
        }
        

        const setMaterialInsertValue = materialIn => {
            const selectOpts = typeSelect.find('option');
            const optValues = selectOpts.map((i, select) => select.innerHTML);

            if ($.inArray(materialIn.type, optValues) === -1) {
                typeSelect.append(`<option>${materialIn.type}</option>`);
            };

            idIns.value = materialIn.id || null
            last_updated_by_user_id.value = materialIn.last_updated_by_user_id || null
            typeSelect.val(materialIn.type).change();
            codeInsInput.value = materialIn.code || null
            noteInsInput.value = materialIn.note || null
            descInsInput.value = materialIn.desc || null
            deleteInsId.value = materialIn.id || null
            // console.log(materialIn.details)
            materialIn.details?.map(function(detail){
                addMaterialDetail(detail)
            })
        }
        

        const datatableSearch = tag =>  
            materialDatatable.DataTable().search(tag).draw()

        $(document).on('click', '.addMaterialInsButton', function(){
            deletePutMethodInput();
            setMaterialInsertValue({});
            deleteForm.style.display = "none";

            removeMaterialDetail()

            materiInsertForm.action = "{{ route('material_ins.store') }}";
        });

        $(document).on('click', '.editMaterialInsertButton', function(){
            const materialInId = $(this).data('material-id');
            const materialIn = materialIns.find(materialIn => materialIn.id === materialInId);
            deleteForm.style.display = "block";

            removeMaterialDetail();
            
            addPutMethodInputInsert();
            setMaterialInsertValue(materialIn);

            materiInsertForm.action = "{{ route('material_ins.update', '') }}/"+materialIn.id;

            deleteForm.action = "{{ route('material_ins.destroy', '') }}/" + materialIn
                .id;

            $('.listSelect').select2({
                dropdownParent:$('#modal_body_material'),
                ajax:{
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'Material') }}',
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            "Authorization",
                            'Bearer {{ Auth::user()->createToken('user_' . Auth::user()->id)->plainTextToken }}'
                        )
                    },
                    processResults: function (data) {
                      // Transforms the top-level key of the response object from 'items' to 'results'
                      const items = data.data.map(item => { return { id: item.id, text: item.name }});

                      return {results:items};
                    }
                }
            });
        })

        $(document).on('click', '#addMaterial', function(){
            addMaterialDetail({})

            $('.listSelect').select2({
                dropdownParent:$('#modal_body_material'),
                ajax:{
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'Material') }}',
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            "Authorization",
                            'Bearer {{ Auth::user()->createToken('user_' . Auth::user()->id)->plainTextToken }}'
                        )
                    },
                    processResults: function (data) {
                      // Transforms the top-level key of the response object from 'items' to 'results'
                      const items = data.data.map(item => { return { id: item.id, text: item.name }});

                      return {results:items};
                    }
                }
            });
            
        })

        $(document).ready(function() {
            $('#typeSelect').select2('destroy').select2({
                tags:true,
                dropdownParent:$('#modal_body_material')
            })

            materialInsert = materialInsert.dataTable({
                processing:true,
                serverSide:true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'MaterialIn') }}?with=details.material',
                    dataSrc: json => {
                        materialIns = json.data;
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

