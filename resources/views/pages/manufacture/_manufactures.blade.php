@include('components.assets._select2')
@include('components.alpine-data._crud')
@include('components.alpine-data._datatable')

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<h2 class="section-title">
    {{ __('Manufacture List') }}
    <button x-data type="button" @@click="$dispatch('manufacture:open-modal', null)"
        class="ml-2 btn btn-primary">
        <i class="fas fa-plus-circle"></i> {{ __('Add') }}
    </button>
</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table x-data="dataTable(manufactureInDatatableConfig)" @@manufacture:datatable-draw.document="draw"
                class="table table-striped" style="width:100%">
            </table>
        </div>
    </div>
</div>

@push('modal')
    <div x-data="crud(manufactureInCrudConfig)" @@manufacture:open-modal.document="openModal"
        @@manufacture:set-data-list.document="setDataList">
        <x-_modal size="xl" centered>

            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}"
                x-effect="formData.id; $nextTick(() => formData.manufacture?.id && formData.id ? $el.disableAll() : $el.enableAll())">

                <div class="row">
                    <div class="col form-group" x-id="['text-input']">
                        <label :for="$id('text-input')">{{ __('Code') }}</label>
                        <input type="text" class="form-control" x-model="formData.code" :id="$id('text-input')">
                    </div>

                    <div class="col form-group" x-id="['input']">
                        <label :for="$id('input')">{{ __('Date') }}</label>
                        <input type="date" class="form-control" required :id="$id('input')"
                            :value="formData.at ? moment(formData.at).format('YYYY-MM-DD') : ''"
                            @@change="formData.at = $event.target.value">
                    </div>
                </div>

                <div class="form-group" x-id="['textarea']">
                    <label :for="$id('textarea')">{{ __('Note') }}</label>
                    <textarea x-model="formData.note" class="form-control" name="note" :id="$id('textarea')" rows="3"
                        style="height:100%;"></textarea>
                </div>

                <!-- TABS LIST -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="material-out-tab" data-toggle="tab" data-target="#material-out"
                            type="button" role="tab" aria-controls="material-out"
                            aria-selected="true">{{ __('Material Out') }}</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="product-in-tab" data-toggle="tab" data-target="#product-in"
                            type="button" role="tab" aria-controls="product-in"
                            aria-selected="false">{{ __('Product In') }}</button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content">

                    <!-- MATERIAL OUT TAB CONTENT -->
                    <div class="tab-pane fade show active" id="material-out" role="tabpanel"
                        aria-labelledby="material-out-tab">
                        <div class="d-flex justify-content-center my-4">
                            <a href="javascript:;" @@click="formData.material_out.details.push({})"
                                class="badge badge-danger mr-3"><i class="fas fa-plus"></i>
                                {{ __('Add material outs') }}</a>
                        </div>

                        <div class="px-0" style="overflow-x: auto">
                            <div style="width: 100%">
                                <div class="row mx-0 my-4">
                                    <div class="font-weight-bold col-5 pl-0 ">{{ __('Name') }}</div>
                                    <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Price') }}</div>
                                    <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Qty') }}</div>
                                    <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Subtotal') }}</div>
                                </div>

                                {{-- DETAILS LOOP --}}
                                <template x-for="(detail, $i) in formData.material_out?.details">
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
                                            <input class="form-control" type="number" x-model="detail.qty" min="1"
                                                required>

                                            <div class="input-group-append">
                                                <span class="input-group-text" x-data="{ unit: '' }"
                                                    x-effect="unit = detail.material_in_detail?.material.unit"
                                                    x-show="unit" x-text="unit"></span>
                                            </div>
                                        </div>

                                        <div class="col-2 pl-4 pr-0" x-data="{ subtotal_price: 0 }"
                                            x-effect="subtotal_price = (detail.qty * detail.material_in_detail?.price || 0)"
                                            x-text="intToCurrency(subtotal_price)">
                                        </div>

                                        <div class="col-1 pl-4 pr-0">
                                            <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                                                @@click.prevent="formData.material_out.details.splice($i, 1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                {{-- END DETAILS LOOP --}}

                                {{-- TOTAL --}}
                                <div class="row mx-0 my-4">
                                    <div class="font-weight-bold col-9 px-0 text-right text-uppercase">Total</div>
                                    <div class="font-weight-bold col-2 pl-4 pr-0"
                                        x-effect="$data.total_in_price = formData.material_out?.details?.reduce((a, b) => a + b.material_in_detail?.price * b.qty, 0)"
                                        x-text="intToCurrency($data.total_in_price || 0)"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PRODUCT TAB CONTENT -->
                    <div class="tab-pane fade" id="product-in" role="tabpanel" aria-labelledby="product-in-tab">
                        <div class="d-flex justify-content-center my-4">
                            <a href="javascript:;" @@click="formData.product_in.details.push({})"
                                class="badge badge-success mr-3"><i class="fas fa-plus"></i>
                                {{ __('Add product in') }}</a>
                        </div>

                        <div class="px-0" style="overflow-x: auto">
                            <div style="width: 100%">
                                <div class="row mx-0 my-4">
                                    <div class="font-weight-bold col-5 pl-0 ">{{ __('Name') }}</div>
                                    <div class="font-weight-bold col-2 pl-4 pr-0">{{ __('Qty') }}</div>
                                    <div class="font-weight-bold col-2 pl-4 pr-0">
                                        {{ __('Cost') }}

                                        <a x-init="$($el).tooltip({ boundary: 'window' })" title="{{ __('Auto calculate from material outs') }}"
                                            @@click="formData.product_in?.details.forEach(detail => detail.price = total_in_price / formData.product_in.details.length / (detail.qty || (detail.qty = 1)))"
                                            class="text-warning">
                                            <i class="fas fa-magic"></i>
                                        </a>
                                    </div>
                                    <div class="font-weight-bold col-1 pl-4 pr-0">{{ __('Subtotal') }}</div>
                                </div>

                                {{-- DETAILS LOOP --}}
                                <template x-for="(detail, $i) in formData.product_in?.details">
                                    <div class="form-group row mx-0 mb-4 align-items-center">
                                        <div class="col-5 px-0">
                                            <select class="form-control" :disabled="detail.out_details?.length > 0"
                                                :data-exclude-enabling="detail.out_details?.length > 0"
                                                x-effect="$($el).val(detail.product_id).change();" x-init="$($el).select2({
                                                    dropdownParent: $el.closest('.modal-body'),
                                                    placeholder: '{{ __('Product') }}',
                                                    data: products.map(product => ({
                                                        id: product.id,
                                                        text: null,
                                                        product: product
                                                    })),
                                                    templateResult: productSelect2TemplateResultAndSelection,
                                                    templateSelection: productSelect2TemplateResultAndSelection,
                                                }).on('select2:select', (e) => {
                                                    detail.product_id = e.target.value;
                                                });"
                                                required>
                                            </select>
                                        </div>

                                        <div class="col-2 pl-4 pr-0 input-group">
                                            <input class="form-control" type="number" x-model="detail.qty"
                                                :min="detail.out_details?.reduce((a, b) => a + b.qty, 0)" required>

                                            <div class="input-group-append">
                                                <span class="input-group-text" x-data="{ unit: '' }"
                                                    x-effect="unit = products.find(product => detail.product_id == product.id)?.unit"
                                                    x-show="unit" x-text="unit"></span>
                                            </div>
                                        </div>

                                        <div class="col-2 pl-4 pr-0">
                                            <input x-model="detail.price" class="form-control" min="0"
                                                type="number" step="any" required>
                                        </div>

                                        <div class="col-2 pl-4 pr-0" x-data="{ subtotal_price: 0 }"
                                            x-effect="subtotal_price = detail.price * detail.qty"
                                            x-text="intToCurrency(subtotal_price || 0)">
                                        </div>

                                        <div class="col-1 pl-4 pr-0">

                                            <x-_disabled-delete-button x-show="detail.out_details?.length > 0"
                                                x-init="$($el).tooltip()" :title="__('cannot be deleted. Product(s) has been used')" />

                                            <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                                                x-show="!(detail.out_details?.length > 0)"
                                                :disabled="detail.out_details?.length > 0"
                                                @@click.prevent="formData.product_in.details.splice($i, 1)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- TOTAL --}}
                                <div class="row mx-0 my-4">
                                    <div class="font-weight-bold col-9 px-0 text-right text-uppercase">Total</div>
                                    <div class="col-2 pl-4 pr-0">
                                        <div class="font-weight-bold"
                                            x-effect="$data.total_out_price = formData.product_in?.details?.reduce((a, b) => a + b.qty * b.price, 0)"
                                            x-text="intToCurrency(total_out_price || 0)"></div>
                                        <div x-show="total_in_price && total_out_price && intToCurrency(total_in_price) != intToCurrency(total_out_price)"
                                            x-transition class="text-danger">
                                            {{ __('Total cost is not equal to total price') }}</div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @slot('footer')
                <div>
                    <button type="submit" class="btn btn-success"
                        :disabled="intToCurrency($data.total_in_price || ($data.total_in_price = 0)) != intToCurrency(
                            $data.total_out_price || ($data.total_out_price = 0))"
                        :class="isFormLoading ? 'btn-progress' : ''" :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty"
                        class="btn btn-icon btn-outline-warning"><i class="fas fa-undo"></i></button>
                </div>

                <div>
                    <x-_disabled-delete-button
                        x-show="formData.product_in?.details?.find(detail => detail.out_details?.length > 0)"
                        x-init="$($el).tooltip()" :title="__('cannot be deleted. Product(s) has been used')" />

                    <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                        @@click="openDeleteModal"
                        x-show="formData.id && !(formData.product_in?.details?.find(detail => detail.out_details?.length > 0))">
                        <i class="fas fa-trash"></i>
                    </button>
                @endslot
        </x-_modal>

        <x-_delete-modal x-on:submit.prevent="submitDelete" />
    </div>
