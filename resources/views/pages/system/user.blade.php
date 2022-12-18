@extends('layouts.main')

@section('title', __('Material'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')
    <div class="section-body">
        <h2 class="section-title">
            {{ __('User List') }}
            <button type="button" class="ml-2 btn btn-success addMaterialButton" data-toggle="modal"
                data-target="#userFormModal">
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
@endsection

@push('js')
    <div class="modal fade" id="userFormModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="materialFormModalLabel">{{ __('Add new material') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_material">

                    <form method="POST" id="userForm">
                        @csrf

                        <input type="hidden" name="id" id="idInput">

                        <div class="form-group">
                            <label for="emailInput">{{ __('Email') }}</label>
                            <input type="email" class="form-control" name="email" id="emailInput" required>
                        </div>

                        <div class="form-group">
                            <label for="nameInput">{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" required id="nameInput">
                        </div>

                        <div class="form-group">
                            <label for="pwInput">{{ __('Password user') }}</label>
                            <input type="password" class="form-control" name="password" required id="pwInput" placeholder="Password">
                        </div>

                        <div class="form-group">
                            <label for="pwInput2">{{ __('Password Confirm') }}</label>
                            <input type="password" class="form-control" name="password2" required id="pwInput2" placeholder="Password Confirmation">
                        </div>

                        <div class="form-group">
                            <label for="role">{{ __('Add Role') }}</label>
                            <select class="form-control select2" multiple='multiple' id="role" name="role[]">
                                <!-- <option value="Super Admin">Super Admin</option>
                                <option value="Stackholder">Stackholder</option>
                                <option value="Manufacture">Manufacture</option>
                                <option value="Sales">Sales</option>
                                <option value="Warehouse">Warehouse</option> -->
                            </select>
                        </div>

                    </form>
                    <div class="d-flex justify-content-between">
                        <button type="submit" form="userForm" class="btn btn-primary">{{ __('Save') }}</button>

                        <button id="deleteFormModalButtonToggle" type="submit" class="btn btn-icon btn-outline-danger"
                            data-toggle="tooltip" title="{{ __('Delete') }}"
                            onclick="$('#userDeleteConfirmationModal').modal('show');">
                            <i class="fas fa-trash" style="font-size: 1rem !important"></i>
                        </button>

                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userDeleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="materialFormModalLabel">{{ __('Are you sure') }}?</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal_body_material" style="font-size: 1.1rem">
                    {{ __('This action can not be undone') }}.
                    {{ __('Do you still want to delete') }} <b style="font-size: 1.5rem" id="deleteUserName"></b>
                    <form method="post" id="deleteForm">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="id" id="deleteId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="deleteForm" class="btn btn-danger"
                        id="">{{ __('Yes') }}</button>
                    <button data-dismiss="modal" class="btn btn-secondary" id="">{{ __('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let users
        const tagsSelect = $('#tagsSelect')
        let materialDatatable = $('#materialDatatable')
        const roles = {{ Js::from(App\Models\Role::all()) }};

        $('#role').select2({
            dropdownParent: $('#userForm'),
            placeholder: '{{ __('Roles') }}',
            data: roles.map(role => {
                return {
                    text: role.name
                }
            })
        });

        const deletePutMethodInput = () => {
            $('[name="_method"][value="put"]').remove()
        }

        const addPutMethodInput = () => {
            $('#userForm').append($('@method('put')'))
        }

        const setFormValue = material => {
            const roles = material.roles?.map(function(detail){
                console.log(detail)
                return detail.name
            })

            $('#role').val(roles).trigger('change')

            console.log(roles)
            // $('#role').val(['Stackholder', 'Super Admin']).trigger('change')
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
            emailInput.value = material.email || null
            deleteId.value = material.id
        }


        const datatableSearch = tag =>
            materialDatatable.DataTable().search(tag).draw()


        $(document).on('click', '.addMaterialButton', function() {
            $('#materialFormModalLabel').html('{{ __('Add new material') }}')


            deletePutMethodInput();
            setFormValue({});

            $('#pwInput').attr('required', '')
            $('#pwInput2').attr('required', '');

            deleteFormModalButtonToggle.style.display = "none";
            userForm.action = "{{ url('system/user/') }}";
        })

        $(document).on('click', '.editMaterialButton', function() {
            $('#materialFormModalLabel').html('{{ __('Edit user') }}')

            const userId = $(this).data('user-id');
            const user = users.find(user => user.id === userId);

            setFormValue(user);
            deletePutMethodInput();
            addPutMethodInput();
            // console.log(user.roles)

            deleteFormModalButtonToggle.style.display = "block";
            $('#pwInput').removeAttr('required');
            $('#pwInput2').removeAttr('required');

            userForm.action = "{{ url('system/user/') }}/" +
                user
                .id;

            deleteUserName.innerHTML = user.name
            deleteForm.action = "{{ url('system/user/') }}/" + user
                .id;
        });

        $(document).ready(function() {
            materialDatatable = materialDatatable.dataTable({
                processing: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                },
                serverSide: true,
                ajax: {
                    url: '{{ action('\App\Http\Controllers\Api\DatatableController', 'User') }}?with=roles',
                    dataSrc: json => {
                        users = json.data;
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
                    data: 'email',
                    title: '{{ __('Email') }}'
                }, {
                    data: 'name',
                    title: '{{ __('Name') }}'
                }, 
                {
                    data: 'roles',
                    name: 'roles',
                    title: '{{ __('Roles') }}',
                    render: roles => roles?.map(role =>
                        `<a href="#" onclick="datatableSearch('${role.name}')" class="m-1 badge badge-success">${role.name}</a>`
                    ).join('') || null,
                }, 
                {
                    render: function(data, type, row) {
                        const editButton = $(
                            '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                        )
                        editButton.attr('data-toggle', 'modal')
                        editButton.attr('data-target', '#userFormModal')
                        editButton.addClass('editMaterialButton');
                        editButton.attr('data-user-id', row.id)
                        return editButton.prop('outerHTML')
                    },
                    orderable: false
                }]
            });
        });
    </script>
@endpush
