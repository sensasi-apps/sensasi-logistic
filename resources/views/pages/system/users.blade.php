@extends('layouts.main')

@section('title', __('Users'))

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('js-lib')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"
        integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@include('components.assets._alpinejs')
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
        <form method="POST" id="userForm" x-data="{ isChangePassword: false, isShowPassword: false }"
            @@user-form:open-modal.document="isChangePassword = false; isShowPassword = false">
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

            <div class="form-check mb-4" x-id="['checkbox']">
                <input type="checkbox" class="form-check-input" :id="$id('checkbox')" x-model="isChangePassword">
                <label :for="$id('checkbox')" class="form-check-label">{{ __('change password') }}</label>
            </div>

            <template x-if="isChangePassword">
                <div>
                    <div class="form-group">
                        <label for="pwInput">{{ __('validation.attributes.new_password') }}</label>

                        <div class="input-group">
                            <input :type="isShowPassword ? 'text' : 'password'" class="form-control" name="password"
                                required id="pwInput" minlength="8" maxlength="255">

                            <div class="input-group-append">
                                <button tabindex="-1" type="button" @@click.prevent="isShowPassword = !isShowPassword"
                                    class="btn btn-outline-secondary" type="button">
                                    <i class="fas" :class="isShowPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pwInput2">{{ __('validation.attributes.new_password') }}</label>
                        <input :type="isShowPassword ? 'text' : 'password'" type="password" class="form-control"
                            name="password_confirmation" required id="pwInput2" minlength="8" maxlength="255">
                    </div>
                </div>
            </template>


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

    @hasrole('Super Admin')
        <x-_modal x-data="{
            userUrlCaches: [],
            user_id: null,
            url: null,
            qrGeneratorInstance: null,
            isFormLoading: false,
        }" id="bypass-modal"
            @url-generation:open-modal.document="
            user_id = $event.detail;
            url = userUrlCaches.find(userUrlCache => userUrlCache?.user_id === $event.detail)?.url;
            isFormLoading = false;
            $($el).modal('show');
        "
            :title="__('url generation for bypassing login')" color="warning">
            <div class="d-flex justify-content-center flex-wrap flex-column" style="line-break: anywhere;">
                <div x-show="url" x-transition>
                    <p>{{ __('open the link below on incognito mode, different browser, or different device') }}:</p>

                    <div class="d-flex justify-content-center" x-init="qrGeneratorInstance = new QRCode($el);
                    qrGeneratorInstance.makeCode('https://google.com')"></div>

                    <p class="pt-4">
                        <a class="btn btn-link" style="font-size:1.1em" :href="url" x-text="url"
                            target="_blank" rel="noopener noreferrer"></a>
                    </p>
                </div>

                <form class="d-flex justify-content-center"
                    @@submit.prevent="async () => {
                    isFormLoading = true;

                    const response = await fetch(`{{ route('bypass-login-url-generation', '') }}/${user_id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf_token()
                        }
                    });
        
                    const responseBody = await response.json();
        
                    // if error
                    if (response.status !== 200) {
                        const modalEl = $el.closest('.modal');

                        modalEl.addAlert(responseBody.message, 'danger');
                        console.error(response);
                    }
        
                    // if success
                    if (response.status === 200) {
                        console.log(responseBody);

                        url = responseBody.url;
                        qrGeneratorInstance.makeCode(url);

                        userUrlCaches.push({
                            user_id,
                            url
                        });

                        isFormLoading = false;
                    }
            }">
                    <button class="btn btn-warning text-capitalize" x-transition
                        :class="{
                            'btn-lg': !url,
                            'btn-sm': url,
                            'btn-progress disabled': isFormLoading,
                        }"
                        x-text="url ? '{{ __('regenerate login url') }}' : '{{ __('generate login url') }}'"></button>
                </form>
            </div>
        </x-_modal>
    @endhasrole
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
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                            data: 'email',
                            title: '{{ __('validation.attributes.email') }}'
                        }, {
                            data: 'name',
                            title: '{{ __('validation.attributes.name') }}'
                        }, {
                            data: 'roles',
                            name: 'roles.name',
                            title: '{{ __('validation.attributes.roles') }}',
                            orderable: false,
                            render: roles => roles?.map(role =>
                                `<a href="#" onclick="datatableSearch('${role.name}')" class="m-1 badge badge-primary">${role.name}</a>`
                            ).join('') || null,
                        }, {
                            searchable: false,
                            orderable: false,
                            render: function(data, type, row) {
                                const editButton = $(
                                    `<a class="btn-icon-custom" href="javascript:;" x-data @click="$dispatch('user-form:open-modal')"><i class="fas fa-cog"></i></a>`
                                )

                                editButton.attr('data-toggle', 'modal')
                                editButton.attr('data-target', '#userFormModal')
                                editButton.addClass('editUserButton');
                                editButton.attr('data-user-id', row.id)
                                return editButton.prop('outerHTML')
                            }
                        }
                        @hasrole('Super Admin')
                            , {
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    return `<a href="javascript:;" class="btn-icon-custom text-warning" x-data @click.prevent="$dispatch('url-generation:open-modal', ${row.id})"><i class="fas fa-key"></i></a>`
                                }
                            }
                        @endhasrole
                    ]

                });
            })
        })();
    </script>
@endpush
