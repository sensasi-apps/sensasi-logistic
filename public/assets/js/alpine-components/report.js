document.addEventListener('alpine:init', () => {
	Alpine.data('reportComponent', () => ({
		printContent: null,

		init() {
			const dateRange = findGetParameter('daterange')?.split('_');
			this.startDate = dateRange ? moment(dateRange[0]) : moment().startOf('month');
			this.endDate = dateRange ? moment(dateRange[1]) : moment().endOf('month');
		},

		daterangepicker() {
			return $(this.$el).daterangepicker({
				autoUpdateInput: false,
				startDate: this.startDate,
				endDate: this.endDate,
				ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
						'month').endOf('month')]
				}
			}, this.cb)
		},

		cb(start, end, label) {
			const form = document.createElement('form');

			dateRangeInput = document.createElement('input');
			dateRangeInput.value = start.format('YYYY-MM-DD') + '_' + end.format('YYYY-MM-DD');
			dateRangeInput.name = 'daterange';

			labelInput = document.createElement('input');
			labelInput.value = label.toLowerCase();
			labelInput.name = 'label';

			form.appendChild(dateRangeInput);
			form.appendChild(labelInput);

			document.querySelector('body').appendChild(form);
			form.submit();
		},

		printTable(title, subtitle) {
			const wrapperDiv = document.createElement('div');
			wrapperDiv.classList.add('print-only');

			const logo = document.body.querySelector('.sidebar-brand a').cloneNode(true);
			logo.style.color = '#000';
			logo.style.fontWeight = '700';
			logo.style.textDecoration = 'none';
			logo.style.letterSpacing = '1.5px';
			logo.style.textTransform = 'uppercase';

			const logoWrapper = document.createElement('div');
			logoWrapper.classList.add('text-right');
			logoWrapper.appendChild(logo);

			wrapperDiv.appendChild(logoWrapper);

			const titleEl = document.createElement('h4');
			titleEl.innerText = title;
			titleEl.classList.add('mb-0');
			titleEl.classList.add('mt-3');
			wrapperDiv.appendChild(titleEl);

			const subtitleEl = document.createElement('p')
			subtitleEl.innerText = subtitle;
			titleEl.classList.add('mt-0');
			subtitleEl.classList.add('mb-4');
			wrapperDiv.appendChild(subtitleEl);

			const table = this.$el.parentElement.querySelector('table').cloneNode(true);
			table.classList.add('table-sm');
			wrapperDiv.appendChild(table);

			document.body.appendChild(wrapperDiv);
			window.print();
			wrapperDiv.remove();
		}
	}));
});