@include('components.assets._select2')
@include('components.alpine-data._crud')
@include('components.alpine-data._datatable')

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<h2 class="section-title">
    {{ __('Material Out List') }}
    <button x-data type="button" @@click="$dispatch('material-out:open-modal', null)"
        class="ml-2 btn btn-danger">
        <i class="fas fa-plus-circle"></i> {{ __('Add') }}
    </button>
</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table x-data="dataTable(materialOutDataTableConfig)" @@material-out:datatable-draw.document="draw"
                class="table table-striped" style="width:100%">
            </table>
        </div>
    </div>
</div>

@push('modal')
    <div x-data="crud(materialOutCrudConfig)" @@material-out:open-modal.document="openModal"
        @@material-out:set-data-list.document="setDataList">
        <x-_modal centered>
            <p x-show="(formData.product_manufacture || formData.material_manufacture) && formData.id" class="text-danger">
                *{{ ucfirst(__('this data can be edit only from manufacture menu')) }}</p>

            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}"
                x-effect="formData.id; $nextTick(() => (formData.product_manufacture?.id || formData.material_manufacture?.id) && formData.id ? $el.disableAll() : $el.enableAll())">

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
                                    data: {{ Js::from($materialOutTypes) }}.map(type => ({
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
                            const detailDates = formData.details?.map(detail => detail.material_in_detail?.material_in.at).filter(date => date);
                            
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
                                    <select class="form-control" x-init="initMaterialInDetailSelect2;
                                    $($el).on('select2:select', (e) => {
                                        detail.material_in_detail = $(e.target).select2('data')[0].materialInDetail;
                                        detail.material_in_detail_id = e.target.value;
                                    })"
                                        x-effect="materialInDetailSelect2Effect($el, detail.material_in_detail_id, detail.material_in_detail)"
                                        required></select>

                                    <div class="row my-3">
                                        <div class="col d-flex align-items-center">
                                            <label class="mb-0 mr-2">{{ __('validation.attributes.price') }}</label>
                                            <div x-data="{ priceText: null }"
                                                x-effect="priceText = numberToCurrency(parseInt(detail.material_in_detail?.price) || 0)"
                                                x-text="priceText">
                                            </div>
                                        </div>

                                        <div class="col d-flex align-items-center" x-id="['input']">
                                            <label :for="$id('input')"
                                                class="mb-0 mr-2">{{ __('validation.attributes.qty') }}</label>
                                            <div class="input-group input-group-sm">
                                                <input :id="$id('input')" class="form-control form-control-sm"
                                                    type="number" x-model="detail.qty" :min="1"
                                                    step="any"
                                                    :max="formData.id ? undefined : detail.material_in_detail?.stock.qty"
                                                    required>

                                                <div class="input-group-append">
                                                    <span class="input-group-text h-auto" x-data="{ unit: '' }"
                                                        x-effect="unit = detail.material_in_detail?.material.unit"
                                                        x-show="unit" x-text="unit"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-end">
                                        <strong x-init="$($el).tooltip({ boundary: 'window' })" title="{{ __('Subtotal') }}"
                                            x-data="{ subtotal_price: 0 }"
                                            x-effect="subtotal_price = (detail.qty * detail.material_in_detail?.price || 0)"
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

                    <div class="form-group d-flex justify-content-between">
                        <div>
                            <label class="text-capitalize">{{ __('total') }}</label>
                            <div>
                                <strong x-text="numberToCurrency(total_price || 0)"></strong>
                            </div>
                        </div>

                        <div>
                            <a href="javascript:;" @@click="formData.details.push({})"
                                class="badge badge-success text-capitalize"><i class="fas fa-plus mr-1"></i>
                                {{ __('add material') }}</a>
                        </div>
                    </div>
                </div>

            </form>

            @slot('footer')
                <div>
                    <button class="btn btn-success"
                        :disabled="(formData.product_manufacture || formData.material_manufacture) && formData.id"
                        :class="isFormLoading ? 'btn-progress' : ''" :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty" class="btn btn-icon btn-outline-warning"><i
                            class="fas fa-undo"></i></button>
                </div>

                <div>
                    <button x-show="!(formData.product_manufacture || formData.material_manufacture) && formData.id"
                        class="btn btn-icon btn-outline-danger" tabindex="-1" @@click="openDeleteModal">
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
        function initMaterialInDetailSelect2() {
            $(this.$el).select2({
                dropdownParent: $(this.$el).closest('.modal-body'),
                placeholder: '{{ __('Material') }}',
                ajax: {
                    delay: 750,
                    cache: true,
                    url: '/api/select2/MaterialInDetail',
                    dataType: 'json',
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            'Authorization',
                            'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                        )
                    },
                    processResults: materialInDetails => {
                        const data = materialInDetails.map(materialInDetail => {
                            return {
                                id: materialInDetail.id,
                                text: '',
                                materialInDetail: materialInDetail
                            }
                        });

                        return {
                            results: data
                        };
                    }
                },
                templateResult: materialInDetailSelect2ResultTemplate,
                templateSelection: materialInDetailSelect2SelectionTemplate,
                minimumInputLength: 3
            });
        }

        function materialInDetailSelect2Effect($el, material_in_detail_id, material_in_detail) {
            if ($($el).find(`option[value="${material_in_detail_id}"]`).length) {
                $($el).val(material_in_detail_id).trigger('change');
            } else {
                var newOption = new Option('', material_in_detail_id, true, true);
                newOption.materialInDetail = material_in_detail;
                $($el).append(newOption);
            }
        }

        const materialOutCrudConfig = {
            blankData: {
                'id': null,
                'code': null,
                'type': null,
                'at': null,
                'note': null,
                'details': [{}]
            },

            dispatchEventsAfterSubmit: [
                'material-out:datatable-draw',
                'material-in:datatable-draw',
                'material:datatable-reload'
            ],

            routes: {
                store: '{{ route('material-outs.store') }}',
                update: '{{ route('material-outs.update', '') }}/',
                destroy: '{{ route('material-outs.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('add new material out') }}` : `{{ __('edit material out') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('delete material out') }}: ` + this.formData.id_for_human;
            }
        };

        const materialOutDataTableConfig = {
            serverSide: true,
            setDataListEventName: 'material-out:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ $datatableAjaxUrl['material_out'] }}',
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
                name: 'details.materialInDetail.material.name',
                width: '20%',
                render: details => details?.map(detail => {
                    const materialName = detail.material_in_detail.material?.name;
                    const detailQty = detail.qty;

                    const text = `${materialName} (${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-danger" @click="search('${materialName}')">${text}</a>`;
                }).join('')
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" href="javascript:;" @click="$dispatch('material-out:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }],
        };
    </script>
@endpush
