/* global REST */

function getChildData(child) {
	const data = {
		tag: child.tagName.toLowerCase(),
		children: [],
	};

	child.childNodes.forEach((node) => {
		const nodeData = {
			tag: node.nodeName.toLowerCase(),
			value: node.textContent,
		};

		data.children.push(nodeData);
	});

	return data;
}

function generatePDFdata(parent) {
	const data = {
		count: 0,
		rows: [],
	};

	Array.from(parent.children).forEach((row) => {
		if (row.className.includes('row-block')) {
			data.count += 1;

			const rowData = {
				columns: [],
			};

			const columns = row.querySelectorAll('.column');

			columns.forEach((column) => {
				const columnData = [];

				Array.from(column.children).forEach((child) => {
					columnData.push(getChildData(child));
				});

				rowData.columns.push(columnData);
			});

			data.rows.push(rowData);
		}
	});

	return data;
}

document.addEventListener('DOMContentLoaded', () => {
	const button = document.querySelector('.save-as-pdf');

	if (button) {
		/**
		 * PDF-tallennusnapin eventlistener
		 */
		button.addEventListener('click', function () {
			// Haetaan kortin tyyppi datasetista (data-type="tulkintakortti")
			const { type } = this.dataset;

			// Haetaan kortin sisältö
			const data = generatePDFdata(document.querySelector(`.${type} > .grid`));

			// Haetaan PDF stringinä rajapinnasta
			fetch(`${REST.url}/pdf`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json; charset=utf-8',
					'X-WP-Nonce': REST.nonce,
				},
				body: JSON.stringify({
					title: 'test',
					data,
					article_url: 'testest',
				}),
			})
				.then((res) => res.json())
				.then((response) => {
					const byteCharacters = window.atob(response);
					const byteNumbers = new Array(byteCharacters.length);

					for (let i = 0; i < byteCharacters.length; i += 1) {
						byteNumbers[i] = byteCharacters.charCodeAt(i);
					}

					const byteArray = new Uint8Array(byteNumbers);

					const blob = new Blob([byteArray], { type: 'application/pdf' });

					const url = URL.createObjectURL(blob);

					window.open(url, '_blank');
				})
				.catch((error) => {
					console.log(error);
				});
		});
	}
});
