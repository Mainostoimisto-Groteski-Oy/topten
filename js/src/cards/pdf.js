/* global Ajax */
jQuery(document).ready(($) => {
	function getChild(node) {
		const tag = node.nodeName.toLowerCase();

		const nodeData = {
			tag,
			value: node.textContent,
			children: [],
			attributes: {
				class: node.className,
			},
		};

		if (tag === 'input' || tag === 'textarea') {
			nodeData.tag = 'input';

			const id = node.id;
			const labelText = $(`label[for="${id}"] .label-text`).text();

			nodeData.attributes = {
				type: node.type,
				value: node.value,
				checked: node.checked,
				label: labelText,
				rows: node.rows,
			};
		} else if (tag === 'picture') {
			const image = node.querySelector('img');

			nodeData.tag = 'img';

			nodeData.attributes = {
				src: image.src,
				alt: image.alt,
				class: image.className,
				width: image.width,
				height: image.height,
			};
		} else if (node.childNodes.length > 0) {
			nodeData.value = null;

			node.childNodes.forEach((child) => {
				nodeData.children.push(getChild(child));
			});
		} else {
			nodeData.value = node.textContent;
		}

		return nodeData;
	}

	/**
	 * Get child data
	 *
	 * @param {Object} child
	 */
	function getChildData(child) {
		const data = getChild(child);

		return data;
	}

	/**
	 * Generate PDF data from card HTML content
	 *
	 * @param {Object} parent
	 */
	function generatePDFdata(parent) {
		const data = {
			count: 0,
			rows: [],
		};

		const rows = parent.children('.row-block');

		rows.each((rowIndex, row) => {
			data.count += 1;

			const rowData = {
				count: 0,
				columns: [],
				attributes: {
					class: row.className,
				},
			};

			$(row)
				.find('.column-item')
				.each((columnIndex, column) => {
					rowData.count += 1;

					const width = getComputedStyle(column).getPropertyValue('--width');

					const columnData = {
						data: [],
						attributes: {
							class: column.className,
							width: width ? width.replace('%', '') : 100,
						},
					};

					$(column)
						.children()
						.each((childIndex, child) => {
							columnData.data.push(getChildData(child));
						});

					rowData.columns.push(columnData);
				});

			data.rows.push(rowData);
		});

		return data;
	}

	/**
	 * Print page as PDF
	 */
	$('.save-as-pdf').on('click', function () {
		// Get card type from dataset (data-type="tulkintakortti")
		const type = $(this).data('type');

		const cardContent = $(`article.${type} .card-content`);

		// Get card content
		const data = generatePDFdata(cardContent);

		console.log(data);

		$.ajax({
			url: Ajax.url,
			type: 'POST',
			data: {
				action: 'topten_generate_pdf',
				nonce: Ajax.nonce,
				title: 'ÄTESTIÖ',
				data,
				article_url: 'testest',
			},
		})
			.done((response) => {
				const byteCharacters = window.atob(response.data);
				const byteNumbers = new Array(byteCharacters.length);

				for (let i = 0; i < byteCharacters.length; i += 1) {
					byteNumbers[i] = byteCharacters.charCodeAt(i);
				}

				const byteArray = new Uint8Array(byteNumbers);
				const blob = new Blob([byteArray], { type: 'application/pdf' });
				const url = URL.createObjectURL(blob);

				// const a = $('<a style="display:none"></a>');

				// a.attr('href', url);
				// a.attr('download', 'test.pdf');

				// $('body').append(a);

				// a[0].click();

				// window.URL.revokeObjectURL(url);

				// a.remove();

				window.open(url, '_blank');
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				// eslint-disable-next-line no-console
				console.error(textStatus, errorThrown);
			});
	});

	/**
	 * Save card to database
	 */
	$('.save-card').on('click', function () {
		const data = {};

		$('.lomakekortti .card-content input, .lomakekortti .card-content textarea').each(function () {
			if ($(this).hasClass('prevent-save')) {
				return;
			}

			const id = $(this).attr('id');
			let value;

			if ($(this).attr('type') === 'checkbox') {
				value = $(this).is(':checked');
			} else {
				value = $(this).val();
			}

			data[id] = value;
		});

		console.log(data);

		$.ajax({
			url: Ajax.url,
			type: 'POST',
			data: {
				action: 'topten_save_card',
				nonce: Ajax.nonce,
				data,
			},
		})
			.done((response) => {
				console.log(response);
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				// eslint-disable-next-line no-console
				console.error(textStatus, errorThrown);
			});
	});

	/**
	 * Load card from database
	 */
	$('.load-card').on('click', function () {
		const cardCode = $('#card-code').val();

		$.ajax({
			url: Ajax.url,
			type: 'POST',
			data: {
				action: 'topten_load_card',
				nonce: Ajax.nonce,
				code: cardCode,
			},
		})
			.done((response) => {
				if (response.success) {
					for (const index in response.data) {
						if ($(`#${index}`).attr('type') === 'checkbox') {
							if (response.data[index] === 'true') {
								$(`#${index}`).prop('checked', true);
							} else {
								$(`#${index}`).prop('checked', false);
							}
						} else {
							$(`#${index}`).val(response.data[index]);
						}
					}
				}
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				// eslint-disable-next-line no-console
				console.error(textStatus, errorThrown);
			});
	});
});
