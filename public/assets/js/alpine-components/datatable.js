document.addEventListener('alpine:init', () => {
	Alpine.data('dataTable', CONFIG => {
		return {
			dataTable: null,

			init() {
				if (!$.fn.dataTable.isDataTable(this.$el)) {
					this.dataTable = this.initDataTable(this.$el);
				} else {
					this.dataTable = $(this.$el)
				}
			},

			search(searchText) {
				return this.dataTable.DataTable().search(searchText).draw()
			},

			draw() {
				return this.dataTable.DataTable().draw();
			},

			initDataTable($el) {
				return $($el).dataTable({
					processing: true,
					search: {
						return: true,
					},
					language: {
						url: `https://cdn.datatables.net/plug-ins/1.13.1/i18n/${CONFIG.locale}.json`
					},
					serverSide: true,
					ajax: {
						url: CONFIG.ajaxUrl,
						dataSrc: json => {
							this.$dispatch(CONFIG.setDataListEventName, json.data);
							return json.data;
						},
						beforeSend: function (request) {
							request.setRequestHeader(
								"Authorization",
								`Bearer ${CONFIG.token}`
							)
						},
						cache: true
					},
					order: CONFIG.order || [1, 'desc'],
					columns: CONFIG.columns

				});
			}
		}
	});
});