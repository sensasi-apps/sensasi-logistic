<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"
        integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
        integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

	<title>Manufacture</title>
</head>
<body class="d-flex justify-content-center">
    @if (Session::has('notifications'))
        @foreach (Session::get('notifications') as $notification)
            @php
                $message = $notification;
                $color = 'info';
                
                if (is_array($notification)) {
                    $message = $notification['message'] ?? $notification[0];
                    $color = $notification['class'] ?? ($notification[1] ?? 'info');
                }
            @endphp

            @include('components._alert', [
                'message' => $message,
                'color' => $color,
            ])
        @endforeach
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            @include('components._alert', ['message' => $error])
        @endforeach
    @endif

	<form class="form-group col-12 col-lg-10" id="manufactureFormModal" method="post" action="{{Route('url_generator.store')}}">
        @csrf
		<h2>Manufacture</h2>
        <input type="hidden" name="manufacture[id]" value="">
        <input type="hidden" name="user" value="{{$data}}">
		<div class="mb-3">
			<label for="manufactureCode" class="form-label">Code</label>
			<input type="text" name="manufacture[code]" id="manufactureCode" class="form-control">
		</div>

		<div class="mb-3">
			<label for="manufacturedate" class="form-label">date</label>
			<input type="date" name="manufacture[at]" id="manufacturedate" class="form-control">
		</div>

		<div class="mb-3">
			<label for="manufactureNote" class="form-label">note</label>
			<input type="text" name="manufacture[note]" id="manufactureNote" class="form-control">
		</div>

		<div class="row mb-3">
                <div class="col-lg-6 col-12">
                    <h5 class="mb-3">{{ __('Materials') }}</h5>

                    <div class="px-1" style="overflow-x: auto">
                        <div id="materialOutDetailsParent" style="width: 100%">
                            <div class="row m-0">
                                <label class="col-7">{{ __('Name') }}</label>
                                <label class="col-4">{{ __('Qty') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="">
                        <a href="#" id="addMaterialOutDetailButton" class="btn btn-success btn-sm mr-2"><i
                                class="fas fa-plus"></i> {{ __('More') }}</a>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <h5 class="mb-3">{{ __('Products') }}</h5>

                    <div class="px-1" style="overflow-x: auto">
                        <div id="productInDetailsParent" style="width: 100%">
                            <div class="row m-0">
                                <label class="col-7">{{ __('Name') }}</label>
                                <label class="col-4">{{ __('Qty') }}</label>
                            </div>
                        </div>
                    </div>


                    <div class="">
                        <a href="#" id="addProductInsButton" class="btn btn-success btn-sm mr-2"><i
                                class="fas fa-plus"></i> {{ __('More') }}</a>
                    </div>
                </div>
            </div>

            <div class="mb-3">
            	<button type="submit" class="btn btn-primary">Kirim</button>
            </div>
	</form>

	<script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"
        integrity="sha512-zMfrMAZYAlNClPKjN+JMuslK/B6sPM09BGvrWlW+cymmPmsUT1xJF3P4kxI3lOh9zypakSgWaTpY6vDJY/3Dig=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
                const initMaterialSelects = $selectDom => $selectDom.select2({
                    dropdownParent: $('#manufactureFormModal'),
                    placeholder: '{{ __('Materials') }}',

                    ajax: {
                        url: '/api/select2/MaterialInDetail',
                        dataType: 'json',
                        beforeSend: function(request) {
                            request.setRequestHeader(
                                "Authorization",
                                'Bearer {{ Auth::user()->createToken('user_' . 1)->plainTextToken }}'
                            )
                        },
                        processResults: function(data) {
                            const theResults = data.map(materialInDetail => {
                                tgl = new Date(materialInDetail.material_in.at)
                                return {
                                    
                                    id: materialInDetail.id,
                                    text: `${materialInDetail.material?.name} (${materialInDetail.stock?.qty}) ${tgl.getDate()}-${tgl.getMonth()}-${tgl.getFullYear()}`
                                }
                            })

                            return {
                                results: theResults
                            };
                        }
                    },
                    minimumInputLength: 3
                });

				function addMaterialOutDetailRow(detail) {
                    const nDetailInputSetMaterialOut = $('.materialOutDetailRowDiv').length

                    const materialSelectParentDiv = document.createElement('div')

                    materialSelectParentDiv.setAttribute('class', 'col-6 pl-0 pr-2')

                    const $selectDomMaterialOut = $(`<select required placeholder="{{ __('Material name') }}"></select>`)
                        .addClass('form-control select2 listSelect')
                        .attr('name', `detailsMaterialOut[${nDetailInputSetMaterialOut}][material_in_detail_id]`)

                    $(materialSelectParentDiv).append($selectDomMaterialOut)

                    if (detail.material_in_detail_id) {
                        $selectDomMaterialOut.append(
                            `<option value="${detail.material_in_detail_id}" selected>${detail.material_in_detail?.material.name}</option>`
                        );
                    }

                    initMaterialSelects($selectDomMaterialOut);
                    $selectDomMaterialOut.val(detail.material_in_detail_id).change();

                    const qtyInputParentDiv = document.createElement('div')
                    qtyInputParentDiv.setAttribute('class', 'col-5 px-2')
                    qtyInputParentDiv.innerHTML = `<input class="form-control" name="detailsMaterialOut[${nDetailInputSetMaterialOut}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class',
                        'form-group row materialOutDetailRowDiv mx-0 align-items-center')
                    $(detailRowDiv).append(materialSelectParentDiv)
                    $(detailRowDiv).append(qtyInputParentDiv)
                    // $(detailRowDiv).append(`<input type="hidden" name="" value="${detail.id}">`)

                    materialOutDetailsParent.append(detailRowDiv);
                    if (nDetailInputSetMaterialOut !== 0) {
                        const removeRowButtonParentDiv = document.createElement('div')
                        removeRowButtonParentDiv.setAttribute('class', 'col-1 pl-2 pr-0')
                        $(removeRowButtonParentDiv).append($(
                            '<button class="btn btn-outline-danger btn-icon" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>'
                        ))

                        $(detailRowDiv).append(removeRowButtonParentDiv)
                    }
                }

                function addProductInDetailRow(detail) {

                    const nDetailInputSetProductIn = $('.detailInputSetProductInDiv').length

                    const detailRowDiv = document.createElement('div')
                    detailRowDiv.setAttribute('class', 'form-group row mx-0 align-items-center detailInputSetProductInDiv')
                    productInDetailsParent.append(detailRowDiv);

                    function getProductSelect() {
                        const products = {{ Js::from(App\Models\Product::all()) }};

                        const initProductsSelect = $selectDomProductIn => $selectDomProductIn.select2({
                            dropdownParent: $('#manufactureFormModal'),
                            placeholder: '{{ __('Product') }}',
                            data: [{
                                id: null,
                                text: null
                            }].concat(products.map(product => {
                                return {
                                    id: product.id,
                                    text: product.name
                                }
                            }))
                        });

                        const productSelectParentDiv = document.createElement('div')
                        productSelectParentDiv.setAttribute('class', 'col-7 pl-0 pr-2')
                        const $selectDomProductIn = $(`<select required placeholder="{{ __('Product name') }}"></select>`)
                            .addClass('form-control productSelect')
                            .attr('name', `detailsProductIn[${nDetailInputSetProductIn}][product_id]`)
                        $(productSelectParentDiv).append($selectDomProductIn)
                        initProductsSelect($selectDomProductIn);
                        $selectDomProductIn.val(detail.product_id).change();

                        return productSelectParentDiv
                    }


                    $(detailRowDiv).append(getProductSelect())


                    const qtyInputParentDiv = document.createElement('div')
                    qtyInputParentDiv.setAttribute('class', 'col-4 px-2')
                    $(qtyInputParentDiv).append(
                        `<input class="form-control" name="detailsProductIn[${nDetailInputSetProductIn}][qty]" min="0" type="number" required placeholder="{{ __('Qty') }}" value="${detail.qty || ''}">`
                    )
                    
                    const getRemoveRowButtonParentDiv = () => {
                        const temp = document.createElement('div')
                        temp.setAttribute('class', 'col-1 pl-2 pr-0')
                        $(temp).append($(
                            `<button class="btn btn-outline-danger btn-icon" tabindex="-1" onclick="this.parentNode.parentNode.remove()"><i class="fas fa-trash"></i></button>`
                        ))

                        return temp;
                    }


                    $(detailRowDiv).append(qtyInputParentDiv)

                    if (nDetailInputSetProductIn !== 0) {
                        $(detailRowDiv).append(getRemoveRowButtonParentDiv())
                    }
                }

                $('#addMaterialOutDetailButton').click(function() {
                    addMaterialOutDetailRow({})
                    // console.log('asdasd')
                })

                $('#addProductInsButton').click(function() {
                    addProductInDetailRow({})
                    // console.log('asdasd')
                })
	</script>
</body>
</html>