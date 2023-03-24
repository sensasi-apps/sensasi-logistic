@include('components.assets._select2')
@include('components.alpine-data._crud')
@include('components.alpine-data._datatable')

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<h2 class="section-title text-capitalize">
    {{ __('product out list') }}
    <button x-data type="button" @@click="$dispatch('product-out:open-modal', null)"
        class="ml-2 btn btn-danger">
        <i class="fas fa-plus-circle"></i> {{ __('Add') }}
    </button>
</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table x-data="dataTable(productOutDataTableConfig)" @@product-out:datatable-draw.document="draw"
                class="table table-striped" style="width:100%">
            </table>
        </div>
    </div>
</div>

@push('modal')
    <div x-data="crud(productOutCrudConfig)" @@product-out:open-modal.document="openModal"
        @@product-out:set-data-list.document="setDataList">
        <x-_modal centered>
            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}">
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
                                    data: {{ Js::from($productOutTypes) }}.map(type => ({
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
                    <input type="date" class="form-control" required :id="$id('input')" x-model="formData.at"
                        x-effect="formData.details;
                            const detailDates = formData.details?.map(detail => detail.product_in_detail?.product_in?.at).filter(date => date);
                            
                            if (!detailDates || detailDates.length === 0) {
                                return;
                            }

                            if (detailDates?.length === 1) {
                                $el.min = detailDates[0];
                                return;
                            }

                            $el.min = detailDates.reduce((a,b) => a > b ? a : b);
                        ">
                </div>

                <div class="form-group" x-id="['textarea']">
                    <label :for="$id('textarea')">{{ __('validation.attributes.note') }}</label>
                    <textarea x-model="formData.note" class="form-control" name="note" :id="$id('textarea')" rows="3"
                        style="height:100%;"></textarea>
                </div>

                <div x-data="{ total_price: 0 }"
                    x-effect="total_price = 0; formData.details?.forEach(detail => total_price += (detail.qty * detail.material_in_detail?.price || 0));">

                    <div class="form-group">
                        <label class="text-capitalize">{{ __('items') }}</label>

                        <div class="list-group mb-4">
                            <template x-for="(detail, $i) in formData.details">
                                <div class="list-group-item p-4">
                                    <select class="form-control" x-init="initProductInDetailSelect2;
                                    $($el).on('select2:select', (e) => {
                                        detail.product_in_detail = $(e.target).select2('data')[0].productInDetail;
                                        detail.product_in_detail_id = e.target.value;
                                    })"
                                        x-effect="productInDetailSelect2Effect($el, detail.product_in_detail_id, detail.product_in_detail)"
                                        required></select>

                                    <div class="row my-3">
                                        <div class="col d-flex align-items-center" x-id="['input']">
                                            <label :for="$id('input')"
                                                class="mb-0 mr-2">{{ __('validation.attributes.price') }}</label>

                                            <input :id="$id('input')" x-model="detail.price"
                                                class="form-control form-control-sm"
                                                x-effect="detail.price = detail.price ? detail.price : detail.product_in_detail?.product.default_price"
                                                min="0" type="number" required>
                                        </div>

                                        <div class="col d-flex align-items-center" x-id="['input']">
                                            <label :for="$id('input')"
                                                class="mb-0 mr-2">{{ __('validation.attributes.qty') }}</label>
                                            <div class="input-group input-group-sm">
                                                <input :id="$id('input')" class="form-control form-control-sm"
                                                    type="number" x-model="detail.qty" min="1" step="any"
                                                    :max="formData.id ? undefined : detail.product_in_detail?.stock?.qty"
                                                    required>

                                                <div class="input-group-append">
                                                    <span class="input-group-text h-auto" x-data="{ unit: '' }"
                                                        x-effect="unit = detail.product_in_detail?.product.unit"
                                                        x-show="unit" x-text="unit"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-end">
                                        <strong x-init="$($el).tooltip({ boundary: 'window' })" title="{{ __('Subtotal') }}"
                                            x-data="{ subtotal_price: 0 }"
                                            x-effect="subtotal_price = (detail.qty * detail.price || 0)"
                                            x-text="numberToCurrency(subtotal_price || 0)">
                                        </strong>

                                        <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                                            @@click.prevent="removeDetail($i)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="form-group d-flex justify-content-between" x-data="{ total_price: 0 }"
                        x-effect="total_price = formData.details?.reduce((a, b) => a + b.price * b.qty, 0)">
                        <div>
                            <label class="text-capitalize">{{ __('total') }}</label>
                            <div>
                                <strong x-text="numberToCurrency(total_price || 0)"></strong>
                            </div>
                        </div>

                        <div>
                            <a href="javascript:;" @@click="formData.details.push({})"
                                class="badge badge-success text-capitalize"><i class="fas fa-plus mr-2"></i>
                                {{ __('add product') }}</a>
                        </div>
                    </div>
                </div>
            </form>

            @slot('footer')
                <div>
                    <button class="btn btn-success" :class="isFormLoading ? 'btn-progress' : ''" :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty"
                        class="btn btn-icon btn-outline-warning"><i class="fas fa-undo"></i></button>
                </div>

                <div>
                    <button x-show="formData.id" class="btn btn-icon btn-outline-danger" tabindex="-1"
                        @@click="openDeleteModal">
                        <i class="fas fa-trash"></i>
                    </button>

                </div>
            @endslot
        </x-_modal>

        <x-_delete-modal x-on:submit.prevent="submitDelete" />
    </div>
@endpush

@push('js')
    <script>
        function initProductInDetailSelect2() {
            $(this.$el).select2({
                dropdownParent: $(this.$el).closest('.modal-body'),
                placeholder: '{{ __('Product') }}',
                ajax: {
                    delay: 750,
                    cache: true,
                    url: '/api/select2/ProductInDetail',
                    dataType: 'json',
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            'Authorization',
                            'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                        )
                    },
                    processResults: productInDetail => {
                        const data = productInDetail.map(productInDetail => {
                            return {
                                id: productInDetail.id,
                                text: productInDetail.id_for_human,
                                productInDetail: productInDetail
                            }
                        });

                        return {
                            results: data
                        };
                    }
                },
                templateResult: productInDetailSelect2ResultTemplate,
                templateSelection: productInDetailSelect2SelectionTemplate,
                minimumInputLength: 3
            });
        }

        function productInDetailSelect2Effect($el, product_in_detail_id, product_in_detail) {
            if ($($el).find(`option[value="${product_in_detail_id}"]`).length) {
                $($el).val(product_in_detail_id).trigger('change');
            } else {
                var newOption = new Option('', product_in_detail_id, true, true);
                newOption.productInDetail = product_in_detail;
                $($el).append(newOption);
            }
        }

        const productOutCrudConfig = {
            blankData: {
                'id': null,
                'code': null,
                'type': null,
                'at': null,
                'note': null,
                'details': [{}]
            },

            dispatchEventsAfterSubmit: [
                'product-out:datatable-draw',
                'product-in:datatable-draw',
                'product:datatable-reload'
            ],

            routes: {
                store: '{{ route('product-outs.store') }}',
                update: '{{ route('product-outs.update', '') }}/',
                destroy: '{{ route('product-outs.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('add new product out') }}` : `{{ __('edit product out') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('delete product out') }}: ` + this.formData.id_for_human;
            }
        };

        const productOutDataTableConfig = {
            serverSide: true,
            setDataListEventName: 'product-out:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ $datatableAjaxUrl['product_out'] }}',
            columns: [{
                data: 'code',
                title: '{{ __('validation.attributes.code') }}'
            }, {
                data: 'at',
                title: '{{ __('validation.attributes.at') }}',
                render: at => at ? moment(at).format('DD-MM-YYYY') : null
            }, {
                data: 'type',
                title: '{{ __('validation.attributes.type') }}',
            }, {
                data: 'note',
                width: '40%',
                title: '{{ __('validation.attributes.note') }}',
            }, {
                orderable: false,
                title: '{{ __('Items') }}',
                data: 'details',
                name: 'details.productInDetail.product.name',
                width: '20%',
                render: details => details.map(detail => {
                    const productName = detail.product_in_detail.product?.name;
                    const detailQty = detail.qty;

                    const text = `${productName} (${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-danger" @click="search('${productName}')">${text}</a>`;
                }).join('')
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" href="javascript:;" @click="$dispatch('product-out:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }],
        };
    </script>
@endpush
