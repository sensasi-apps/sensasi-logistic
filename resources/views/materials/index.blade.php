@extends('layouts.main')

@section('title', __('Material'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material list') }}
            <button id="tambahMateri" type="button" class="ml-2 btn btn-success" data-toggle="modal"
                data-target="#materialModal" data-materi_id="" data-materi_code="" data-materi_name="" data-materi_unit=""
                data-materi_tags="">
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

        {{-- <a href="#" id="ubahMateri" class="btn btn-primary" data-toggle="modal"
                                        data-target="#materialModal" data-materi_id="{{ $materi->id }}"
                                        data-materi_code="{{ $materi->code }}" data-materi_name="{{ $materi->name }}"
                                        data-materi_unit="{{ $materi->unit }}"
                                        data-materi_tags="{{ $materi->tags_json }}"><i class="fas fa-eye"></i></a> --}}
    </div>
@endsection

@push('js')
    <div class="modal fade" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('Add new material') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true text-white">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_material">

                    <form action="" method="post" id="materialForm">
                        @csrf

                        <input type="hidden" name="id" id="materialId">
                        <div class="mb-3">
                            <label for="materialCode">{{ __('Code') }}</label>
                            <input type="text" class="form-control" name="code" id="materialCode"
                                placeholder="Material Code">
                        </div>
                        <div class="mb-3">
                            <label for="materialName">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" required id="materialName"
                                placeholder="Material Name">
                        </div>

                        <div class="mb-3">
                            <label for="materialUnit">{{ __('Unit') }}</label>
                            <input type="text" class="form-control" name="unit" required id="materialUnit"
                                placeholder="Material Unit">
                        </div>

                        <div class="form-group">
                            <label for="tags">{{ __('Tags') }}</label>
                            <select id="tags" name="tags[]" class="form-control select2" multiple
                                data-select2-opts='{"tags": "true", "tokenSeparators": [",", " "]}'>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <form action="" method="post" id="DeleteForm">
                        @csrf
                        @METHOD('DELETE')
                        <input type="hidden" name="id" id="idMaterialDelete">
                        <button type="submit" id="materialDelete" class="btn btn-danger">Delete</button>
                        <button type="submit" form="materialForm" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const editButton = $(
            "<a href=\"#\" id=\"materialEditButton\" class=\"\" data-toggle=\"modal\" data-target=\"#materialModal\"><i class=\"fas fa-cog\"></i></a>"
        );

        let materials;

        $(document).ready(function() {
            $("#materialDatatable").dataTable({
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
                    }
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
                    title: '{{ __('Tags') }}',
                    render: data => data?.join(', '),
                    "orderable": false
                }, {
                    render: function(data, type, row) {
                        const rowEditButton = editButton.clone();
                        rowEditButton.attr('data-material-id', row.id);
                        return rowEditButton.prop('outerHTML');
                    },
                    "orderable": false
                }]
            });

            $('[data-toggle="tooltip"]').tooltip();

        });
    </script>

    <script type="text/javascript">
        $(document).on('click', '#tambahMateri', function() {
            const MaterialForm = document.querySelector('#materialForm');
            const materialDelete = document.querySelector('#materialDelete');

            const tagsInputEl = $('#tags');
            tagsInputEl.val(null).change();


            let MaterialId = $(this).data('materi_id');
            let MaterialName = $(this).data('materi_name');
            let MaterialUnit = $(this).data('materi_unit');
            let MaterialCode = $(this).data('materi_code');

            //jika ada method('put'), maka hapus
            if (document.querySelector('.put')) {
                document.querySelector('.put').remove();
            }

            $("#materialId").val(MaterialId);
            $("#materialName").val(MaterialName);
            $("#materialUnit").val(MaterialUnit);
            $("#materialCode").val(MaterialCode);

            // $('#material_tags').val(MaterialId);

            $('#exampleModalLabel').html('Add New Material');
            $('#materialDelete').hide();

            // zain: element not found
            // $('#name_tags').hide();

            MaterialForm.action = "{{ route('materials.store') }}";
        })


        $(document).on('click', '#materialEditButton', function() {
            //get material data
            const materialId = $(this).data('material-id');
            const material = materials.find(material => material.id === materialId);

            //get input elements
            const tagsInputEl = $('#tags');

            // tags options check
            const selectOpts = tagsInputEl.find('option');
            const optValues = selectOpts.map((i, select) => select.innerHTML);

            material.tags.map(tag => {
                if ($.inArray(tag, optValues) === -1) {
                    tagsInputEl.append(`<option>${tag}</option>`);
                };
            })


            const MaterialForm = document.querySelector('#materialForm');
            const materialDelete = document.querySelector('#materialDelete');

            // let MaterialId = $(this).data('materi_id');
            // let MaterialName = $(this).data('materi_name');
            // let MaterialUnit = $(this).data('materi_unit');
            // let MaterialCode = $(this).data('materi_code');

            //jika ada method('put'), maka hapus
            if (document.querySelector('.put')) {
                document.querySelector('.put').remove();
            }

            //menambahkan method('put')
            var put = document.createElement('div');
            put.setAttribute('class', 'put');
            put.innerHTML = '@METHOD('PUT')';

            $("#materialId").val(material.id);
            $("#idMaterialDelete").val(material.id);
            $("#materialName").val(material.name);
            $("#materialUnit").val(material.unit);
            $("#materialCode").val(material.code);
            $('#exampleModalLabel').html('Edit Material');

            tagsInputEl.val(material.tags).change();

            $('#materialDelete').show();

            // zain: element not found
            // $('#name_tags').show();

            MaterialForm.appendChild(put);
            MaterialForm.action = "{{ route('materials.update', '') }}/" +
                material
                .id;
            DeleteForm.action = "{{route('materials.destroy', '')}}/"+material
                .id;
                 //maaf saya melawan hukum pemrograman. meskipun route-nya ke store tapi karena ada METHOD("PUT") jadinya ke redirect ke update

            //note : tadi itu sebenarnya routenya dialihkan ke materials.update tapi dengan id 1 saja.
        });
    </script>
@endpush
