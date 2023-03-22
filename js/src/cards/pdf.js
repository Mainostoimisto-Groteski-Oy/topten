/* global REST */

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
			const element = document.querySelector(`.${type}`);

			// Haetaan PDF stringinä rajapinnasta
			fetch(`${REST.url}/pdf`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json; charset=utf-8',
					'X-WP-Nonce': REST.nonce,
				},
				body: JSON.stringify({
					title: 'test',
					html: element.innerHTML,
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
				// .then((res) => res.blob())
				// .then((blob) => URL.createObjectURL(blob))
				// .then((url) => {
				// 	window.open(url, '_blank');
				// })
				.catch((error) => {
					console.log(error);
				});
		});
	}
});
