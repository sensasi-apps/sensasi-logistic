@extends('layouts.main')

@section('title', __('Material In'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')

    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material In List') }}
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="materialFormModalLabel">{{ __('Add new material in') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_material">

                    <form method="POST" id="materiInsertForm">
                        @csrf

                        <input type="hidden" name="id" id="idIns">

                        
                        <div class="row">
                            <div class="col form-group">
                                <label for="codeInsInput">{{ __('Code') }}</label>
                                <input type="text" class="form-control" name="code" id="codeInsInput">
                            </div>
                            
                            <div class="col form-group">
                                <label for="typeSelect">{{ __('Type') }}</label>
                                <select id="typeSelect" name="type" required class="form-control select2" data-select2-opts='{"tags": "true"}'></select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="atInput">{{ __('Date') }}</label>
                            <input type="date" class="form-control" name="at" required id="atInput">
                        </div>
                            
                        <div class="form-group">
                            <label for="descInsInput">{{ __('Description') }}</label>
                            <input type="text" class="form-control" name="desc" required id="descInsInput">
                        </div>

                        <div class="form-group">
                            <label for="noteInsInput">{{ __('Note') }}</label>
                            <textarea class="form-control" name="note" id="noteInsInput" rows="3" style="height:100%;"></textarea>
                        </div>

                        <div class="px-1" style="overflow-x: auto">
                            <div id="materialInDetailsParent"  style="width: 100%">
                                <div class="row m-0">
                                    <label class="col-4">{{ __('Name') }}</label>
                                    <label class="col-2">{{ __('Qty') }}</label>
                                    <label class="col-3">{{ __('Price/qty') }}</label>
                                    <label class="col-2">{{ __('Subtotal') }}</label>
                                </div>
                            </div>
                        </div>


                        <div class="">
                            <a href="#" id="addMaterialButton" class="btn btn-success btn-sm mr-2"><i class="fas fa-plus"></i> {{ __('More') }}</a>
                            <a href="{{route('materials.index')}}">{{ __('New material') }}?</a>
                        </div>
                    </form>    
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    {{-- <div class=""> --}}
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
                    {{-- </div> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        const materials = {{ Js::from(App\Models\Material::all()) }};
        let materialIns
        const typeSelect = $('#typeSelect')
        let materialDatatable = $('#materialDatatable')

        const materialFormModalLabel = $('#materialFormModalLabel')

        let materialInsert = $('#materialInsert')

        function removeMaterialInDetails(){
            $('div .details').remove()
        }

        function removeMaterialInDetailRow(dom){
            dom.parentNode.parentNode.remove();
        }

        function addMaterialInDetailRow(detail){
            var MaterialInId = detail.id || ''
            var MaterialName = detail.material?.name || ''
            var qty = detail.qty || ''
            var price = detail.price || ''

            const materialSelectParentDiv = document.createElement('div')
            materialSelectParentDiv.setAttribute('class', 'col-4 pl-0 pr-2')
            const $selectDom = $(`<select required placeholder="{{ __('Material name') }}"></select>`)
                .addClass('form-control select2 listSelect')
                .attr('name', 'material_ids[]')
            $(materialSelectParentDiv).append($selectDom)
            initMaterialSelects($selectDom);
            $selectDom.val(detail.material_id || '').change();

            const qtyInputParentDiv = document.createElement('div')
            qtyInputParentDiv.setAttribute('class', 'col-2 px-2')
            $(qtyInputParentDiv).append(`<input class="form-control" name="qty[]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${qty}">`)

            const priceInputParentDiv = document.createElement('div')
            priceInputParentDiv.setAttribute('class', 'col-3 px-2')
            $(priceInputParentDiv).append(`<input class='form-control' name='price[]' min="0" type="number" required placeholder="{{ __('Price') }}" value="${price}">`)

            const subtotalPreviewParentDiv = document.createElement('div')
            subtotalPreviewParentDiv.setAttribute('class', 'col-2 px-2')
            subtotalPreviewParentDiv.innerHTML="Rp. 0";

            const removeRowButtonParentDiv = document.createElement('div')
            removeRowButtonParentDiv.setAttribute('class', 'col-1 pl-2 pr-0')
            $(removeRowButtonParentDiv).append($('<button class="btn btn-outline-danger btn-icon" onclick="removeMaterialInDetailRow(this)"><i class="fas fa-trash"></i></button>'))

            const detailRowDiv = document.createElement('div')
            detailRowDiv.setAttribute('class', 'form-group row details mx-0 align-items-center')
            $(detailRowDiv).append(materialSelectParentDiv)
            $(detailRowDiv).append(qtyInputParentDiv)
            $(detailRowDiv).append(priceInputParentDiv)
            $(detailRowDiv).append(subtotalPreviewParentDiv)
            $(detailRowDiv).append(removeRowButtonParentDiv)
            $(detailRowDiv).append(`<input type="hidden" name="idDetail[]" value="${MaterialInId}">`)

            materialInDetailsParent.append(detailRowDiv);

            // $('#').parent().before(detailRowDiv);
        }

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInputInsert = () => {
            $('#materiInsertForm').append($('@method('put')'))
        }
        

        const setMaterialInsertValue = materialIn => {
            
            if (materialIn.type) {
                const selectOpts = typeSelect.find('option');
                const optValues = selectOpts.map((i, select) => select.innerHTML);
                if ($.inArray(materialIn.type, optValues) === -1) {
                    typeSelect.append(`<option>${materialIn.type}</option>`);
                };
            }
            
            typeSelect.val(materialIn.type || null).change();
            idIns.value = materialIn.id || null
            codeInsInput.value = materialIn.code || null
            noteInsInput.value = materialIn.note || null
            descInsInput.value = materialIn.desc || null
            deleteInsId.value = materialIn.id || null

            if (materialIn.at) {
                const dateObj = new Date(materialIn.at);
                
                const month = dateObj.getMonth() + 1; //months from 1-12
                const day = dateObj.getDate();
                const year = dateObj.getFullYear();
                
                atInput.value = `${year}-${month}-${day}`
            } else {
                atInput.value = '{{ date('Y-m-d') }}'
            }

            materialIn.details?.map(function(detail){
                addMaterialInDetailRow(detail)
            })
        }
        
        const datatableSearch = tag => materialDatatable.DataTable().search(tag).draw()

        $(document).on('click', '.addMaterialInsButton', function(){
            deletePutMethodInput();
            setMaterialInsertValue({});
            deleteForm.style.display = "none";

            removeMaterialInDetails()

            for (let i = 0; i < 3; i++) {
                addMaterialInDetailRow({})
            }

            materiInsertForm.action = "{{ route('material-ins.store') }}";
        });


        const initMaterialSelects = $selectDom => $selectDom.select2({
            dropdownParent:$('#modal_body_material'),
            placeholder: '{{ __('Material') }}',
            data: materials.map(material => formattedMaterial = {id: material.id, text: material.name})
        }); 

        $(document).on('click', '.editMaterialInsertButton', function(){
            const materialInId = $(this).data('material-id');
            const materialIn = materialIns.find(materialIn => materialIn.id === materialInId);
            deleteForm.style.display = "block";

            removeMaterialInDetails();
            
            addPutMethodInputInsert();
            setMaterialInsertValue(materialIn);

            materiInsertForm.action = "{{ route('material-ins.update', '') }}/"+materialIn.id;

            deleteForm.action = "{{ route('material-ins.destroy', '') }}/" + materialIn
                .id;
        })

        $(document).on('click', '#addMaterialButton', function(){
            addMaterialInDetailRow({})
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

