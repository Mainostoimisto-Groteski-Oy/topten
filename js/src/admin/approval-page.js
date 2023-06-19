jQuery(document).ready(($) => {
	const tableElements = $('.topten-datatable');

	if (tableElements.length > 0) {
		tableElements.each(function () {
			$(this).DataTable({
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fi.json',
				},
			});
		});
	}
});
