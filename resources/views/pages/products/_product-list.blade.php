@include('components.assets._select2')
@include('components.alpine-data._crud')
@include('components.alpine-data._datatable')

{{-- TODO: clearer name for all push and stack --}}
@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<h2 class="section-title">
    {{ __('Product List') }}
    <button x-data type="button" @@click="$dispatch('product:open-modal', null)"
        class="ml-2 btn btn-primary">
        <i class="fas fa-plus-circle"></i> {{ __('Add') }}
    </button>
</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table x-data="dataTable(productListDataTableConfig)" @@product:datatable-reload.document="reload"
                class="table table-striped" style="width:100%">
            </table>
        </div>
    </div>
</div>

@push('modal')
    <div x-data="crud(productListCrudConfig)" @@product:open-modal.document="openModal"
        @@product:set-data-list.document="setDataList">
        <x-_modal centered>
            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}">

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('validation.attributes.code') }}</label>
                    <input type="text" class="form-control" x-model="formData.code" :id="$id('input')">
                </div>

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('validation.attributes.brand') }}</label>
                    <input type="text" class="form-control" :id="$id('input')" x-model="formData.brand">
                </div>

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('validation.attributes.name') }}</label>
                    <input type="text" class="form-control" :id="$id('input')" x-model="formData.name" required>
                </div>

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('validation.attributes.unit') }}</label>
                    <input type="text" class="form-control" :id="$id('input')" x-model="formData.unit" required>
                </div>

                <div class="form-group" x-id="['input']">
                    <label for="priceInput" :for="$id('input')">
                        {{ __('validation.attributes.default_price') }}

                        <x-_icon-tooltip-span :title="__('used to estimate the value of the asset')" icon="fas fa-info-circle" />
                    </label>

                    <input type="number" min="0" class="form-control" :id="$id('input')"
                        x-model="formData.default_price" required>
                </div>

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('validation.attributes.low_qty') }}</label>

                    <x-_icon-tooltip-span :title="__('used for warnings')" icon="fas fa-info-circle" />

                    <div class="input-group">
                        <input type="number" min="0" class="form-control" :id="$id('input')"
                            x-model="formData.low_qty">
                        <div class="input-group-append">
                            <span class="input-group-text" x-show="formData.unit" x-text="formData.unit"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group" x-id="['select']">
                    <label :for="$id('select')">{{ __('validation.attributes.tags') }}</label>
                    <select class="form-control" multiple x-init="$(document).ready(function() {
                        $($el).select2({
                            dropdownParent: $el.closest('.modal-body'),
                            tags: true,
                            tokenSeparators: [',', ' ']
                        }).on('select2:select', () => {
                            formData.tags = $($el).val();
                        })
                    })"
                        x-effect="addOptionIfNotExists($el, formData.tags); $($el).val(formData.tags).change()"
                        :id="$id('select')"></select>
                </div>
            </form>

            @slot('footer')
                <div>
                    <button class="btn btn-success" :class="isFormLoading ? 'btn-progress' : ''" type="submit"
                        :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty" class="btn btn-icon btn-outline-warning"><i
                            class="fas fa-undo"></i></button>
                </div>

                <div>
                    <x-_disabled-delete-button x-show="formData.has_children" x-init="$($el).tooltip()" :title="__('cannot be deleted. Product(s) has been used')" />

                    <template x-if="formData.id && !formData.has_children">
                        <button class="btn btn-icon btn-outline-danger" tabindex="-1"
                            @@click="openDeleteModal">
                            <i class="fas fa-trash"></i>
                        </button>
                    </template>
                </div>
            @endslot
        </x-_modal>

        <x-_delete-modal x-on:submit.prevent="submitDelete" />
    </div>
@endpush

@push('js')
    <script>
        // page scripts
        function addOptionIfNotExists($el, tags) {
            const selectOpts = $($el).find('option')
            const optValues = selectOpts.map((i, select) => select.innerText)

            tags?.map(tag => {
                if ($.inArray(tag, optValues) === -1) {
                    $($el).append(`<option>${tag}</option>`)
                }
            })
        }

        const productListCrudConfig = {
            blankData: {
                'id': null,
                'code': null,
                'brand': null,
                'name': null,
                'default_price': null,
                'unit': null,
                'low_qty': null,
                'tags': []
            },

            dispatchEventsAfterSubmit: ['product:datatable-reload'],

            routes: {
                store: '{{ route('products.store') }}',
                update: '{{ route('products.update', '') }}/',
                destroy: '{{ route('products.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('add new product') }}` : `{{ __('edit product') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('delete product') }}: ` + this.formData.id_for_human;
            }
        };

        const productListDataTableConfig = {
            setDataListEventName: 'product:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ $datatableAjaxUrl['product_list'] }}',
            order: [1, 'asc'],
            columns: [{
                data: 'code',
                title: '{{ __('validation.attributes.code') }}'
            }, {
                data: 'brand',
                title: '{{ __('validation.attributes.brand') }}'
            }, {
                data: 'name',
                title: '{{ __('validation.attributes.name') }}'
            }, {
                data: 'qty',
                title: '{{ __('validation.attributes.qty') }}',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {

                    const isStockLow = row.low_qty && row.qty <= row.low_qty;

                    return isStockLow ?
                        `<b class="text-${row.qty === 0 ? 'danger' : 'warning'} blinking" x-init="$($el).tooltip()" title="${row.qty === 0 ? '{{ __('out of stock') }}' : '{{ __('low qty') }}'}">${data} ${row.unit}</b>` :
                        `${data} ${row.unit}`;
                }
            }, {
                data: 'tags',
                name: 'tags_json',
                width: '15%',
                orderable: false,

                title: '{{ __('validation.attributes.tags') }}',
                render: data => data?.map(tag =>
                    `<a href="javascript:;" class="m-1 badge badge-primary" @click="search('${tag}')">${tag}</a>`
                ).join(''),
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" href="javascript:;" @click="$dispatch('product:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }]
        };
    </script>
@endpush
