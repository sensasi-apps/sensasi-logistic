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
        <x-_modal size="xl" centered>
            <p x-show="formData.manufacture && formData.id" class="text-danger">
                *{{ __('This data can be edit only from manufacture menu') }}</p>
            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}"
                x-effect="formData.id; $nextTick(() => formData.manufacture?.id && formData.id ? $el.disableAll() : $el.enableAll())">

                <div class="row">
                    <div class="col form-group" x-id="['text-input']">
                        <label :for="$id('text-input')">{{ __('Code') }}</label>
                        <input type="text" class="form-control" x-model="formData.code" :id="$id('text-input')">
                    </div>

                    <div class="col form-group" x-id="['select']">
                        <label :for="$id('select')">{{ __('Type') }}</label>
                        <select class="form-control" name="type" required :id="$id('select')" :value="formData.type"
                            x-effect="$($el).val(formData.type).change()"
                            @@readystatechange.document="$($el).select2({
                                tags: true,
                                dropdownParent: $el.closest('.modal-body'),
                                data: {{ Js::from($materialOutTypes) }}.map(type => ({
                                    id: type,
                                    text: type
                                }))
                            }).on('select2:select', (e) => {
                                formData.type = e.target.value;
                            })"></select>
                    </div>
                </div>

                <div class="form-group" x-id="['input']">
                    <label :for="$id('input')">{{ __('Date') }}</label>
                    <input type="date" class="form-control" required :id="$id('input')"
                        :value="formData.at ? moment(formData.at).format('YYYY-MM-DD') : ''"
                        @@change="formData.at = $event.target.value">
                </div>

                <div class="form-group" x-id="['textarea']">
                    <label :for="$id('textarea')">{{ __('Note') }}</label>
                    <textarea x-model="formData.note" class="form-control" name="note" :id="$id('textarea')" rows="3"
                        style="height:100%;"></textarea>
                </div>

                <div class="d-flex justify-content-center my-2">
                    <a href="javascript:;" @@click="formData.details.push({})"
                        class="badge badge-success mr-3"><i class="fas fa-plus"></i> {{ __('Add material') }}</a>
                </div>

                <div class="px-0" style="overflow-x: auto" x-data="{ total_price: 0 }"
                    x-effect="total_price = 0; formData.details?.forEach(detail => total_price += (detail.qty * detail.material_in_detail?.price || 0));">
                    <div style="width: 100%">
                        <div class="row mx-0 my-4">
                            <div class="font-weight-bold col-5 pl-0 ">{{ __('Name') }}</div>
                            <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Price') }}</div>
                            <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Qty') }}</div>
                            <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Subtotal') }}</div>
                        </div>

                        {{-- DETAILS LOOP --}}
                        <template x-for="(detail, $i) in formData.details">
                            <div class="form-group row mx-0 mb-4 align-items-center">
                                <div class="col-5 px-0">
                                    <select class="form-control" x-init="initMaterialInDetailSelect2;
                                    $($el).on('select2:select', (e) => {
                                        detail.material_in_detail = $(e.target).select2('data')[0].materialInDetail;
                                        detail.material_in_detail_id = e.target.value;
                                    })"
                                        x-effect="materialInDetailSelect2Effect($el, detail.material_in_detail_id, detail.material_in_detail)"
                                        required></select>
                                </div>

                                <div class="col-2 pl-4 pr-0" x-data="{ priceText: null }"
                                    x-effect="priceText = intToCurrency(detail.material_in_detail?.price || 0)"
                                    x-text="priceText">
                                </div>

                                <div class="col-2 pl-4 pr-0 input-group">
                                    <input class="form-control" type="number" x-model="detail.qty" min="1" required>

                                    <div class="input-group-append">
                                        <span class="input-group-text" x-data="{ unit: '' }"
                                            x-effect="unit = detail.material_in_detail?.material.unit" x-show="unit"
                                            x-text="unit"></span>
                                    </div>
                                </div>

                                <div class="col-2 pl-4 pr-0" x-data="{ subtotal_price: 0 }"
                                    x-effect="subtotal_price = (detail.qty * detail.material_in_detail?.price || 0)"
                                    x-text="intToCurrency(subtotal_price)">
                                </div>

                                <div class="col-1 pl-4 pr-0">
                                    <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                                        @@click.prevent="removeDetail($i)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                        {{-- END DETAILS LOOP --}}

                        {{-- TOTAL --}}
                        <div class="row mx-0 my-4">
                            <div class="font-weight-bold col-9 px-0 text-right text-uppercase">Total</div>
                            <div class="font-weight-bold col-2 pl-4 pr-0" x-text="intToCurrency(total_price || 0)"></div>
                        </div>
                    </div>
                </div>
            </form>

            @slot('footer')
                <div>
                    <button class="btn btn-success" :disabled="formData.manufacture && formData.id"
                        :class="isFormLoading ? 'btn-progress' : ''" :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty" class="btn btn-icon btn-outline-warning"><i
                            class="fas fa-undo"></i></button>
                </div>

                <div>
                    <button x-show="!formData.manufacture && formData.id" class="btn btn-icon btn-outline-danger"
                        tabindex="-1" @@click="openDeleteModal">
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
                    delay: 500,
                    cache: true,
                    url: '/api/select2/MaterialInDetail',
                    dataType: 'json',
                    beforeSend: function(request) {
                        request.setRequestHeader(
                            'Authorization',
                            'Bearer {{ decrypt(request()->cookie('api-token')) }}'
                        )
                    },
                    processResults: materialInDetail => {
                        const data = materialInDetail.map(materialInDetail => {
                            return {
                                id: materialInDetail.id,
                                text: null,
                                materialInDetail: materialInDetail
                            }
                        });

                        return {
                            results: data
                        };
                    }
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }

                    const datePrinted = data.materialInDetail?.material_in.at ? moment(data.materialInDetail
                        .material_in.at).format('DD-MM-YYYY') : null;

                    return $(`
                        <div style='line-height: 1em;'>
                            <small>${datePrinted}</small>
                            <p class='my-0' stlye='font-size: 1.1em'><b>${data.materialInDetail.material.id_for_human}<b></p>
                            <small><b>${data.materialInDetail.stock.qty}</b>/${data.materialInDetail.qty} ${data.materialInDetail.material.unit} @ ${intToCurrency(data.materialInDetail.price)}</small>
                        </div>
                    `);
                },
                templateSelection: function(data) {
                    if (data.text === '{{ __('Material') }}') {
                        return data.text;
                    }

                    const materialInDetail = data.materialInDetail || data.element.materialInDetail;

                    const codePrinted = materialInDetail.material?.code ?
                        '<small class=\'text-muted\'><b>' +
                        materialInDetail.material?.code + '</b></small> - ' : '';
                    const brandPrinted = materialInDetail.material?.code ?
                        '<small class=\'text-muted\'>(' +
                        materialInDetail.material?.brand + ')</small>' : '';
                    const namePrinted = materialInDetail.material?.name;
                    const atPrinted = materialInDetail.material_in?.at ? moment(materialInDetail.material_in
                        ?.at).format('DD-MM-YYYY') : null;

                    return $(`
                        <div>
                            ${codePrinted}
                            ${namePrinted}
                            ${brandPrinted}
                            <small class='text-muted ml-2'>
                                ${atPrinted}
                            </small>
                        </div>
                    `);
                },
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
                'details': [{}],
                'manufacture': {}
            },

            refreshDatatableEventName: 'material-out:datatable-draw',

            routes: {
                store: '{{ route('material-outs.store') }}',
                update: '{{ route('material-outs.update', '') }}/',
                destroy: '{{ route('material-outs.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('Add New Material Out') }}` : `{{ __('Edit Material Out') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('Delete Material Out') }}: ` + this.formData.id_for_human;
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
