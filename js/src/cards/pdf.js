/* global Ajax */

jQuery(document).ready(($) => {
	/**
	 * Get child data
	 */
	function getChildData(child) {
		console.log(child);

		const tag = child.tagName.toLowerCase();

		const data = {
			tag,
			children: [],
			attributes: {},
		};

		if (tag === 'picture') {
			const image = child.querySelector('img');

			data.tag = 'img';
			data.attributes.src = image.src;
		}

		child.childNodes.forEach((node) => {
			const nodeData = {
				tag: node.nodeName.toLowerCase(),
				value: node.textContent,
				attributes: {},
			};

			data.children.push(nodeData);
		});

		console.log(data);

		return data;
	}

	/**
	 * Generate PDF data from card HTML content
	 * @param object parent Parent element
	 * @returns array Data array
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
			};

			$(row)
				.find('.column')
				.each((columnIndex, column) => {
					rowData.count += 1;

					const columnData = [];

					$(column)
						.children()
						.each((childIndex, child) => {
							columnData.push(getChildData(child));
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

				window.open(url, '_blank');
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				console.log(textStatus, errorThrown);
			});
	});
});
