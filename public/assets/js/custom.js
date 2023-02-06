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

	modal.disableAll = function () {
		[...this.querySelectorAll('button')].map(button => {
			button.disabled = true
		})
	}

	modal.enableAll = function () {
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

[...document.querySelectorAll('button')].map(button => {
	button.loading = function (isLoadingParam) {
		const isLoading = isLoadingParam === null ? this.classList.contains('btn-progress') : isLoadingParam;

		if (isLoading) {
			this.classList.add('disabled')
			this.classList.add('btn-progress')
		} else {
			this.classList.remove('disabled')
			this.classList.remove('btn-progress')
		}
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
		[...this.elements].map(element => {
			element.disabled = true;
		})
	}

	form.enableAll = function () {
		[...this.elements].map(element => {
			element.disabled = false;
		})
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
				<span>×</span>
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
	notifications.map(notif =>
		addAlert(document.querySelector('.section-body'), notif.messageHtml, notif.color))

function intToCurrency(number) {
	return number.toLocaleString('id', {
		style: 'currency',
		currency: 'IDR',
		maximumFractionDigits: 0
	})
}