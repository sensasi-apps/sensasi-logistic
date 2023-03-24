@include('components.assets._select2')
@include('components.alpine-data._crud')
@include('components.alpine-data._datatable')

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<h2 class="section-title text-capitalize">
    {{ __('product in list') }}
    <button x-data type="button" @@click="$dispatch('product-in:open-modal', null)"
        class="ml-2 btn btn-success">
        <i class="fas fa-plus-circle"></i> {{ __('Add') }}
    </button>
</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table x-data="dataTable(productInDatatableConfig)" @@product-in:datatable-draw.document="draw"
                class="table table-striped" style="width:100%">
            </table>
        </div>
    </div>
</div>

@push('modal')
    <div x-data="crud(productInCrudConfig)" @@product-in:open-modal.document="openModal"
        @@product-in:set-data-list.document="setDataList">
        <x-_modal centered>
            <p x-show="formData.manufacture" class="text-danger">
                *{{ ucfirst(__('this data can be edit only from manufacture menu')) }}</p>

            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}"
                x-effect="formData.id; $nextTick(() => formData.manufacture?.id && formData.id ? $el.disableAll() : $el.enableAll())">

                <div class="row">
                    <div class="col form-group" x-id="['text-input']">
                        <label :for="$id('text-input')">{{ __('validation.attributes.code') }}</label>
                        <input type="text" class="form-control" x-model="formData.code" :id="$id('text-input')">
                    </div>

                    <div class="col form-group" x-id="['select']">
                        <label :for="$id('select')">{{ __('validation.attributes.type') }}</label>
                        <select class="form-control" name="type" required :id="$id('select')" :value="formData.type"
                            x-effect="$($el).val(formData.type).change()" x-init="$(document).ready(function() {
                                $($el).select2({
                                    tags: true,
                                    dropdownParent: $el.closest('.modal-body'),
                                    data: {{ Js::from($productInTypes) }}.map(type => ({
                                        id: type,
                                        text: type
                                    }))
                                }).on('select2:close', (e) => {
                                    formData.type = e.target.value;
                                })
                            })"></select>
                    </div>
                </div>

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('validation.attributes.at') }}</label>
                    <input type="date" class="form-control" required :id="$id('input')" x-model="formData.at">
                </div>

                <div class="form-group" x-id="['textarea']">
                    <label :for="$id('textarea')">{{ __('validation.attributes.note') }}</label>
                    <textarea x-model="formData.note" class="form-control" name="note" :id="$id('textarea')" rows="3"
                        style="height:100%;"></textarea>
                </div>

                <div class="d-flex justify-content-center my-2">
                    <a href="javascript:;" @@click="formData.details.push({})"
                        class="badge badge-success text-capitalize"><i class="fas fa-plus mr-2"></i>
                        {{ __('add product') }}</a>
                </div>

                <div class="form-group">
                    <label class="text-capitalize">{{ __('items') }}</label>

                    <div class="list-group">
                        <template x-for="(detail, $i) in formData.details">
                            <div class="list-group-item p-4">
                                <div class="form-group mb-3" x-id="['select-input']">
                                    <label :for="$id('select-input')"
                                        class="text-capitalize">{{ __('validation.attributes.material') }}</label>

                                    <select :id="$id('select-input')" class="form-control"
                                        :disabled="detail.out_details?.length > 0"
                                        :data-exclude-enabling="detail.out_details?.length > 0"
                                        x-effect="$($el).val(detail.product_id).change();" x-init="$($el).select2({
                                            dropdownParent: $el.closest('.modal-body'),
                                            placeholder: '{{ __('Product') }}',
                                            data: products.map(product => ({
                                                id: product.id,
                                                text: product.id_for_human,
                                                product: product
                                            })),
                                            templateResult: productSelect2TemplateResultAndSelection,
                                            templateSelection: productSelect2TemplateResultAndSelection,
                                        }).on('select2:select', (e) => {
                                            detail.product_id = e.target.value;
                                        });" required>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col form-group mb-3" x-id="['text-input']">
                                        <label :for="$id('text-input')"
                                            class="text-capitalize">{{ __('validation.attributes.qty') }}</label>
                                        <div class="input-group input-group-sm">
                                            <input :id="$id('text-input')" class="form-control form-control-sm"
                                                type="number" x-model="detail.qty"
                                                :min="detail.out_details?.reduce((a, b) => a + b.qty, 0)" required>

                                            <div class="input-group-append">
                                                <span class="input-group-text h-auto" x-data="{ unit: '' }"
                                                    x-effect="unit = products.find(product => detail.product_id == product.id)?.unit"
                                                    x-show="unit" x-text="unit"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col form-group mb-3" x-id="['text-input']">
                                        <label :for="$id('text-input')"
                                            class="text-capitalize">{{ __('validation.attributes.price') }}</label>
                                        <input :id="$id('text-input')" x-model="detail.price"
                                            class="form-control form-control-sm" min="0" type="number" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col form-group" x-id="['date-input']">
                                        <label :for="$id('date-input')"
                                            class="text-capitalize">{{ __('validation.attributes.manufactured_at') }}</label>
                                        <input :id="$id('date-input')" class="form-control form-control-sm"
                                            :max="formData.at" x-model="detail.manufactured_at" type="date">
                                    </div>

                                    <div class="col form-group" x-id="['date-input']">
                                        <label :for="$id('date-input')"
                                            class="text-capitalize">{{ __('validation.attributes.expired_at') }}</label>
                                        <input :id="$id('date-input')" class="form-control form-control-sm"
                                            :min="formData.at" x-model="detail.expired_at" type="date">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-end">
                                    <strong x-init="$($el).tooltip()" title="{{ __('subtotal') }}" x-data="{ subtotal_price: 0 }"
                                        x-effect="subtotal_price = detail.price * detail.qty"
                                        x-text="numberToCurrency(subtotal_price || 0)">
                                    </strong>

                                    <x-_disabled-delete-button x-show="detail.out_details?.length > 0"
                                        x-init="$($el).tooltip()" :title="__('cannot be deleted. Product(s) has been used')" />

                                    <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                                        x-show="!(detail.out_details?.length > 0)"
                                        :disabled="detail.out_details?.length > 0"
                                        @@click.prevent="removeDetail($i)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="form-group d-flex justify-content-between">
                    <div>
                        <label class="text-capitalize">{{ __('total') }}</label>
                        <div>
                            <strong x-data="{ total_price: 0 }"
                                x-effect="total_price = formData.details?.reduce((a, b) => a + b.qty * b.price, 0)"
                                x-text="numberToCurrency(total_price || 0)"></strong>
                        </div>
                    </div>

                    <div>
                        <a href="javascript:;" @@click="formData.details.push({})"
                            class="badge badge-success text-capitalize"><i class="fas fa-plus mr-1"></i>
                            {{ __('add material') }}</a>
                    </div>
                </div>
            </form>

            @slot('footer')
                <div>
                    <button class="btn btn-success" :disabled="!!(formData.manufacture)"
                        :class="isFormLoading ? 'btn-progress' : ''" :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty"
                        class="btn btn-icon btn-outline-warning"><i class="fas fa-undo"></i></button>
                </div>

                <div>
                    <x-_disabled-delete-button x-show="formData.has_out_details" x-init="$($el).tooltip()" :title="__('cannot be deleted. Product(s) has been used')" />

                    <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                        @@click="openDeleteModal" x-show="formData.id && !formData.has_out_details">
                        <i class="fas fa-trash"></i>
                    </button>
                @endslot
        </x-_modal>

        <x-_delete-modal x-on:submit.prevent="submitDelete" />
    </div>
