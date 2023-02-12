// TODO: clear table first when user do search

document.addEventListener('alpine:init', () => {
	Alpine.data('dataTable', (config) => {
		return {
			dataTable: null,

			init() {
				if (!$.fn.dataTable.isDataTable(this.$el)) {
					this.dataTable = this.initDataTable(this.$el);
				} else {
					this.dataTable = $(this.$el)
				}
			},

			tagOnClick(searchText) {
				return this.dataTable.DataTable().search(searchText).draw()
			},

			initDataTable($el) {
				return $($el).dataTable({
					processing: true,
					search: {
						return: true,
					},
					language: {
						// TODO: url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/{{ app()->getLocale() }}.json'
						url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
					},
					serverSide: true,
					ajax: {
						url: config.ajaxUrl,
						dataSrc: json => {
							this.$dispatch('material-in:set-data-list', json.data);
							return json.data;
						},
						beforeSend: function (request) {
							request.setRequestHeader(
								"Authorization",
								`Bearer ${config.token}`
							)
						},
						cache: true
					},
					order: [1, 'desc'],
					columns: config.columns

				});
			}
		}
	});
});