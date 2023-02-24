@extends('layouts.main')

@section('title', __('Users'))

@include('components.assets._datatable')
@include('components.assets._select2')

@section('main-content')
    <div class="section-body">
        <h2 class="section-title">
            {{ __('User List') }}
            <button type="button" class="ml-2 btn btn-primary addUserButton" data-toggle="modal" data-target="#userFormModal">
                <i class="fas fa-plus-circle"></i> {{ __('Add') }}
            </button>
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="userDatatable" style="width:100%">
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <x-_modal id="userFormModal" centered>
        <form method="POST" id="userForm">
            @csrf

            <div class="form-group">
                <label for="emailInput">{{ __('validation.attributes.email') }}</label>
                <input type="email" class="form-control" name="email" value="{{ $user->email ?? '' }}" id="emailInput"
                    required>
            </div>

            <div class="form-group">
                <label for="nameInput">{{ __('validation.attributes.name') }}</label>
                <input type="text" class="form-control" name="name" required value="{{ $user->name ?? '' }}"
                    id="nameInput">
            </div>

            <div class="form-group">
                <label for="pwInput">{{ __('validation.attributes.password') }}</label>
                <input type="password" class="form-control" name="password" required id="pwInput" minlength="8"
                    maxlength="255">
            </div>

            <div class="form-group">
                <label for="pwInput2">{{ __('validation.attributes.password_confirmation') }}</label>
                <input type="password" class="form-control" name="password_confirmation" required id="pwInput2"
                    minlength="8" maxlength="255">
            </div>

            <div class="form-group">
                <label for="role">{{ __('validation.attributes.roles') }}</label>
                <select class="form-control select2" multiple='multiple' id="rolesSelect" name="roles[]">
                </select>
            </div>
        </form>

        @slot('footer')
            <button type="submit" form="userForm" class="btn btn-primary">{{ __('Save') }}</button>

            <button id="deleteFormModalButtonToggle" type="submit" class="btn btn-icon btn-outline-danger"
                data-toggle="tooltip" title="{{ __('Delete') }}" onclick="$('#userDeleteConfirmationModal').modal('show');">
                <i class="fas fa-trash" style="font-size: 1rem !important"></i>
            </button>
        @endslot
    </x-_modal>

    <x-_modal id="userDeleteConfirmationModal" color="danger">
        {{ __('This action can not be undone') }}.
        {{ __('Do you still want to delete') }} <b style="font-size: 1.5rem" id="deleteUserName"></b>
        <form method="post" id="deleteForm">
            @csrf
            @method('delete')
            <input type="hidden" name="id" id="deleteId">
        </form>

        @slot('footer')
            <button type="submit" form="deleteForm" class="btn btn-danger" id="">{{ __('Yes') }}</button>
            <button data-dismiss="modal" class="btn btn-secondary" id="">{{ __('Cancel') }}</button>
        @endslot
    </x-_modal>
@endpush

@push('js')
    <script>
        (function() {

            let users
            let userDatatable = $('#userDatatable')
            const roles = {{ Js::from(Spatie\Permission\Models\Role::all()) }};

            $('#rolesSelect').select2({
                dropdownParent: '#userForm',
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

            const setFormValue = user => {
                $('#rolesSelect').val(user.roles?.map(role => role.name) || []).change();
                nameInput.value = user.name || null
                emailInput.value = user.email || null
                deleteId.value = user.id
            }


            const datatableSearch = tag =>
                userDatatable.DataTable().search(tag).draw()


            $(document).on('click', '.addUserButton', function() {

                deletePutMethodInput();
                setFormValue({});

                $('#pwInput').attr('required', '')
                $('#pwInput2').attr('required', '');

                document.getElementById('userFormModal').setTitle('{{ __('Add new user') }}');
                deleteFormModalButtonToggle.style.display = "none";
                userForm.action = "{{ url('system/users/') }}";
            })

            $(document).on('click', '.editUserButton', function() {

                const userId = $(this).data('user-id');
                const user = users.find(user => user.id === userId);

                setFormValue(user);
                deletePutMethodInput();
                addPutMethodInput();

                deleteFormModalButtonToggle.style.display = "block";
                $('#pwInput').removeAttr('required');
                $('#pwInput2').removeAttr('required');

                deleteUserName.innerHTML = user.name
                document.getElementById('userFormModal').setTitle(`{{ __('Edit user') }}: ${user.name}`);
                userForm.action = "{{ url('system/users/') }}/" + user.id;
                deleteForm.action = "{{ url('system/users/') }}/" + user.id;
            });

            $(document).ready(function() {
                userDatatable = userDatatable.dataTable({
                    processing: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
                    },
                    serverSide: true,
                    ajax: {
                        url: '{{ $userDatatableApiUrl }}',
                        dataSrc: json => {
                            users = json.data;
                            return json.data;
                        },
                        beforeSend: function(request) {
                            request.setRequestHeader(
                                "Authorization",
                                'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                            )
                        },
                        cache: true
                    },
                    order: [],
                    columns: [{
                            data: 'email',
                            title: '{{ __('validation.attributes.name') }}'
                        }, {
                            data: 'name',
                            title: '{{ __('validation.attributes.name') }}'
                        },
                        {
                            data: 'roles',
                            name: 'roles.name',
                            title: '{{ __('validation.attributes.roles') }}',
                            orderable: false,
                            render: roles => roles?.map(role =>
                                `<a href="#" onclick="datatableSearch('${role.name}')" class="m-1 badge badge-primary">${role.name}</a>`
                            ).join('') || null,
                        },
                        {
                            render: function(data, type, row) {
                                const editButton = $(
                                    '<a class="btn-icon-custom" href="#"><i class="fas fa-cog"></i></a>'
                                )
                                editButton.attr('data-toggle', 'modal')
                                editButton.attr('data-target', '#userFormModal')
                                editButton.addClass('editUserButton');
                                editButton.attr('data-user-id', row.id)
                                return editButton.prop('outerHTML')
                            },
                            orderable: false
                        }
                    ]
                });
            })
        })();
    </script>
@endpush
