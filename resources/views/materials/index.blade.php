@extends('layouts.main')

@section('title', 'Basic Page Format')

@section('main-content')
<div></div>
    <div class="section-body">
        <h2 class="section-title">Daftar Material</h2>

        <div class="">
            <div class="card">
                @if($materials->count() == 0)   
                    <p class="text-danger">Materials is empty!</p>
                @else
                    <table class="table table-striped">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Details</th>
                        </tr>

                        @foreach($materials as $materi)
                            <tr>
                                <td>{{$materi->code}}</td>
                                <td>{{$materi->name}}</td>
                                <td>{{$materi->unit}}</td>
                                <td><a href="#" id="ubahMateri" class="btn btn-primary" 
                                    data-toggle="modal" 
                                    data-target="#materialModal" 
                                    data-materi_id="{{$materi->id}}"
                                    data-materi_code="{{$materi->code}}"
                                    data-materi_name="{{$materi->name}}" 
                                    data-materi_unit="{{$materi->unit}}" 
                                    data-materi_tags="{{$materi->tags_json}}"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->
            <button id="tambahMateri" type="button" class="btn btn-primary" data-toggle="modal" data-target="#materialModal" 
            data-materi_id=""
            data-materi_code="" 
            data-materi_name="" 
            data-materi_unit="" 
            data-materi_tags="">
              Tambah Material
            </button>

            <!-- Modal -->
            
@endsection

@push('js')
        <div class="modal fade" id="materialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true text-white">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body" id="modal_body_material">

                    <form action="" method="post" id="materialForm">
                        @csrf

                        <input type="hidden" name="id" id="materialId">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="code" required id="materialCode" placeholder="Material Code">
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" required id="materialName" placeholder="Material Name">
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="unit" required id="materialUnit" placeholder="Material Unit">
                        </div>

                        <div class="mb-3">
                            <select id="selectTags" name="tags_json" multiple="multiple">
                                <option>asdasd</option>
                            </select>
                        </div>
                    </form>
                  </div>
                  <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <form action="{{route('materials.destroy', 1)}}" method="post">
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
        $(document).on('click', '#tambahMateri', function(){
            const MaterialForm = document.querySelector('#materialForm')
            const materialTags = document.querySelector('#materialTags')
            const materialDelete = document.querySelector('#materialDelete')
            let idMaterial = $(this).data('materi_id')

            for(var i = 0; i < 100; i++){
                const name_tags = document.querySelector('#name_tags'+i)

                if (name_tags) {
                    name_tags.remove()
                }
            }

            $("#materialId").val(idMaterial)
            $("#materialName").val($(this).data('materi_name'))
            $("#materialUnit").val($(this).data('materi_unit'))
            $("#materialTagss").html($(this).data('materi_tags'))
            $("#materialCode").val($(this).data('materi_code'))

            materialTags.placeholder = 'Material Tags'

            $('#material_tags').val(idMaterial)
            $('#material_id_tags').val($(this).data('materi_tags'))

            $('#materialDelete').hide()
            $('#name_tags').hide()

            MaterialForm.action = "{{route('materials.store')}}"
        })

        $(document).on('click', '#ubahMateri', function(){
            const MaterialForm = document.querySelector('#materialForm')
            const materialTags = document.querySelector('#materialTags')
            const materialDelete = document.querySelector('#materialDelete')
            var put = document.createElement('div')
            put.innerHTML = '@METHOD("PUT")'
            let idMaterial = $(this).data('materi_id')

            var anu = $(this).data('materi_tags').split(',')

            for(var i = 0; i < 100; i++){
                const name_tags = document.querySelector('#name_tags'+i)

                if (name_tags) {
                    name_tags.remove()
                }
            }

            for (var i = 0; i < anu.length; i++) {
                var form = document.createElement('form')
                var csrf = document.createElement('div')
                
                var material_id_tags = document.createElement('input')
                var name_tags = document.createElement('span')
                var name_tag = document.createTextNode(anu[i])
                var name_tag_button = document.createElement('button')
                var x = document.createElement('i')

                csrf.setAttribute('class', 'csrf')
                csrf.innerHTML = '@csrf'

                x.setAttribute('class', 'fas fa-times')

                name_tag_button.setAttribute('class', 'btn rounded-circle')
                name_tag_button.appendChild(x)

                name_tags.setAttribute('class', 'badge rounded-pill text-white bg-secondary')
                name_tags.setAttribute('id', 'name_tags')
                name_tags.appendChild(name_tag)
                name_tags.appendChild(name_tag_button)

                material_id_tags.setAttribute('type', 'hidden')
                material_id_tags.setAttribute('id','material_id_tags')
                material_id_tags.setAttribute('name', 'id')
                material_id_tags.value = idMaterial

                form.setAttribute('action', "")
                form.setAttribute('method', 'post')
                form.setAttribute('class', 'd-inline-block mb-3 tagsForm')
                form.setAttribute('id', 'name_tags'+i)

                form.appendChild(csrf)
                form.appendChild(put)
                form.appendChild(material_id_tags)
                form.appendChild(name_tags)
                modal_body_material.appendChild(form)
                }

                $("#materialId").val(idMaterial)
                $("#idMaterialDelete").val(idMaterial)
                $("#materialName").val($(this).data('materi_name'))
                $("#materialUnit").val($(this).data('materi_unit'))
                $("#materialTagss").html($(this).data('materi_tags'))
                $("#materialCode").val($(this).data('materi_code'))

                $('#material_tags').val(idMaterial)
                $('#material_id_tags').val($(this).data('materi_tags'))

                $('#materialDelete').show()
                $('#name_tags').show()

                MaterialForm.appendChild(put)
                MaterialForm.action = "{{route('materials.update',1)}}"
        })
    </script>
@endpush('js')


@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush


@push('js')
    <script>
        $("#selectTags").select2({
            tags: true,
            dropdownParent: $('#materialModal'),
            tokenSeparators: [',', ' ']
        })
    </script>
@endpush