@endpush

@push('js')
    <script>
        const products = @json(App\Models\Product::all());

        function productSelect2TemplateResultAndSelection(data) {

            if (!data.id) {
                return data.text;
            }

            const product = data.product;

            const brandPrinted = product?.brand ?
                '<small class=\'text-muted\'>(' +
                product?.brand + ')</small>' : '';

            const codePrinted = product?.code ?
                '<small class=\'text-muted\'><b>' +
                product?.code + '</b></small> - ' : '';

            return $(`
				<div>
					${codePrinted}
					${product?.name}
					${brandPrinted}
				</div>
			`);
        }

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

        const manufactureInCrudConfig = {
            blankData: {
                'id': null,
                'code': null,
                'at': null,
                'note': null,
                'material_out': {
                    'details': [{}]
                },
                'product_in': {
                    'details': [{}]
                }
            },

            refreshDatatableEventName: 'manufacture:datatable-draw',

            routes: {
                store: '{{ route('manufactures.store') }}',
                update: '{{ route('manufactures.update', '') }}/',
                destroy: '{{ route('manufactures.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('Add New Manufacture') }}` : `{{ __('Edit Manufacture') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('Delete Manufacture') }}: ` + this.formData.id_for_human;
            }
        };

        const manufactureInDatatableConfig = {
            serverSide: true,
            setDataListEventName: 'manufacture:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ $manufactureDatatableAjaxUrl }}',
            columns: [{
                data: 'code',
                title: '{{ __('validation.attributes.code') }}'
            }, {
                data: 'at',
                title: '{{ __('validation.attributes.at') }}',
                render: at => moment(at).format('DD-MM-YYYY')
            }, {
                data: 'note',
                title: '{{ __('validation.attributes.note') }}'
            }, {
                orderable: false,
                title: '{{ __('Material') }}',
                data: 'material_out.details',
                name: 'material_out.details.material_in_detail.material.name',
                render: details => details.map(detail => {
                    const materialName = detail.material_in_detail?.material.name;
                    const detailQty = detail.qty;

                    const text = `${materialName} (${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-danger" @click="search('${materialName}')">${text}</a>`;
                }).join('')
            }, {
                orderable: false,
                title: '{{ __('products') }}',
                data: 'product_in.details',
                name: 'product_in.details.product.name',
                render: details => details.map(detail => {
                    const productName = detail.product?.name;
                    const stockQty = detail.stock?.qty;
                    const detailQty = detail.qty;

                    const text = `${productName} (${stockQty}/${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-success" @click="search('${productName}')">${text}</a>`;
                }).join('')
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" href="javascript:;" @click="$dispatch('manufacture:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }]
        };
    </script>
@endpush
