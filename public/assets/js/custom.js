/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

[...document.querySelectorAll('.modal')].map(modal => {

	modal.setTitle = function (text) {
		this.querySelector('.modal-title').innerText = text
	}

	modal.addAlert = function (message, color = 'danger') {
		addAlert(this.querySelector('.modal-body'), message, color);
	}

	modal.disableAllButtons = function () {
		[...this.querySelectorAll('button')].map(button => {
			button.disabled = true
		})
	}

	modal.enableAllButtons = function () {
		[...this.querySelectorAll('button')].map(button => {
			button.disabled = false
		})
	}
});

$('.modal').on('show.bs.modal', function () {
	const openedModal = $('.modal.show');

	if (openedModal.length > 0) {
		const savedStyle = $('body').attr('style');

		function keepBodyStyle() {
			$('body').attr('style', savedStyle);
			$('body').addClass('modal-open');
		}

		keepBodyStyle();

		$(this).on('hidden.bs.modal', function () {
			keepBodyStyle();
		});
	}
});

[...document.querySelectorAll('form')].map(form => {
	form.removeMethodInput = function () {
		$('[name="_method"][value="put"]').remove()
	}

	form.addMethodInput = function () {
		this.removeMethodInput()
		$(this).append('<input type="hidden" name="_method" value="put">')
	}

	form.disableAll = function () {
		[...this.querySelectorAll('input, button, textarea, select')].map(element => {
			element.disabled = true;
			element.classList.add('disabled');
		});

		[...this.querySelectorAll('a')].map(element => {
			element.classList.add('btn-sm');
			element.classList.add('btn');
			element.classList.add('disabled');
		});
	}

	form.enableAll = function () {
		[...this.querySelectorAll('input, button, textarea, select')].map(element => {
			if (!element.dataset.excludeEnabling || element.dataset.excludeEnabling === 'false') {
				element.disabled = false;
				element.classList.remove('disabled');
			}
		});

		[...this.querySelectorAll('a')].map(element => {
			element.classList.remove('btn-sm');
			element.classList.remove('btn');
			element.classList.remove('disabled');
		});
	};

	form.removeAllValidationClass = function () {
		[...this.elements].map(element => {
			element.classList.remove('is-invalid')
		})
	};

	[...form.elements].map(element => {
		element.onkeypress = function () {
			this.classList.remove('is-invalid')
		};
	})
});

const addAlert = (parentEl, message, color = 'danger') => {
	const alertEl = document.createElement('div');
	alertEl.setAttribute('class', `alert alert-${color} alert-dismissible fade`);
	alertEl.innerHTML = `
		<div class="alert-body">
			<button class="close" data-dismiss="alert">
				<span>&times;</span>
			</button>
			${message}
		</div>
	`

	$(parentEl).prepend(alertEl);

	setTimeout(() => {
		alertEl.classList.add('show')
	}, 300);
}

const handleNotifications = notifications =>
	notifications?.map(notif =>
		addAlert(document.querySelector('.section-body'), notif.messageHtml || notif.message, notif.color))

// TODO: internationaliztion from env
const numberToCurrency = number => number.toLocaleString('id-ID', {
	style: 'currency',
	currency: 'IDR',
	maximumFractionDigits: 4,
	minimumFractionDigits: 0
});