@endpush

@push('js')
    <script>
        // page scripts

        const products = @json(App\Models\Product::all());

        const productInCrudConfig = {
            blankData: {
                'id': null,
                'code': null,
                'type': null,
                'at': null,
                'note': null,
                'details': [{}]
            },

            dispatchEventsAfterSubmit: ['product-in:datatable-draw', 'product:datatable-reload'],

            routes: {
                store: '{{ route('product-ins.store') }}',
                update: '{{ route('product-ins.update', '') }}/',
                destroy: '{{ route('product-ins.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('add new product in') }}` : `{{ __('edit product in') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('delete product in') }}: ` + this.formData.id_for_human;
            }
        };

        const productInDatatableConfig = {
            serverSide: true,
            setDataListEventName: 'product-in:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ $datatableAjaxUrl['product_in'] }}',
            columns: [{
                data: 'code',
                title: '{{ __('validation.attributes.code') }}'
            }, {
                data: 'at',
                title: '{{ __('validation.attributes.at') }}',
                render: at => moment(at).format('DD-MM-YYYY')
            }, {
                data: 'type',
                title: '{{ __('validation.attributes.type') }}',
            }, {
                data: 'note',
                title: '{{ __('validation.attributes.note') }}'
            }, {
                orderable: false,
                title: '{{ __('Items') }}',
                data: 'details',
                name: 'details.product.name',
                width: '20%',
                render: details => details.map(detail => {
                    const productName = detail.product?.name;
                    const stockQty = detail.stock?.qty || 0;
                    const detailQty = detail.qty;

                    const text = `${productName} (${stockQty}/${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-success" @click="search('${productName}')">${text}</a>`;
                }).join('')
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" href="javascript:;" @click="$dispatch('product-in:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }]
        };
    </script>
@endpush
