/* global Ajax */

import { __ } from '@wordpress/i18n';

jQuery(document).ready(($) => {
	const tableElements = $('.tt-datatable');
	const tables = [];

	if (tableElements.length > 0) {
		tableElements.each(function () {
			const table = $(this).DataTable({
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fi.json',
				},
			});

			tables.push(table);
		});
	}

	function findTable(that) {
		const tableElement = $(that).closest('.tt-datatable');

		return tables?.find((table) => table.table().node().id === tableElement.attr('id'));
	}

	function handleApproval(action, that) {
		const postId = $(that).data('id');
		const message = $(`textarea#tt-approval-message-${postId}`).val();

		console.log(message);

		$.ajax({
			url: Ajax.url,
			method: 'POST',
			data: {
				action,
				post_id: postId,
				message,
				nonce: Ajax.nonce,
			},
		})
			.done((response) => {
				console.log(response);

				$('.tt-message-row').empty();

				if (response.success) {
					const table = findTable(that);

					if (table) {
						table.row($(that).closest('tr')).remove().draw();
					}

					$('.tt-message-row').removeClass('hidden').removeClass('tt-error').addClass('tt-success');
					$('.tt-message-row').append(`<p>${response.data.message}</p>`);
				} else {
					$('.tt-message-row').removeClass('hidden').removeClass('tt-success').addClass('tt-error');
					$('.tt-message-row').append(`<p>${response.data.message}</p>`);

					if (response.data.error_code) {
						$('.tt-message-row').append(`<p>${response.data.error_code}</p>`);
					}
				}
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				console.log(textStatus, errorThrown);

				$('.tt-message-row').empty();
				$('.tt-message-row').removeClass('hidden').removeClass('tt-success').addClass('tt-error');
				$('.tt-message-row').append(
					`<p>${__('Jotain meni vikaan. Yritä kohta uudestaan.', 'topten')}</p>`
				);
				$('.tt-message-row').append(`<p>${errorThrown}</p>`);
			});
	}

	$(document).on('click', '.tt-approve', function () {
		const confirm = window.confirm(__('Haluatko varmasti hyväksyä kortin?', 'topten')); // eslint-disable-line no-alert

		if (!confirm) {
			return;
		}

		handleApproval('tt_approve_card', this);
	});

	$(document).on('click', '.tt-disapprove', function () {
		const confirm = window.confirm(__('Haluatko varmasti hylätä kortin?', 'topten')); // eslint-disable-line no-alert

		if (!confirm) {
			return;
		}

		handleApproval('tt_disapprove_card', this);
	});
});
