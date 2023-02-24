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

function intToCurrency(number) {
	return number.toLocaleString('id', {
		style: 'currency',
		currency: 'IDR',
		maximumFractionDigits: 0
	})
}

function csrf_token() {
	return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

function tab_system_init(page) {
	window.onhashchange = function () {
		const activeTab = location.hash.replace(/^#/, '');
		$(`#pageTab a[href="#${activeTab || 'list'}"].nav-link`).tab('show')
	}

	window.onhashchange()

	$('#pageTab a.nav-link').on('click', function (e) {
		window.history.pushState(null, null, `${page}${e.target.hash}`)
	})
}