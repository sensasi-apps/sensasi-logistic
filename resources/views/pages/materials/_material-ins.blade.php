@include('components.assets._select2')
@include('components.alpine-data._crud')
@include('components.alpine-data._datatable')

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<div>
    <h2 class="section-title">
        {{ __('Material In List') }}
        <button x-data type="button" @@click="$dispatch('material-in:open-modal', null)"
            class="ml-2 btn btn-success">
            <i class="fas fa-plus-circle"></i> Tambah
        </button>
    </h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table x-data="dataTable(dataTableConfig)" @@material-in:datatable-draw.document="draw"
                    class="table table-striped" style="width:100%">
                </table>
            </div>
        </div>
    </div>
</div>

@push('modal')
    <div x-data="crud(metrialInCrudConfig)" @@material-in:open-modal.document="openModal"
        @@material-in:set-data-list.document="setDataList">
        <x-_modal size="xl" centered>
            <form method="POST" @@submit.prevent="submitForm" id="{{ uniqid() }}">

                <div class="row">
                    <div class="col form-group" x-id="['text-input']">
                        <label :for="$id('text-input')">{{ __('Code') }}</label>
                        <input type="text" class="form-control" x-model="formData.code" :id="$id('text-input')">
                    </div>

                    <div class="col form-group" x-id="['select']">
                        <label :for="$id('select')">{{ __('Type') }}</label>
                        <select class="form-control" name="type" required :id="$id('select')" :value="formData.type"
                            x-effect="$($el).val(formData.type).change()" x-on:readystatechange.document="initSelect2"
                            x-init="$($el).on('select2:select', (e) => {
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

                <div class="form-group">
                    <label for="materialInNoteInput">{{ __('Note') }}</label>
                    <textarea x-model="formData.note" class="form-control" name="note" id="materialInNoteInput" rows="3"
                        style="height:100%;"></textarea>
                </div>

                <div class="d-flex justify-content-center my-2">
                    <a href="javascript:;" @@click="formData.details.push({})"
                        class="badge badge-success mr-3"><i class="fas fa-plus"></i> {{ __('Add material') }}</a>

                    {{-- TODO: should not refresh on added; possible solution material select with ajax --}}
                    {{-- <span>
                        {{ __('Material is not on the list') }}?
                        <a href="javascript:;" tabindex="-1" @@click="$dispatch('material:open-modal', null)"
                            class="badge badge-secondary ml-1">{{ __('Add New Material') }}</a>
                    </span> --}}
                </div>


                <div class="px-0" style="overflow-x: auto">
                    <div style="width: 100%">
                        <div class="row mx-0 my-4">
                            <div class="font-weight-bold col-6 pl-0 ">{{ __('Name') }}</div>
                            <div class="font-weight-bold col-1">{{ __('Qty') }}</div>
                            <div class="font-weight-bold col-2">{{ __('Price') }}</div>
                            <div class="font-weight-bold col-2 pr-0">{{ __('Subtotal') }}</div>
                        </div>

                        {{-- DETAILS LOOP --}}
                        <template x-for="(detail, $i) in formData.details">
                            <div x-effect="hasOutDetails = () => detail.out_details?.length > 0"
                                class="form-group row mx-0 mb-4 align-items-center">

                                <div class="col-5 px-0">
                                    <select class="form-control" :disabled="hasOutDetails"
                                        :data-exclude-enabling="hasOutDetails"
                                        x-effect="$($el).val(detail.material_id).change();" x-init="$($el).select2({
                                            dropdownParent: $el.closest('.modal-body'),
                                            data: materials.map(material => ({
                                                id: material.id,
                                                text: material.name + ` (${material.brand})` + (material.code ? ` (${material.code})` : ''),
                                            }))
                                        }).on('select2:select', (e) => {
                                            detail.material_id = e.target.value;
                                        });"
                                        required>
                                    </select>
                                </div>

                                <div class="col-2 pl-4 pr-0 input-group">
                                    <input class="form-control" type="number" x-model="detail.qty"
                                        :min="detail.out_details?.reduce((total, item) => total + item.qty, 0) || 0"
                                        required>

                                    <div class="input-group-append">
                                        <span class="input-group-text" x-data="{ unit: '' }"
                                            x-effect="unit = materials.find(material => detail.material_id == material.id)?.unit"
                                            x-show="unit" x-text="unit"></span>
                                    </div>
                                </div>

                                <div class="col-2 pl-4 pr-0">
                                    <input x-model="detail.price" class="form-control" min="0" type="number"
                                        required>
                                </div>

                                <div class="col-2 pl-4 pr-0" x-data="{ subtotal: detail.price * detail.qty }"
                                    x-effect="subtotal = detail.price * detail.qty"
                                    x-text="subtotal ? intToCurrency(subtotal) : ''">
                                </div>

                                <div class="col-1 pl-4 pr-0">

                                    <x-_disabled-delete-button x-show="hasOutDetails" x-init="$($el).tooltip()"
                                        :title="__('cannot be deleted. Material(s) has been used')" />

                                    <button type="button" class="btn btn-icon btn-outline-danger" tabindex="-1"
                                        x-show="!hasOutDetails()" :disabled="hasOutDetails"
                                        @@click.prevent="removeDetail($i)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </form>

            @slot('footer')
                <div>
                    {{-- TODO: bug on save button, text not hide on loading --}}
                    <button class="btn btn-success" :class="isFormLoading ? 'btn-progress' : ''"
                        :form="htmlElements.form.id">
                        {{ __('Save') }}
                    </button>

                    <button @@click="restore()" x-show="isDirty" class="btn btn-icon btn-outline-warning"><i
                            class="fas fa-undo"></i></button>
                </div>

                <div>
                    <x-_disabled-delete-button x-show="isDetailsUsed" x-init="$($el).tooltip()" :title="__('cannot be deleted. Material(s) has been used')" />

                    <template x-if="!isDetailsUsed">
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

        const materials = @json(App\Models\Material::all());

        function initSelect2() {
            // this = Alpine
            $(this.$el).select2({
                tags: true,
                dropdownParent: this.$el.closest('.modal-body'),
                data: @json($materialInTypes).map(type => ({
                    id: type,
                    text: type
                }))
            })
        }

        const metrialInCrudConfig = {
            blankData: {
                'id': null,
                'code': null,
                'type': null,
                'at': null,
                'note': null,
                'details': [{}]
            },

            refreshDatatableEventName: 'material-in:datatable-draw',

            routes: {
                store: '{{ route('material-ins.store') }}',
                update: '{{ route('material-ins.update', '') }}/',
                destroy: '{{ route('material-ins.destroy', '') }}/',
            },

            getTitle(hasnotId) {
                return !hasnotId ? `{{ __('Add New Material In') }}` : `{{ __('Edit Material In') }}: ` + this
                    .formData.id_for_human;
            },

            getDeleteTitle() {
                return `{{ __('Delete Material In') }}: ` + this.formData.id_for_human;
            }
        };

        const dataTableConfig = {
            locale: '{{ app()->getLocale() }}',
            setDataListEventName: 'material-in:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '/api/datatable/MaterialIn?with=details.material,details.stock,details.outDetails',
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
                name: 'details.material.name',
                width: '20%',
                render: details => details.map(detail => {
                    const materialName = detail.material?.name;
                    const stockQty = detail.stock?.qty;
                    const detailQty = detail.qty;

                    const text = `${materialName} (${stockQty}/${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-success" @click="search('${materialName}')">${text}</a>`;
                }).join('')
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" href="javascript:;" @click="$dispatch('material-in:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }]
        };
    </script>
@endpush
