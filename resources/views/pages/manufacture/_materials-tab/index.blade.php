@include('components.alpine-data._datatable')

<h2 class="section-title text-capitalize">
    {{ __('material manufacture list') }}
    <button x-data type="button" @@click="$dispatch('material-manufacture:open-modal', null)"
        class="ml-2 btn btn-primary">
        <i class="fas fa-plus-circle"></i> {{ __('Add') }}
    </button>
</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table x-data="dataTable(materialManufactureDatatableConfig)" @@material-manufacture:datatable-draw.document="draw"
                class="table table-striped" style="width:100%">
            </table>
        </div>
    </div>
</div>

@include('pages.manufacture._materials-tab.crud');

@push('js')
    <script>
        const materialManufactureDatatableConfig = {
            serverSide: true,
            setDataListEventName: 'material-manufacture:set-data-list',
            token: '{{ decrypt(request()->cookie('api-token')) }}',
            ajaxUrl: '{{ $materialManufactureDatatableAjaxUrl }}',
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
                title: '{{ __('material') }}',
                data: 'material_out.details',
                name: 'materialOut.details.materialInDetail.material.name',
                render: details => details.map(detail => {
                    const materialName = detail.material_in_detail?.material.name;
                    const detailQty = detail.qty;

                    const text = `${materialName} (${detailQty})`;
                    return `<a href="javascript:;" class="m-1 badge badge-danger" @click="search('${materialName}')">${text}</a>`;
                }).join('')
            }, {
                orderable: false,
                title: '{{ __('material') }}',
                data: 'material_in.details',
                name: 'materialIn.details.material.name',
                render: details => details.map(detail => {
                    const materialName = detail.material?.name;
                    const stockQty = detail.stock?.qty;
                    const detailQty = detail.qty;

                    const text = `${materialName} (${stockQty}/${detailQty})`;
                    return `<a class="m-1 badge badge-success" @click.prevent="search('${materialName}')">${text}</a>`;
                }).join('')
            }, {
                render: function(data, type, row) {
                    return `<a class="btn-icon-custom" @click.prevent="$dispatch('material-manufacture:open-modal', ${row.id})"><i class="fas fa-cog"></i></a>`;
                },
                orderable: false
            }]
        };
    </script>
@endpush
