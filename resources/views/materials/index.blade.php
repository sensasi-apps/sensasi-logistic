@extends('layouts.main')

@section('title', __('Material'))

@push('css-lib')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('main-content')
    <div></div>
    <div class="section-body">
        <h2 class="section-title">
            {{ __('Material list') }}
            <button id="tambahMateri" type="button" class="ml-2 btn btn-success" data-toggle="modal" data-target="#materialModal"
                data-materi_id="" data-materi_code="" data-materi_name="" data-materi_unit="" data-materi_tags="">
                <i class="fas fa-plus-circle"></i> Tambah
            </button>
        </h2>

        <div class="">
            <div class="card">
                @if ($materials->count() == 0)
                    <p class="text-danger">Materials is empty!</p>
                @else
                    <table class="table table-striped">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Details</th>
                        </tr>

                        @foreach ($materials as $materi)
                            <tr>
                                <td>{{ $materi->code }}</td>
                                <td>{{ $materi->name }}</td>
                                <td>{{ $materi->unit }}</td>
                                <td><a href="#" id="ubahMateri" class="btn btn-primary" data-toggle="modal"
                                        data-target="#materialModal" data-materi_id="{{ $materi->id }}"
                                        data-materi_code="{{ $materi->code }}" data-materi_name="{{ $materi->name }}"
                                        data-materi_unit="{{ $materi->unit }}"
                                        data-materi_tags="{{ $materi->tags_json }}"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>


@endsection

@push('js-lib')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

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
                    <form action="{{ route('materials.destroy', 1) }}" method="post">
                        @csrf
                        @METHOD('PUT')
                        <input type="hidden" name="id" id="idMaterialDelete">
                        <button type="submit" id="materialDelete" class="btn btn-danger">Delete</button>
                        <button type="submit" form="materialForm" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).on('click', '#tambahMateri', function() {
            const MaterialForm = document.querySelector('#materialForm');
            const materialDelete = document.querySelector('#materialDelete');

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

            $('#material_tags').val(MaterialId);

            $('#exampleModalLabel').html('Add New Material');

            $('#materialDelete').hide();
            $('#name_tags').hide();

            MaterialForm.action = "{{ route('materials.store') }}";
        })

        $(document).on('click', '#ubahMateri', function() {
            const MaterialForm = document.querySelector('#materialForm');
            const materialDelete = document.querySelector('#materialDelete');

            let MaterialId = $(this).data('materi_id');
            let MaterialName = $(this).data('materi_name');
            let MaterialUnit = $(this).data('materi_unit');
            let MaterialCode = $(this).data('materi_code');

            //jika ada method('put'), maka hapus
            if (document.querySelector('.put')) {
                document.querySelector('.put').remove();
            }

            //menambahkan method('put')
            var put = document.createElement('div');
            put.setAttribute('class', 'put');
            put.innerHTML = '@METHOD('PUT')';

            $("#materialId").val(MaterialId);
            $("#idMaterialDelete").val(MaterialId);
            $("#materialName").val(MaterialName);
            $("#materialUnit").val(MaterialUnit);
            $("#materialCode").val(MaterialCode);
            $('#exampleModalLabel').html('Edit Material');

            $('#material_tags').val(MaterialId);

            $('#materialDelete').show();
            $('#name_tags').show();

            MaterialForm.appendChild(put);
            MaterialForm.action = "{{route('materials.store')}}/"+MaterialId; //maaf saya melawan hukum pemrograman. meskipun route-nya ke store tapi karena ada METHOD("PUT") jadinya ke redirect ke update

            //note : tadi itu sebenarnya routenya dialihkan ke materials.update tapi dengan id 1 saja.
        });
    </script>
@endpush

