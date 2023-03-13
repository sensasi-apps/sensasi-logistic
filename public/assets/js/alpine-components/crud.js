document.addEventListener('alpine:init', () => {
	Alpine.data('crud', CONFIG => ({
		dataList: [],
		formData: {},

		// UI Logic
		isFormLoading: false,
		htmlElements: [],

		getTitle: CONFIG.getTitle,
		getDeleteTitle: CONFIG.getDeleteTitle,

		init() {
			// elements mapping
			const root = this.$el;
			const modals = root.querySelectorAll('.modal');

			this.htmlElements = {
				root: root,
				modal: modals[0],
				form: root.querySelector('form'),
				deleteModal: modals[1],
				deleteForm: modals[1].querySelector('form')
			};
		},

		openModal(event) {
			const dataId = event.detail;
			const isDifferentData = this.formData.id !== dataId;

			if (isDifferentData) {
				let data = CONFIG.blankData;

				if (dataId) {
					data = this.getDataById(dataId);
				}

				this.setFormData(data);
			}

			const title = this.getTitle(dataId);
			this.htmlElements.modal.setTitle(title);

			// open modal
			$(this.htmlElements.modal).modal('show');
		},

		openDeleteModal() {
			// prevent opening delete modal on create
			if (!this.formData.id) {
				console.error('No data id');
				return;
			}

			this.htmlElements.deleteModal.setTitle(this.getDeleteTitle());
			$(this.htmlElements.deleteModal).modal('show');
		},

		setDataList(event) {
			this.dataList = event.detail;
		},

		// Methods
		getDataById(id) {
			return this.dataList.find(data => data.id === id);
		},

		setFormData(data) {
			this.formData = JSON.parse(JSON.stringify(data));
		},

		removeDetail(i) {
			this.formData.details.splice(i, 1);
		},

		restore() {
			this.setFormData(this.getDataById(this.formData.id) || CONFIG.blankData);
		},

		setIsLoading(isLoading) {
			this.isFormLoading = isLoading;

			if (!isLoading) {
				this.htmlElements.modal.enableAllButtons();
				this.htmlElements.form.enableAll();
				this.htmlElements.deleteModal.enableAllButtons();
				this.htmlElements.deleteForm.enableAll();
			}

			if (isLoading) {
				this.htmlElements.modal.disableAllButtons();
				this.htmlElements.form.disableAll();
				this.htmlElements.deleteModal.disableAllButtons();
				this.htmlElements.deleteForm.disableAll();
			}

		},

		get isDirty() {
			const untouchedJsonData = JSON.stringify(this.formData.id ?
				this.getDataById(this.formData.id) : CONFIG.blankData);
			const currentJsonData = JSON.stringify(this.formData);

			return untouchedJsonData !== currentJsonData;
		},

		async submitForm() {
			const endpoint = this.formData.id ? CONFIG.routes.update + this.formData.id :
				CONFIG.routes.store;

			this.setIsLoading(true);

			await this.fetch(endpoint, this.formData.id ? "PUT" : "POST", JSON.stringify(
				this.formData));
			this.setIsLoading(false);
		},

		async submitDelete() {
			// prevent submit delete form on create
			if (!this.formData.id) {
				console.error('No data id');
				return;
			}

			this.setIsLoading(true);
			await this.fetch(CONFIG.routes.destroy + this.formData.id, 'DELETE');
			this.setIsLoading(false);
		},

		async fetch(endpoint, method, body) {
			const response = await fetch(endpoint, {
				method: method,
				headers: {
					"Content-Type": "application/json",
					Accept: "application/json",
					'X-CSRF-TOKEN': csrf_token()
				},
				body: body,
			});

			const responseBody = await response.json();

			// if error
			if (response.status !== 200) {
				let modalEl;

				if (method === 'DELETE') {
					modalEl = this.htmlElements.deleteModal;
				} else {
					modalEl = this.htmlElements.modal;
				}

				modalEl.addAlert(responseBody.message, 'danger');
				console.error(response);
			}

			// if success
			if (response.status === 200) {
				// refresh datatable & etd
				CONFIG.dispatchEventsAfterSubmit.forEach(eventName => this.$dispatch(eventName));

				// clear alert is any
				this.htmlElements.modal.querySelectorAll('.alert').forEach(alert => {
					alert.remove();
				});
				this.htmlElements.deleteModal.querySelectorAll('.alert').forEach(alert => {
					alert.remove();
				});

				// close modal
				if (method === 'DELETE') {
					$(this.htmlElements.deleteModal).on('hidden.bs.modal',
						() => $(this.htmlElements.modal).modal('hide')
					);
					$(this.htmlElements.deleteModal).modal('hide');
				} else {
					$(this.htmlElements.modal).modal('hide');
				}

				// clear form
				this.setFormData(CONFIG.blankData);

				handleNotifications(responseBody.notifications);
			}
		}
	}));
});