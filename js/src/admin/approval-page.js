/* global Ajax */

jQuery(document).ready(($) => {
	const tableElements = $('.tt-datatable');

	if (tableElements.length > 0) {
		tableElements.each(function () {
			$(this).DataTable({
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fi.json',
				},
			});
		});
	}

	$(document).on('click', '.tt-approve', function () {
		const postId = $(this).data('id');
		const message = $(`textarea#tt-message-${postId}`).val();

		$.ajax({
			url: Ajax.url,
			method: 'POST',
			data: {
				action: 'tt_approve_card',
				post_id: postId,
				message,
				nonce: Ajax.nonce,
			},
		})
			.done((response) => {
				console.log(response);
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				console.log(textStatus, errorThrown);
			});
	});
});
