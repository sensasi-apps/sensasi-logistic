@extends('layouts.main')
@include('components.alpine-data._datatable')

@section('title', __('user activities'))

@push('js-lib')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsdiff/5.1.0/diff.min.js"
        integrity="sha512-vco9RAxEuv4PQ+iTQyuKElwoUOcsVdp+WgU6Lgo82ASpDfF7vI66LlWz+CZc2lMdn52tjjLOuHvy8BQJFp8a1A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush

@section('main-content')
    <div class="section-body">
        <h2 class="section-title text-capitalize">
            {{ __('user activities') }}
        </h2>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table x-data="dataTable(logDatatableConfig)" class="table table-striped" style="width:100%">
                    </table>
                </div>
            </div>
        </div>

        @php
            $datatableAjaxUrl = route('api.datatable', [
                'model_name' => 'UserActivity',
                'params_json' => urlencode(
                    json_encode([
                        'withs' => ['user'],
                    ]),
                ),
            ]);
        @endphp

        @push('js')
            <script>
                const logDatatableConfig = {
                    serverSide: true,
                    token: '{{ decrypt(request()->cookie('api-token')) }}',
                    ajaxUrl: '{{ $datatableAjaxUrl }}',
                    order: [
                        [4, 'desc']
                    ],
                    columns: [{
                        orderable: false,
                        searchable: false,
                        title: '#',
                        data: 'id',
                    }, {
                        orderable: false,
                        searchable: false,
                        title: '{{ __('user info') }}',
                        render: (data, type, row) => {
                            const actionColor = {
                                'created': 'success',
                                'updated': 'warning',
                                'deleted': 'danger',
                            };
                            return `
                                    <div>{{ __('nama') }}: ${row.user.name}</div>
                                    <div>{{ __('email') }}: ${row.user.email}</div>
                                    <div class="text-${actionColor[row.action]}">{{ __('action') }}: ${row.action}</div>
                                    <div>{{ __('model') }}: ${row.model}</div>
                                    <div>{{ __('ip') }}: ${row.ip}</div>
                                    <div>{{ __('browser') }}: ${row.browser}</div>
                                    <div>{{ __('os') }}: ${row.os}</div>
                                    <div>{{ __('device') }}: ${row.device}</div>
                                `;
                        }
                    }, {
                        visible: false,
                        data: 'user.name',
                        title: '{{ __('validation.attributes.name') }}'
                    }, {
                        visible: false,
                        data: 'user.email',
                        title: '{{ __('validation.attributes.email') }}'
                    }, {
                        data: 'at',
                        title: '{{ __('validation.attributes.at') }}',
                        render: at => moment(at).format('DD-MM-YYYY')
                    }, {
                        data: 'action',
                        visible: false,
                        title: '{{ __('action') }}'

                    }, {
                        visible: false,
                        data: 'model',
                        title: '{{ __('model') }}'
                    }, {
                        orderable: false,
                        data: 'value',
                        title: '{{ __('changes') }}',
                        render: (value, type, row) => {
                            if (typeof value === 'object' && value !== null) {
                                let html = ``;
                                for (var key of Object.keys(value)) {
                                    if (key !== 'at') {
                                        html += `<div>${key}: ${value[key]}</div>`;
                                    }
                                }

                                return html;
                            }

                            return '<i class="text-muted">-</i>';
                        }
                    }, {
                        visible: false,
                        data: 'ip',
                        title: '{{ __('ip') }}'
                    }, {
                        visible: false,
                        data: 'browser',
                        title: '{{ __('browser') }}'
                    }, {
                        visible: false,
                        data: 'os',
                        title: '{{ __('os') }}'
                    }, {
                        visible: false,
                        data: 'device',
                        title: '{{ __('device') }}'
                    }]
                };
            </script>
        @endpush

    </div>
@endsection