function csrf_token() {
	return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function tab_system_init(page, defaultTab = 'list') {
	window.onhashchange = function () {
		const activeTab = location.hash?.replace(/^#/, '');
		$(`.nav .nav-item a[href="#${activeTab || defaultTab}"][data-toggle="tab"].nav-link`).tab('show');

	}

	window.onhashchange()

	$('.nav .nav-item a[data-toggle="tab"].nav-link').on('click', function (e) {
		window.history.pushState(null, null, `${page}${location.search}${e.target.hash || e.target.getAttribute('href')}`)
	})
}

function findGetParameter(parameterName) {
	var result = null,
		tmp = [];
	var items = location.search.substr(1).split("&");
	for (var index = 0; index < items.length; index++) {
		tmp = items[index].split("=");
		if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
	}
	return result;
}

function materialInDetailSelect2ResultTemplate(data) {
	if (data.loading) {
		return data.text;
	}

	const atPrinted = data.materialInDetail?.material_in.at ? `IN: ${moment(data.materialInDetail
		.material_in.at).format('DD-MM-YYYY')}` : '';
	const edPrinted = data.materialInDetail?.material_in.expired_at ? `ED: ${moment(data.materialInDetail
		.material_in.expired_at).format('DD-MM-YYYY')}` : '';

	return $(`
		<div style='line-height: 1em'>
			<small>${edPrinted}${edPrinted ? ', ' : ''}${atPrinted}</small>
			<p class='my-0' stlye='font-size: 1.1em'><b>${data.materialInDetail.material.id_for_human}<b></p>
			<small><b>${data.materialInDetail.stock.qty}</b>/${data.materialInDetail.qty} ${data.materialInDetail.material.unit} @ ${numberToCurrency(data.materialInDetail.price)}</small>
		</div>
	`)
}

function materialInDetailSelect2SelectionTemplate(data) {
	if (!data.id) {
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
	const atPrinted = materialInDetail.material_in?.at ? `IN: ${moment(materialInDetail.material_in
		?.at).format('DD-MM-YYYY')}` : '';

	const edPrinted = materialInDetail.material_in?.expired_at ? `ED: ${moment(materialInDetail.material_in?.expired_at).format('DD-MM-YYYY')}` : '';

	return $(`
		<div>
			${codePrinted}
			${namePrinted}
			${brandPrinted}
			<small class='text-muted ml-2'>
				${edPrinted}${edPrinted ? ', ' : ''}${atPrinted}
			</small>
		</div>
	`);
}

function materialSelect2TemplateResultAndSelection(data) {
	if (!data.id) {
		return data.text;
	}

	const material = data.material;

	const codePrinted = material?.code ?
		'<small class=\'text-muted\'><b>' +
		material?.code + '</b></small> - ' : '';
	const brandPrinted = material?.brand ?
		'<small class=\'text-muted\'>(' +
		material?.brand + ')</small>' : '';
	const namePrinted = material?.name;

	return $(`
		<div>
			${codePrinted}
			${namePrinted}
			${brandPrinted}
		</div>
	`);
}

function productInDetailSelect2ResultTemplate(data) {
	if (data.loading) {
		return data.text;
	}

	const atPrinted = data.productInDetail?.product_in.at ? `IN: ${moment(data.productInDetail
		.product_in.at).format('DD-MM-YYYY')}` : '';
	const edPrinted = data.productInDetail?.product_in.expired_at ? `ED: ${moment(data.productInDetail
		.product_in.expired_at).format('DD-MM-YYYY')}` : '';

	return $(`
		<div style='line-height: 1em'>
			<small>${edPrinted}${edPrinted ? ', ' : ''}${atPrinted}</small>
			<p class='my-0' stlye='font-size: 1.1em'><b>${data.productInDetail.product.id_for_human}<b></p>
			<small><b>${data.productInDetail.stock.qty}</b>/${data.productInDetail.qty} ${data.productInDetail.product.unit} @ ${numberToCurrency(data.productInDetail.price)}</small>
		</div>
	`)
}

function productInDetailSelect2SelectionTemplate(data) {

	if (!data.id) {
		return data.text;
	}

	const productInDetail = data.productInDetail || data.element.productInDetail;

	const codePrinted = productInDetail.product?.code ?
		'<small class=\'text-muted\'><b>' +
		productInDetail.product?.code + '</b></small> - ' : '';
	const brandPrinted = productInDetail.product?.code ?
		'<small class=\'text-muted\'>(' +
		productInDetail.product?.brand + ')</small>' : '';
	const namePrinted = productInDetail.product?.name;
	const atPrinted = productInDetail.product_in?.at ? `IN: ${moment(productInDetail.product_in
		?.at).format('DD-MM-YYYY')}` : '';

	const edPrinted = productInDetail.product_in?.expired_at ? `ED: ${moment(productInDetail.product_in?.expired_at).format('DD-MM-YYYY')}` : '';

	return $(`
		<div>
			${codePrinted}
			${namePrinted}
			${brandPrinted}
			<small class='text-muted ml-2'>
				${edPrinted}${edPrinted ? ', ' : ''}${atPrinted}
			</small>
		</div>
	`);
}

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