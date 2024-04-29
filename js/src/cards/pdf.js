/* global Ajax */

import { __ } from '@wordpress/i18n';

jQuery(document).ready(($) => {
	// const cardTop = 0;
	// const cardLeft = 0;

	// function getChild(node) {
	// 	const tag = node.nodeName.toLowerCase();

	// 	let x = 0;
	// 	let y = 0;
	// 	let height = node.offsetHeight;
	// 	let width = node.offsetWidth;

	// 	if (node.getBoundingClientRect) {
	// 		x = node.getBoundingClientRect().left - cardLeft;
	// 		y = node.getBoundingClientRect().top - cardTop;
	// 	} else {
	// 		const range = document.createRange();
	// 		range.selectNodeContents(node);
	// 		const rects = range.getClientRects();

	// 		if (rects.length > 0) {
	// 			x = rects[0].left - cardLeft;
	// 			y = rects[0].top - cardTop;
	// 			width = rects[0].width;
	// 			height = rects[0].height;
	// 		}
	// 	}

	// 	const nodeData = {
	// 		tag,
	// 		value: node.textContent,
	// 		children: [],
	// 		attributes: {
	// 			class: node.className,
	// 			x,
	// 			y,
	// 			height,
	// 			width,
	// 		},
	// 	};

	// 	if (tag === 'input' || tag === 'textarea') {
	// 		nodeData.tag = 'input';

	// 		nodeData.attributes = {
	// 			type: node.type,
	// 			value: node.value,
	// 			checked: node.checked,
	// 			x,
	// 			y,
	// 			height,
	// 			width,
	// 		};
	// 	} else if (tag === 'picture') {
	// 		const image = node.querySelector('img');

	// 		x = image.getBoundingClientRect().left - cardLeft;
	// 		y = image.getBoundingClientRect().top - cardTop;

	// 		nodeData.tag = 'img';

	// 		nodeData.attributes = {
	// 			src: image.src,
	// 			alt: image.alt,
	// 			class: image.className,
	// 			width: image.width,
	// 			height: image.height,
	// 			x,
	// 			y,
	// 		};
	// 	} else if (tag === 'a') {
	// 		nodeData.value = node.href;

	// 		node.childNodes.forEach((child) => {
	// 			nodeData.children.push(getChild(child));
	// 		});
	// 	} else if (tag === 'th' || tag === 'td') {
	// 		nodeData.value = null;

	// 		node.childNodes.forEach((child) => {
	// 			nodeData.children.push(getChild(child));
	// 		});
	// 	} else if (node.childNodes.length > 0) {
	// 		nodeData.value = null;

	// 		node.childNodes.forEach((child) => {
	// 			nodeData.children.push(getChild(child));
	// 		});
	// 	} else {
	// 		nodeData.value = node.textContent;
	// 	}

	// 	return nodeData;
	// }

	// /**
	//  * Get child data
	//  *
	//  * @param {Object} child
	//  */
	// function getChildData(child) {
	// 	const data = getChild(child);

	// 	return data;
	// }

	// /**
	//  * Generate PDF data from card HTML content
	//  *
	//  * @param {Object} parent
	//  */
	// function generatePDFdata(parent) {
	// 	const data = {
	// 		count: 0,
	// 		rows: [],
	// 	};

	// 	const rows = parent.children('.row-block');

	// 	rows.each((rowIndex, row) => {
	// 		data.count += 1;

	// 		const rowData = {
	// 			count: 0,
	// 			columns: [],
	// 			attributes: {
	// 				class: row.className,
	// 				x: row.offsetLeft,
	// 				y: row.offsetTop,
	// 			},
	// 		};

	// 		$(row)
	// 			.find('.column-item')
	// 			.each((columnIndex, column) => {
	// 				rowData.count += 1;

	// 				const width = $(column).width();
	// 				const height = $(column).height();

	// 				const columnData = {
	// 					data: [],
	// 					attributes: {
	// 						class: column.className,
	// 						height,
	// 						width,
	// 						x: column.offsetLeft,
	// 						y: column.offsetTop,
	// 					},
	// 				};

	// 				$(column)
	// 					.children()
	// 					.each((childIndex, child) => {
	// 						columnData.data.push(getChildData(child));
	// 					});

	// 				rowData.columns.push(columnData);
	// 			});

	// 		data.rows.push(rowData);
	// 	});

	// 	return data;
	// }

	// function wrapTextNode(textNode) {
	// 	// const words = textNode.textContent.split(/[\s\-]/);
	// 	const words = textNode.textContent.split(/(?<=\s)|(?<=\-)/);

	// 	words.forEach((word) => {
	// 		const check = word.replace(/(\r\n|\n|\r|\t)/gm, '');

	// 		if (!check) {
	// 			return;
	// 		}

	// 		const span = document.createElement('span');

	// 		span.className = 'word';

	// 		const newTextNode = document.createTextNode(word);

	// 		span.appendChild(newTextNode);

	// 		textNode.parentNode.insertBefore(span, textNode);
	// 	});

	// 	textNode.parentNode.removeChild(textNode);
	// }

	function limitInputCharacters() {
		$(document).on('input', '.card-content input[type="text"]', function () {
			const value = $(this).val();

			const tempElement = $('<span>')
				.css('font-family', $(this).css('font-family'))
				.css('font-size', $(this).css('font-size'))
				.css('font-weight', $(this).css('font-weight'))
				.css('font-style', $(this).css('font-style'))
				.css('letter-spacing', $(this).css('letter-spacing'))
				.css('text-transform', $(this).css('text-transform'))
				.css('white-space', 'nowrap')
				.css('position', 'absolute')
				.css('top', '-9999px')
				.css('left', '-9999px')
				.css('width', 'auto')
				.css('height', 'auto')
				.text(value)
				.appendTo('body');

			const textWidth = tempElement.width();

			tempElement.remove();

			if (textWidth > $(this).width()) {
				$(this).val(value.slice(0, -1));
			}
		});

		$(document).on('paste', '.card-content input[type="text"]', function (event) {
			event.preventDefault();

			const value = $(this).val();

			// Get clipboard data
			const clipboardData = event.originalEvent.clipboardData.getData('text/plain');

			const tempElement = $('<span>')
				.css('font-family', $(this).css('font-family'))
				.css('font-size', $(this).css('font-size'))
				.css('font-weight', $(this).css('font-weight'))
				.css('font-style', $(this).css('font-style'))
				.css('letter-spacing', $(this).css('letter-spacing'))
				.css('text-transform', $(this).css('text-transform'))
				.css('white-space', 'nowrap')
				.css('position', 'absolute')
				.css('top', '-9999px')
				.css('left', '-9999px')
				.css('width', 'auto')
				.css('height', 'auto')
				.text(value)
				.appendTo('body');

			const chars = clipboardData.split('');

			let string = value;

			chars.forEach((char) => {
				string += char;

				tempElement.text(string);
				tempElement.width();

				if (tempElement.width() > $(this).width()) {
					string = string.slice(0, -1);
				}
			});

			$(this).val(string);
		});

		// Todo: Fix textarea
		// $(document).on('input', '.card-content textarea', function () {
		// 	const value = $(this).val();
		// 	const rows = $(this).attr('rows');

		// 	// Split by line breaks
		// 	const lines = value.split(/\r|\r\n|\n/);

		// 	let row = 0;

		// 	lines.forEach((line) => {
		// 		row++;

		// 		const tempElement = $('<span>')
		// 			.css('font-family', $(this).css('font-family'))
		// 			.css('font-size', $(this).css('font-size'))
		// 			.css('font-weight', $(this).css('font-weight'))
		// 			.css('font-style', $(this).css('font-style'))
		// 			.css('letter-spacing', $(this).css('letter-spacing'))
		// 			.css('text-transform', $(this).css('text-transform'))
		// 			.css('white-space', 'nowrap')
		// 			.css('position', 'absolute')
		// 			.css('top', '-9999px')
		// 			.css('left', '-9999px')
		// 			.css('width', 'auto')
		// 			.css('height', 'auto')
		// 			.text(line)
		// 			.appendTo('body');

		// 		const textWidth = tempElement.width();

		// 		tempElement.remove();

		// 		if (textWidth >= $(this).width()) {
		// 			row++;
		// 		}
		// 	});

		// 	if (row > rows) {
		// 		$(this).val(value.slice(0, -1));
		// 	}
		// });

		// Todo: Fix paste on textarea
		// $(document).on('paste', '.card-content textarea', function (event) {
		// 	event.preventDefault();

		// 	const value = $(this).val();
		// 	const rows = $(this).attr('rows');

		// 	// Split by line breaks
		// 	const lines = value.split(/\r|\r\n|\n/);

		// 	// Get clipboard data
		// 	const clipboardData = event.originalEvent.clipboardData.getData('text/plain');

		// 	let row = 0;

		// 	console.log(lines);

		// 	lines.forEach((line) => {
		// 		if (line === '') {
		// 			return;
		// 		}

		// 		row++;

		// 		const tempElement = $('<span>')
		// 			.css('font-family', $(this).css('font-family'))
		// 			.css('font-size', $(this).css('font-size'))
		// 			.css('font-weight', $(this).css('font-weight'))
		// 			.css('font-style', $(this).css('font-style'))
		// 			.css('letter-spacing', $(this).css('letter-spacing'))
		// 			.css('text-transform', $(this).css('text-transform'))
		// 			.css('white-space', 'nowrap')
		// 			.css('position', 'absolute')
		// 			.css('top', '-9999px')
		// 			.css('left', '-9999px')
		// 			.css('width', 'auto')
		// 			.css('height', 'auto')
		// 			.text(line)
		// 			.appendTo('body');

		// 		const textWidth = tempElement.width();

		// 		tempElement.remove();

		// 		if (textWidth >= $(this).width()) {
		// 			row++;
		// 		}
		// 	});

		// 	console.log('rows pre ' + row);

		// 	const clipboardLines = clipboardData.split(/\r|\r\n|\n/);

		// 	let lastIsLineBreak = false;
		// 	const lastLine = lines[lines.length - 1];

		// 	// Check if last character is line break
		// 	if (value.slice(-1) === '\n') {
		// 		lastIsLineBreak = true;

		// 		row++;
		// 	}

		// 	let elementString = value;

		// 	clipboardLines.forEach((line) => {
		// 		let string = '';

		// 		if (!lastIsLineBreak) {
		// 			string = lastLine + line;
		// 		} else {
		// 			string = line;

		// 			row++;
		// 		}

		// 		console.log(row);

		// 		if (row > rows) {
		// 			return;
		// 		}

		// 		const chars = line.split('');

		// 		chars.forEach((char) => {
		// 			string += char;

		// 			const tempElement = $('<span>')
		// 				.css('font-family', $(this).css('font-family'))
		// 				.css('font-size', $(this).css('font-size'))
		// 				.css('font-weight', $(this).css('font-weight'))
		// 				.css('font-style', $(this).css('font-style'))
		// 				.css('letter-spacing', $(this).css('letter-spacing'))
		// 				.css('text-transform', $(this).css('text-transform'))
		// 				.css('white-space', 'nowrap')
		// 				.css('position', 'absolute')
		// 				.css('top', '-9999px')
		// 				.css('left', '-9999px')
		// 				.css('width', 'auto')
		// 				.css('height', 'auto')
		// 				.text(line)
		// 				.appendTo('body');

		// 			tempElement.text(string);

		// 			console.log($(this).width(), tempElement.width());

		// 			const x = Math.ceil(tempElement.width() / $(this).width());

		// 			console.log(x);

		// 			// if (tempElement.width() > $(this).width()) {
		// 			// 	row++;
		// 			// }

		// 			tempElement.remove();

		// 			if (row > rows) {
		// 				string = string.slice(0, -1);
		// 			}
		// 		});

		// 		// if (row <= rows) {
		// 		// 	string += '\n';
		// 		// }

		// 		// console.log(string);

		// 		elementString += string;
		// 	});

		// 	$(this).val(elementString);
		// });

		// const originalContainer = $('.card-content-wrapper');

		// if (originalContainer.length > 0) {
		// 	const copyContainer = originalContainer.clone();

		// 	copyContainer.addClass('print');

		// 	// Append copy to same parent
		// 	copyContainer.css('opacity', 0);
		// 	originalContainer.parent().append(copyContainer);

		// 	const inputs = copyContainer.find('.card-content input[type="text"], .card-content textarea');

		// 	if (inputs.length > 0) {
		// 		inputs.each((index, input) => {
		// 			const width = $(input).outerWidth();
		// 			const height = $(input).outerHeight();
		// 			const innerHeight = $(input).innerHeight();
		// 			const padding = $(input).css('padding');

		// 			// Find input in original container
		// 			const originalInput = $(originalContainer).find(`#${input.id}`);

		// 			$(originalInput).attr('data-width', width);
		// 			$(originalInput).attr('data-height', height);
		// 			$(originalInput).attr('data-inner-height', innerHeight);

		// 			// Limit input characters
		// 			$(originalInput).on('input', function () {
		// 				const isTextarea = $(this).is('textarea');
		// 				const value = $(this).val();
		// 				const inputWidth = Number($(originalInput).data('width'));
		// 				const inputInnerHeight = Number($(originalInput).data('inner-height'));

		// 				if (isTextarea) {
		// 					const text = $('<textarea>')
		// 						.css('font-family', $(input).css('font-family'))
		// 						.css('font-size', $(input).css('font-size'))
		// 						.css('font-weight', $(input).css('font-weight'))
		// 						.css('font-style', $(input).css('font-style'))
		// 						.css('letter-spacing', $(input).css('letter-spacing'))
		// 						.css('text-transform', $(input).css('text-transform'))
		// 						.css('position', 'absolute')
		// 						.css('top', '-9999px')
		// 						.css('left', '-9999px')
		// 						.css('width', width)
		// 						.css('height', height)
		// 						.css('padding', padding)
		// 						.attr('rows', $(input).attr('rows'))
		// 						.text(value)
		// 						.appendTo('body');

		// 					// Get scroll height
		// 					const scrollHeight = text[0].scrollHeight;

		// 					if (scrollHeight > inputInnerHeight) {
		// 						$(originalInput).val(value.slice(0, -1));
		// 					}
		// 				} else {
		// 					// Calculate width of input value
		// 					const text = $('<span>')
		// 						.css('font-family', $(input).css('font-family'))
		// 						.css('font-size', $(input).css('font-size'))
		// 						.css('font-weight', $(input).css('font-weight'))
		// 						.css('font-style', $(input).css('font-style'))
		// 						.css('letter-spacing', $(input).css('letter-spacing'))
		// 						.css('text-transform', $(input).css('text-transform'))
		// 						.css('white-space', 'nowrap')
		// 						.css('position', 'absolute')
		// 						.css('top', '-9999px')
		// 						.css('left', '-9999px')
		// 						.css('width', 'auto')
		// 						.css('height', 'auto')
		// 						.text(value)
		// 						.appendTo('body');

		// 					const textWidth = text.width();

		// 					text.remove();

		// 					if (textWidth > inputWidth) {
		// 						$(originalInput).val(value.slice(0, -1));
		// 					}
		// 				}
		// 			});
		// 		});
		// 	}

		// 	copyContainer.remove();
		// }
	}

	if ($('body').hasClass('single-lomakekortti')) {
		limitInputCharacters();
	}

	// /**
	//  * Print page as PDF
	//  */
	// $('.save-as-pdf:not(:disabled)').on('click', function () {
	// 	const originalContainer = $('.card-content-wrapper');
	// 	const copyContainer = originalContainer.clone();

	// 	copyContainer.addClass('print');

	// 	// Append copy to same parent
	// 	originalContainer.parent().prepend(copyContainer);

	// 	const content = copyContainer.find('.card-content');

	// 	// Hide original
	// 	originalContainer.hide();

	// 	const nodes = $(content)
	// 		.find('*')
	// 		.contents()
	// 		.filter(function () {
	// 			return this.nodeType === 3;
	// 		});

	// 	nodes.each((index, node) => {
	// 		wrapTextNode(node);
	// 	});

	// 	cardTop = content[0].getBoundingClientRect().top;
	// 	cardLeft = content[0].getBoundingClientRect().left;

	// 	// Get card content
	// 	let data = generatePDFdata(content);

	// 	data = JSON.stringify(data);

	// 	const title = $('.row-block .title-wrapper h1').text();

	// 	$.ajax({
	// 		url: Ajax.url,
	// 		type: 'POST',
	// 		data: {
	// 			action: 'topten_generate_pdf',
	// 			nonce: Ajax.nonce,
	// 			title,
	// 			data,
	// 			article_url: window.location.href,
	// 		},
	// 	})
	// 		.done((response) => {
	// 			copyContainer.remove();
	// 			originalContainer.show();

	// 			const byteCharacters = window.atob(response.data);
	// 			const byteNumbers = new Array(byteCharacters.length);

	// 			for (let i = 0; i < byteCharacters.length; i += 1) {
	// 				byteNumbers[i] = byteCharacters.charCodeAt(i);
	// 			}

	// 			const byteArray = new Uint8Array(byteNumbers);
	// 			const blob = new Blob([byteArray], { type: 'application/pdf' });
	// 			const url = URL.createObjectURL(blob);

	// 			const a = $('<a style="display:none"></a>');

	// 			a.attr('href', url);
	// 			a.attr('download', title + '.pdf');

	// 			$('body').append(a);

	// 			a[0].click();

	// 			window.URL.revokeObjectURL(url);

	// 			a.remove();

	// 			window.open(url, '_blank');
	// 		})
	// 		.fail((jqXHR, textStatus, errorThrown) => {
	// 			// eslint-disable-next-line no-console
	// 			console.error(jqXHR, textStatus, errorThrown);
	// 		});
	// });

	// This needs to actually do something :D
	$(document).on('click', '.save-as-pdf:not(:disabled)', function () {
		window.print();
	});

	// Duplicate text areas for printing so content is not cut off when creating pdf / printing out card
	window.onbeforeprint = function () {
		const contents = [];
		$('.card-content textarea').each(function () {
			contents.push($(this).val());
			const content = $(this).val();
			const id = $(this).attr('id');
			const minHeight = $(this).innerHeight();
			const divs = $(
				'<div class="textarea-duplicate" data-id="' +
					id +
					'" style="min-height:' +
					minHeight +
					'px">' +
					content +
					'</div>',
			);
			if ($('.textarea-duplicate[data-id="' + id + '"]').length === 0) {
				$(this).after(divs);
				$(this).hide();
			}
		});
	};

	// After print window is closed, get rid of the duplicates and show the original textareas
	window.onafterprint = function () {
		$('.card-content textarea').each(function () {
			$(this).show();
		});
		$('.card-content .textarea-duplicate').each(function () {
			$(this).remove();
		});
	};

	$('.close-modal').on('click', function () {
		closeModal();
	});

	$(document).keydown(function (event) {
		if (event.keyCode === 27) {
			closeModal();
		}
	});

	function trapFocus(event, firstFocusableElement, lastFocusableElement) {
		if (event.keyCode === 9) {
			// tab
			if (event.shiftKey) {
				// shift + tab

				if (firstFocusableElement === firstFocusableElement.ownerDocument.activeElement) {
					event.preventDefault();

					lastFocusableElement.focus();
				}
			} else if (lastFocusableElement === lastFocusableElement.ownerDocument.activeElement) {
				event.preventDefault();

				firstFocusableElement.focus();
			}
		}
	}

	function openModal() {
		$('.save-card-modal').fadeIn(300);
		$('.save-card-modal .modal-content').focus();
		$('.save-card').attr('aria-expanded', 'true');
		$('body').addClass('no-scroll');

		const focusableElements = $('.save-card-modal').find(
			'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
		);

		const firstFocusableElement = focusableElements[0];
		const lastFocusableElement = focusableElements[focusableElements.length - 1];

		$('.save-card-modal').keydown(function (event) {
			trapFocus(event, firstFocusableElement, lastFocusableElement);
		});
	}

	function closeModal() {
		$('.save-card-modal').fadeOut(300, () => {
			$('#card-code-textarea').html('');
			$('.save-card-modal .loading-spinner').hide();
			$('.save-card-modal .copy-card-code-wrapper').attr('aria-busy', 'false');
			$('body').removeClass('no-scroll');
			$('.save-card-modal .message-wrapper p.message').html('');
		});
		$('.save-card').focus();
	}

	/**
	 * Save card to database
	 */
	$('.save-card').on('click', function () {
		openModal();

		$('.save-card-modal .loading-spinner').show();
		$('.save-card-modal .copy-card-code-wrapper').attr('aria-busy', 'true');

		const data = {};

		$('.lomakekortti .card-content input, .lomakekortti .card-content textarea').each(function () {
			if ($(this).hasClass('prevent-save')) {
				return;
			}

			const id = $(this).attr('id');

			let value;

			if ($(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radio') {
				value = $(this).is(':checked');
			} else if ($(this).is('textarea')) {
				value = $(this).val().replace(/\n/g, '<br>');
			} else {
				value = $(this).val();
			}

			data[id] = value;
		});

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
				$('.save-card-modal .loading-spinner').hide();
				$('.save-card-modal .copy-card-code-wrapper').attr('aria-busy', 'false');

				if (response.success) {
					$('.save-card-modal #card-code-textarea').html(response.data);
				}
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				// eslint-disable-next-line no-console
				console.error(textStatus, errorThrown);
			});
	});

	$('button.copy-card-code').on('click', function () {
		const value = $('#card-code-textarea').val();

		navigator.clipboard.writeText(value);

		$('.copy-card-code-wrapper .message-wrapper p.message').html(__('Koodi kopioitu leikepöydälle!', 'topten'));
		$('.copy-card-code-wrapper .message-wrapper').fadeIn(200);

		setTimeout(() => {
			$('.copy-card-code-wrapper .message-wrapper').fadeOut(200, () => {
				$('.copy-card-code-wrapper .message-wrapper p.message').html('');
			});
		}, 2500);
	});

	$('#send-code-to-email').on('click', function () {
		const code = $('#card-code-textarea').val();
		const email = $('#send-card-code-email').val();
		const cardId = $('.site-main').data('post-id');

		$.ajax({
			url: Ajax.url,
			type: 'POST',
			data: {
				action: 'topten_send_code',
				nonce: Ajax.nonce,
				code,
				email,
				cardId,
			},
		})
			.success((response) => {
				if (response.success) {
					closeModal();
					$('.lomake-card-success').fadeIn(200);
					$('.lomake-card-success .message-wrapper p.message').html(response.data);
					setTimeout(() => {
						$('.lomake-card-success').fadeOut(200, () => {
							$('.lomake-card-success .message-wrapper p.message').html('');
						});
					}, 2500);
				} else {
					$('.save-card-modal .message-wrapper').addClass('error');
					$('.save-card-modal .message-wrapper').fadeIn(200);
					$('.save-card-modal .message-wrapper p.message').html(response.data);
					setTimeout(() => {
						$('.save-card-modal .message-wrapper').fadeOut(200, () => {
							$('.save-card-modal .message-wrapper p.message').html('');
						});
					}, 2500);
				}
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				// eslint-disable-next-line no-console
				console.error(textStatus, errorThrown);
			});
	});

	/**
	 * Clear card fields
	 */
	$('.clear-input').on('click', function () {
		// eslint-disable-next-line no-alert
		const confirm = window.confirm(__('Haluatko varmasti tyhjentää lomakkeen?', 'topten'));

		if (confirm === true) {
			$('.lomakekortti .card-content input, .lomakekortti .card-content textarea').each(function () {
				$(this).val('');

				if ($(this).attr('type') === 'checkbox') {
					$(this).prop('checked', false);
				} else {
					$(this).val('');
				}
			});
		}
	});

	/**
	 * Load card from database
	 */
	$(document).on('click', '.load-card', function () {
		$('.card-code-label .material-symbols').hide();
		$('.card-code-label .errormsg').hide();
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
						// Remove all spaces from the index
						index.replace(/\s/g, '');

						if ($(`#${index}`).attr('type') === 'checkbox' || $(`#${index}`).attr('type') === 'radio') {
							if (response.data[index] === 'true') {
								$(`#${index}`).prop('checked', true);
							} else {
								$(`#${index}`).prop('checked', false);
							}
						} else {
							$(`#${index}`).val(response.data[index]);
						}
					}
					$('#card-code').val('');
					$('.card-code-label .success').show();
				} else {
					$('.card-code-label .errormsg').html(response.data);
					$('.card-code-label .error').show();
					$('.card-code-label .errormsg').show();
				}
			})
			.fail((jqXHR, textStatus, errorThrown) => {
				// eslint-disable-next-line no-console
				console.error(textStatus, errorThrown);
			});
	});
});
