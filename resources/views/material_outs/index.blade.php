@extends('layouts.main')

@section('title', __('Material Out'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')

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
                    <table class="table table-striped" id="materialInDatatable" style="width:100%">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <div class="modal fade" id="materialOutFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="materialFormModalLabel">{{ __('Add new material out') }}</h5>
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
                                <select id="typeSelect" name="type" required class="form-control select2"
                                    data-select2-opts='{"tags": "true"}'></select>
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
                            <div id="materialInDetailsParent" style="width: 100%">
                                <div class="row m-0">
                                    <label class="col-6">{{ __('Name') }}</label>
                                    <label class="col-5">{{ __('Qty') }}</label>
                                </div>
                            </div>
                        </div>


                        <div class="">
                            <a href="#" id="addMaterialButton" class="btn btn-success btn-sm mr-2"><i
                                    class="fas fa-plus"></i> {{ __('More') }}</a>
                            <a href="{{ route('materials.index') }}">{{ __('New material') }}?</a>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="submit" form="materiInsertForm"
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
        let materialOuts
        let materialInDatatable = $('#materialInDatatable')


        const materialInDetails = {{ Js::from(App\Models\MaterialInDetail::with('material')->get()) }};
        const typeSelect = $('#typeSelect')
        const materialFormModalLabel = $('#materialFormModalLabel')

        const datatableSearch = tag => materialInDatatable.DataTable().search(tag).draw()
        const renderTagButton = text =>
            `<a href="#" onclick="datatableSearch('${text.split(' ')[0]}')" class="m-1 badge badge-primary">${text}</a>`

        const initMaterialSelects = $selectDom => $selectDom.select2({
            dropdownParent: $('#modal_body_material'),
            placeholder: '{{ __('Material') }}',
            data: materialInDetails.map(item => formattedMaterial = {
                id: item.id,
                text: item.material?.name+' '+'['+item.qty+']'
            })
        });

        function removeMaterialInDetails() {
            $('div .details').remove()
        }

        function removeMaterialInDetailRow(dom) {
            dom.parentNode.parentNode.remove();
        }

        function addMaterialOutDetailRow(detail) {
            const materialSelectParentDiv = document.createElement('div')
            materialSelectParentDiv.setAttribute('class', 'col-6 pl-0 pr-2')
            const $selectDom = $(`<select required placeholder="{{ __('Material name') }}"></select>`)
                .addClass('form-control select2 listSelect')
                .attr('name', 'material_ids[]')
            $(materialSelectParentDiv).append($selectDom)
            initMaterialSelects($selectDom);
            console.log(detail.detail_ins);
            $selectDom.val(detail.mat_in_detail_id).change();


            const qtyInputParentDiv = document.createElement('div')
            qtyInputParentDiv.setAttribute('class', 'col-5 px-2')
            $(qtyInputParentDiv).append(
                `<input class="form-control" name="qty[]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
            )

            const removeRowButtonParentDiv = document.createElement('div')
            removeRowButtonParentDiv.setAttribute('class', 'col-1 pl-2 pr-0')
            $(removeRowButtonParentDiv).append($(
                '<button class="btn btn-outline-danger btn-icon" onclick="removeMaterialInDetailRow(this)"><i class="fas fa-trash"></i></button>'
            ))

            const detailRowDiv = document.createElement('div')
            detailRowDiv.setAttribute('class', 'form-group row details mx-0 align-items-center')
            $(detailRowDiv).append(materialSelectParentDiv)
            $(detailRowDiv).append(qtyInputParentDiv)
            $(detailRowDiv).append(removeRowButtonParentDiv)
            $(detailRowDiv).append(`<input type="hidden" name="idDetail[]" value="${detail.id}">`)

            materialInDetailsParent.append(detailRowDiv);
        }

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInputInsert = () => {
            $('#materiInsertForm').append($('@method('put')'))
        }


        const setMaterialInFormValue = materialOut => {

            if (materialOut.type) {
                const selectOpts = typeSelect.find('option');
                const optValues = selectOpts.map((i, select) => select.innerHTML);
                if ($.inArray(materialOut.type, optValues) === -1) {
                    typeSelect.append(`<option>${materialOut.type}</option>`);
                };
            }

            typeSelect.val(materialOut.type || null).change();
            idIns.value = materialOut.id || null
            codeInsInput.value = materialOut.code || null
            noteInsInput.value = materialOut.note || null
            descInsInput.value = materialOut.desc || null
            deleteInsId.value = materialOut.id || null

            if (materialOut.at) {
                const dateObj = new Date(materialOut.at);

                const month = dateObj.getMonth() + 1; //months from 1-12
                const day = dateObj.getDate();
                const year = dateObj.getFullYear();

                atInput.value = `${year}-${month}-${day}`
            } else {
                atInput.value = '{{ date('Y-m-d') }}'
            }

            materialOut.detail_outs?.map(function(detail) {
                addMaterialOutDetailRow(detail)
            })
        }


        $(document).on('click', '.addMaterialOutsButton', function() {
            deletePutMethodInput();
            setMaterialInFormValue({});
            deleteForm.style.display = "none";

            removeMaterialInDetails()

            addMaterialOutDetailRow({})

            materiInsertForm.action = "{{ route('material-outs.store') }}";
        });

        $(document).on('click', '.editMaterialOutButton', function() {
            const materialOutId = $(this).data('material-id');
            const materialOut = materialOuts.find(materialOut => materialOut.id === materialOutId);
            deleteForm.style.display = "block";

            removeMaterialInDetails();

            addPutMethodInputInsert();
            setMaterialInFormValue(materialOut);

            materiInsertForm.action = "{{ route('material-outs.update', '') }}/" + materialOut.id;

            deleteForm.action = "{{ route('material-outs.destroy', '') }}/" + materialOut
                .id;
        })

        $(document).on('click', '#addMaterialButton', function() {
            addMaterialOutDetailRow({})
        })



        $(document).ready(function() {
            $('#typeSelect').select2('destroy').select2({
                tags: true,
                dropdownParent: $('#modal_body_material')
            })

            materialInDatatable = materialInDatatable.dataTable({
                processing: true,
                search: {
                    return: true,
                },
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                },
                serverSide: true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'MaterialOut') }}?with=detail_outs.detail_ins.material',
                    dataSrc: json => {
                        materialOuts = json.data;
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
                    data: 'detail_outs',
                    width: '20%',
                    render: detail_outs => detail_outs.map(detail_out => renderTagButton(
                        `${detail_out.detail_ins.material.name} (${detail_out.qty})`)).join('')
                }, {
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
            });
        });
    </script>
@endpush
