import html2pdf from 'html2pdf.js';

function saveAsPDF(selector) {
	const element = document.querySelector(selector);

	const args = {
		margin: 1,
		filename: 'tulkintakortti.pdf',
		image: { type: 'jpeg', quality: 0.98 },
		html2canvas: { scale: 2, letterRendering: true },
		jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
	};

	html2pdf(element, args);
}

document.addEventListener('DOMContentLoaded', () => {
	const button = document.querySelector('.save-as-pdf');

	button.addEventListener('click', function () {
		const { type } = this.dataset;

		saveAsPDF(`.${type}`);
	});
});
