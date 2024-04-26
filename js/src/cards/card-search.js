/* eslint-disable no-param-reassign */
/* eslint-disable no-underscore-dangle */
/* global Ajax */

// Applies ajax overlay when ajax is running
const ajaxOverlay = jQuery('#ajaxOverlay');
const ajaxSpinner = jQuery('#ajaxSpinner');
jQuery(document)
	.ajaxStart(() => {
		jQuery(ajaxOverlay).fadeIn(200);
		jQuery(ajaxSpinner).show();
		jQuery('#textSearch').addClass('disabled');
	})
	.ajaxStop(() => {
		jQuery(ajaxOverlay).fadeOut(200);
		jQuery(ajaxSpinner).hide();
		jQuery('#textSearch').removeClass('disabled');
		// Fix borders
		// Cannot be done with css since we query all the cards from database and just hide those not relevant with css, since it's faster than meta_queries in WP :Dddd
		if (jQuery('#tulkintakortit').length > 0) {
			const lastTulkinta = jQuery('#tulkintakortit').find('li.card:visible').last();
			jQuery('#tulkintakortit li.card').removeClass('last');
			jQuery(lastTulkinta).addClass('last');
		}
		if (jQuery('#ohjekortit').length > 0) {
			const lastOhje = jQuery('#ohjekortit').find('li.card:visible').last();
			jQuery('#ohjekortit li.card').removeClass('last');
			jQuery(lastOhje).addClass('last');
		}
		if (jQuery('#lomakekortit').length > 0) {
			const lastLomake = jQuery('#lomakekortit').find('li.card:visible').last();
			jQuery('#lomakekortit li.card').removeClass('last');
			jQuery(lastLomake).addClass('last');
		}
	});

jQuery(document).ready(($) => {
	// Fetch cards from database via query
	// cardStatusType is an acf field set in korttiluettelo template that determines which cards are shown (valid, expired or 2025 law)
	function cardSearch() {
		const cardStatusType = $('#primary').attr('data-template');

		let freeText = '';
		if (localStorage.getItem('freeText')) {
			freeText = localStorage.getItem('freeText');
		} else {
			freeText = $('#freeText').val();
		}

		let cardLaw = '';

		if (localStorage.getItem('cardLaw')) {
			cardLaw = localStorage.getItem('cardLaw');

			const splitLaw = cardLaw.split('|');

			$('#cardLaw').val(splitLaw[0]);
		} else {
			cardLaw = $('#cardLaw').val();
		}

		let cardCategory = '';

		if (localStorage.getItem('cardCategory')) {
			cardCategory = localStorage.getItem('cardCategory');
			const splitCategory = cardCategory.split('|');
			$('#cardCategory').val(splitCategory[0]);
		} else {
			cardCategory = $('#cardCategory').val();
		}
		let filterOrder = '';
		if (localStorage.getItem('filterOrder')) {
			filterOrder = localStorage.getItem('filterOrder');
		} else {
			filterOrder = $('#filterOrder').val();
		}
		let cardDateStart = '';
		if (localStorage.getItem('cardDateStart')) {
			cardDateStart = localStorage.getItem('cardDateStart');
		} else {
			cardDateStart = $('#dateStart').val();
		}
		let cardDateEnd = '';
		if (localStorage.getItem('cardDateEnd')) {
			cardDateEnd = localStorage.getItem('cardDateEnd');
		} else {
			cardDateEnd = $('#cardDateEnd').val();
		}

		// if these are not in localstorage they're not in use at all
		const cardkeywords = JSON.parse(localStorage.getItem('keywords'));
		// const cardmunicipalities = JSON.parse(localStorage.getItem('municipalities'));

		// get checked checkboxes values and push them to array
		let cardClasses = '';
		if (localStorage.getItem('cardclassfilter')) {
			cardClasses = JSON.parse(localStorage.getItem('cardclassfilter'));
		} else {
			cardClasses = [];
			$('input[name="cardclassfilter"]:checked').each(function () {
				cardClasses.push($(this).val());
			});
		}
		let cardTypes = '';
		if (localStorage.getItem('cardTypeFilter')) {
			cardTypes = JSON.parse(localStorage.getItem('cardTypeFilter'));
		} else {
			cardTypes = [];
			$('input[name="cardTypeFilter"]:checked').each(function () {
				cardTypes.push($(this).val());
			});
		}
		$.ajax({
			url: Ajax.url,
			method: 'POST',
			data: {
				action: 'topten_card_search',
				cardStatusType,
				freeText,
				cardkeywords,
				// cardmunicipalities,
				cardLaw,
				cardCategory,
				cardDateStart,
				cardDateEnd,
				filterOrder,
				cardTypes,
				cardClasses,
				nonce: Ajax.nonce,
			},
			success(data) {
				// PHP code handles the data so we just need to append it to the DOM
				$('#listCards').html(data);
				// Hide empty categories
				// Tulkintakortti post type has three levels of categories, the others two
				$('#tulkintakortit ul.children').each(function () {
					const tulkintaStatus = [];
					const grandparent = $(this).attr('data-parent');
					$(this)
						.children()
						.children('ul.grandchildren')
						.each(function () {
							const parent = $(this).attr('data-parent');
							if ($(this).children().length <= 0) {
								$(this).parent(`li.child[data-id=${parent}]`).hide();
								tulkintaStatus.push('false');
							} else {
								tulkintaStatus.push('true');
							}
						});
					if (tulkintaStatus.indexOf('true') === -1) {
						$(`#tulkintakortit li.parent[data-id=${grandparent}]`).hide();
					}
				});

				$('#ohjekortit ul.children').each(function () {
					if ($(this).children().length <= 0) {
						$(this).hide();
						$(this).parent('li.parent').hide();
					}
				});
				$('#lomakekortit ul.children').each(function () {
					if ($(this).children().length <= 0) {
						$(this).hide();
						$(this).parent('li.parent').hide();
					}
				});
			},
			error(errorThrown) {
				// eslint-disable-next-line no-console
				console.error(errorThrown);
			},
		});
		clearErrors();
	}

	function updateFilters(type) {
		// For keywords and municipalities, municipalities are not in use as of 09/05/2023 due to customer request
		// Get items from local storage
		const words = JSON.parse(localStorage.getItem(`${type}`));
		let typeName = '';
		// Match type to taxonomy name
		if (type === 'keywords') {
			typeName = 'asiasanat';
		} else if (type === 'municipalities') {
			typeName = 'kunta';
		}
		// If there are items in local storage, fetch them from database and append to DOM
		if (words) {
			$.ajax({
				url: Ajax.url,
				method: 'POST',
				data: {
					action: 'topten_fetch_terms',
					nonce: Ajax.nonce,
					keywords: words,
					type: typeName,
				},
				success(data) {
					const terms = data.data;
					if (terms.length > 0) {
						$(`#selected${type}`).html('');
						$(`#selected${type}`).parent('figure').addClass('active');
						$(terms).each(function () {
							$(`#selected${type}`).append(
								`<li class="keyword" data-id="${this.value}"><button class="removekeyword" aria-label="Poista ${this.label}" data-type="${type}" data-id="${this.value}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${this.label}</span></li>`,
							);
						});
					}
				},
				error(errorThrown) {
					// eslint-disable-next-line no-console
					console.error(errorThrown);
				},
			});
		}
	}

	function showFilters(type) {
		// For items with singular type localstorage
		// Get items from local storage
		if (type === 'cardLaw') {
			const cardLaw = localStorage.getItem('cardLaw');
			if (cardLaw) {
				// localstorage doesn't take array values so we need to split the string
				const splitLaw = cardLaw.split('|');
				$('#selectedLaw').html('');
				$(`#selectedLaw`).parent('figure').addClass('active');
				$('#selectedLaw').append(
					`<li class="keyword" data-id="${splitLaw[0]}"><button class="removekeyword" aria-label="Poista ${splitLaw[1]}" data-type="cardLaw" data-id="${splitLaw[0]}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${splitLaw[1]}</span></li>`,
				);
			}
		}
		if (type === 'cardCategory') {
			const cardCategory = localStorage.getItem('cardCategory');
			if (cardCategory) {
				const splitCategory = cardCategory.split('|');
				$('#selectedCategory').html('');
				$(`#selectedCategory`).parent('figure').addClass('active');
				$('#selectedCategory').append(
					`<span class="keyword" data-id="${splitCategory[0]}"><button class="removekeyword" aria-label="Poista ${splitCategory[1]}" data-type="cardCategory" data-id="${splitCategory[0]}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${splitCategory[1]}</span></span>`,
				);
			}
		}
		if (type === 'cardDateStart') {
			const cardDateStart = localStorage.getItem('cardDateStart');
			if (cardDateStart) {
				const date = new Date(cardDateStart);
				const day = date.getDate();
				const month = date.getMonth() + 1;
				const year = date.getFullYear();
				const dateString = `${day}.${month}.${year}`;
				$('#selectedDateStart').html('');
				$(`#selectedDateStart`).parent('ul').parent('figure').addClass('active');
				$('#selectedDateStart').append(
					`<span class="keyword" data-id="dateStart"><button class="removekeyword" aria-label="Poista ${dateString}" data-type="cardDateStart" data-time="${cardDateStart}" data-id="dateStart"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${dateString}</span></span>`,
				);
			}
		}
		if (type === 'cardDateEnd') {
			const cardDateEnd = localStorage.getItem('cardDateEnd');
			if (cardDateEnd) {
				$('#selectedDateEnd').html('');
				$(`#selectedDateEnd`).parent('ul').parent('figure').addClass('active');
				const date = new Date(cardDateEnd);
				const day = date.getDate();
				const month = date.getMonth() + 1;
				const year = date.getFullYear();
				const dateString = `${day}.${month}.${year}`;
				$('#selectedDateEnd').append(
					`<span class="keyword" data-id="dateEnd"><button class="removekeyword" aria-label="Poista ${dateString}" data-type="cardDateEnd" data-time="${cardDateEnd}" data-id="dateEnd"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${dateString}</span></span>`,
				);
			}
		}
		if (type === 'freeText') {
			const freeText = localStorage.getItem('freeText');
			if (freeText) {
				$('#selectedFreeText').html('');
				$(`#selectedFreeText`).parent('figure').addClass('active');
				$('#selectedFreeText').append(
					`<span class="keyword" data-id="freeText"><button class="removekeyword" aria-label="Poista ${freeText}" data-type="freeText" data-id="freeText"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${freeText}</span></span>`,
				);
			}
		}
	}

	// Just a collection of functions to run on page load
	function updateSingularFilters() {
		showFilters('cardLaw');
		showFilters('cardCategory');
		showFilters('cardDateStart');
		showFilters('cardDateEnd');
		showFilters('freeText');
	}

	// Handles keywords and card classes
	function applyFilters(type) {
		// Get chosen items from hidden input field
		let keyword = '';
		if (type === 'keywords') {
			keyword = $(`#card${type}Value`).val();
			// Check if item is already in local storage
			if (localStorage.getItem(type)) {
				const storage = JSON.parse(localStorage.getItem(type));
				if (storage.indexOf(keyword) === -1) {
					storage.push(keyword);
					localStorage.setItem(type, JSON.stringify(storage));
				}
			} else {
				const storage = [];
				storage.push(keyword);
				localStorage.setItem(type, JSON.stringify(storage));
			}
			// Update filters to DOM
			if ($(`#selected${type}`).children().length > 0) {
				const existingChildren = [];
				// Check if item is already in DOM
				$(`#selected${type}`)
					.children()
					.each(function () {
						existingChildren.push($(this).attr('data-id'));
					});
				// If not, append it
				if (existingChildren.indexOf(keyword) === -1) {
					$(`#selected${type}`).parent('figure').addClass('active');
					$(`#selected${type}`).append(
						`<li class="keyword" data-id="${keyword}"><button class="removekeyword" aria-label="Poista" data-type="${type}" data-id="${keyword}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${$(
							`#card${type}`,
						).val()}</span></li>`,
					);
				}
			} else {
				// If there are no items in DOM, append it
				$(`#selected${type}`).parent('figure').addClass('active');
				$(`#selected${type}`).append(
					`<li class="keyword" data-id="${keyword}"><button class="removekeyword" aria-label="Poista" data-type="${type}" data-id="${keyword}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${$(
						`#card${type}`,
					).val()}</span></li>`,
				);
			}
			$('#cardkeywords').val('');
		} else if (type === 'cardclassfilter') {
			// there's always at least of one these checked to so we don't need a check for 0
			const cardClasses = [];
			// get the checked card classes
			$(`input[name="cardclassfilter"]:checked`).each(function () {
				cardClasses.push(`${$(this).val()}|${$(this).attr('data-name')}`);
			});
			localStorage.setItem(type, JSON.stringify(cardClasses));
			// Update filters to DOM
			if ($('#selectedCardClasses').length > 0) {
				const storage = JSON.parse(localStorage.getItem(type));

				$('#selectedCardClasses').html('');
				$('#selectedCardClasses').parent('figure').addClass('active');

				$(storage).each(function () {
					// split the string into an array
					const split = this.toString().split('|');
					$('#selectedCardClasses').append(
						`<li class="keyword" data-id="${split[0]}"><button class="removekeyword" aria-label="Poista" data-type="cardclassfilter" data-id="${split[0]}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${split[1]}</span></li>`,
					);
				});
			}
		} else if (type === 'cardTypeFilter') {
			// there's always at least of one these checked to so we don't need a check for 0
			const cardTypes = [];
			// get the checked card classes
			$(`input[name="cardTypeFilter"]:checked`).each(function () {
				cardTypes.push(`${$(this).val()}|${$(this).attr('data-name')}`);
			});
			localStorage.setItem(type, JSON.stringify(cardTypes));
			// Update filters to DOM
			if ($('#selectedCardTypes').length > 0) {
				const storage = JSON.parse(localStorage.getItem(type));

				$('#selectedCardTypes').html('');
				$('#selectedCardTypes').parent('figure').addClass('active');

				$(storage).each(function () {
					// split the string into an array
					const split = this.toString().split('|');
					$('#selectedCardTypes').append(
						`<li class="keyword" data-id="${split[0]}"><button class="removekeyword" aria-label="Poista" data-type="cardTypeFilter" data-id="${split[0]}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${split[1]}</span></li>`,
					);
				});
			}
		}
	}

	function removeFilters(id, type) {
		if (type === 'keywords') {
			const storage = JSON.parse(localStorage.getItem(type));
			$(storage).each(function () {
				const storedItem = this.toString();
				if (storedItem === id) {
					// Remove this id from the array
					storage.splice(storage.indexOf(id), 1);
					// Push back to localstorage
					if (storage.length <= 0) {
						localStorage.removeItem(type);
						// If it is the last item, hide the whole figure
						$(`li.keyword[data-id=${id}]`).parents('figure').removeClass('active');
					} else {
						localStorage.setItem(type, JSON.stringify(storage));
					}
				}
			});
			// Remove keyword div with this data-id attribute
			$(`li.keyword[data-id="${id}"]`).remove();
		} else if (type === 'cardLaw') {
			const storage = localStorage.getItem(type);
			const splitLaw = storage.split('|');
			if (splitLaw[0] === id) {
				localStorage.removeItem(type);
				$(`li.keyword[data-id=${id}]`).parents('figure').removeClass('active');
				$(`li.keyword[data-id=${id}]`).remove();
				$('#cardLaw').val('');
			}
		} else if (type === 'cardCategory') {
			const storage = localStorage.getItem(type);
			const splitCategory = storage.split('|');
			if (splitCategory[0] === id) {
				localStorage.removeItem(type);
				$(`li.keyword[data-id=${id}]`).parents('figure').removeClass('active');
				$(`li.keyword[data-id=${id}]`).remove();
				$('#cardCategory').val('');
			}
		} else if (type === 'cardDateStart' || type === 'cardDateEnd') {
			if (type === 'cardDateStart') {
				$('#cardDateStart').val('');
				localStorage.removeItem('cardDateStart');
				// if cardDateEnd is empty, hide the whole figure
				if (localStorage.getItem('cardDateEnd') === null) {
					$(`li.keyword[data-id=${id}]`).parents('figure').removeClass('active');
				}
				$(`li.keyword[data-id=${id}]`).remove();
			} else if (type === 'cardDateEnd') {
				// do the same thing in reverse
				$('#cardDateEnd').val('');
				localStorage.removeItem('cardDateEnd');
				if (localStorage.getItem('cardDateStart') === null) {
					$(`li.keyword[data-id=${id}]`).parents('figure').removeClass('active');
				}
				$(`li.keyword[data-id=${id}]`).remove();
			}
		} else if (type === 'freeText') {
			$('#freeText').val('');
			localStorage.removeItem('freeText');
			$(`li.keyword[data-id=${id}]`).parents('figure').removeClass('active');
			$(`li.keyword[data-id=${id}]`).remove();
		} else if (type === 'cardclassfilter') {
			const storage = JSON.parse(localStorage.getItem(type));
			// We want at least one to be selected and shown at all times
			if (storage.length > 1) {
				$(`ul#selectedCardClasses li.keyword`).removeClass('disabled');
				$(storage).each(function () {
					const storedItem = this.toString().split('|')[0];

					if (storedItem === id) {
						// Remove this id from the array
						storage.splice(storage.indexOf(id), 1);
						// Push back to localstorage

						localStorage.setItem(type, JSON.stringify(storage));
						$('input[name="cardclassfilter"]').each(function () {
							if ($(this).val() === id) {
								$(this).prop('checked', false);
							}
						});
					}
				});
				// Remove keyword element with this data-id attribute
				$(`li.keyword[data-id="${id}"]`).remove();
				if (storage.length > 1) {
					// TODO: check why this isn't working 100%
					$(`ul#selectedCardClasses li.keyword[data-id="${id}"]`).addClass('disabled');
				}
			} else {
				$(`ul#selectedCardClasses li.keyword[data-id="${id}"]`).addClass('disabled');
			}
		} else if (type === 'cardTypeFilter') {
			const storage = JSON.parse(localStorage.getItem(type));
			// We want at least one to be selected and shown at all times
			if (storage.length > 1) {
				$(`ul#selectedCardTypes li.keyword`).removeClass('disabled');
				$(storage).each(function () {
					const storedItem = this.toString().split('|')[0];

					if (storedItem === id) {
						// Remove this id from the array
						storage.splice(storage.indexOf(id), 1);
						// Push back to localstorage

						localStorage.setItem(type, JSON.stringify(storage));
						$('input[name="cardTypeFilter"]').each(function () {
							if ($(this).val() === id) {
								$(this).prop('checked', false);
							}
						});
					}
				});
				// Remove keyword element with this data-id attribute
				$(`li.keyword[data-id="${id}"]`).remove();
				if (storage.length > 1) {
					// TODO: check why this isn't working 100%
					$(`ul#selectedCardTypes li.keyword[data-id="${id}"]`).addClass('disabled');
				}
			} else {
				$(`ul#selectedCardTypes li.keyword[data-id="${id}"]`).addClass('disabled');
			}
		}
		cardSearch();
	}
	// jquery autocomplete for keywords (and municipalities, but not in use)
	let suggestions = [];
	if ($('#searchAndFilters').length > 0) {
		$.ajax({
			url: Ajax.url,
			method: 'POST',
			data: {
				action: 'topten_fetch_suggestions',
				nonce: Ajax.nonce,
			},
			success(data) {
				suggestions = $.map(data.data, (item) => ({
					label: item.label,
					value: item.value,
				}));

				$('#cardkeywords').autocomplete({
					source: suggestions,
					// When user selects an item from the list, display values
					select(e, ui) {
						$('#cardkeywords').val(ui.item.label);
						$('#cardkeywordsValue').val(ui.item.value);
						return false;
					},
					change(e, ui) {
						if (!ui.item) {
							$('#cardkeywords').val('');
						}
					},
					focus(e, ui) {
						this.value = ui.item.label;

						e.preventDefault();
					},
					minLength: 1,
					// Show the list of values
				})._renderItem = function (ul, item) {
					return $('<li></li>')
						.data('item.autocomplete', item.label)
						.append(`<div data-id="${item.value}">${item.label}</div>`)
						.appendTo(ul);
				};
			},
			error(errorThrown) {
				// eslint-disable-next-line no-console
				console.error(errorThrown);
			},
		});
	}

	// handle checking / unchecking checkboxes from another element press
	function cardTypeFilters(id, name) {
		// These exist
		if ($(`input[name=${name}]`).length > 0) {
			// At least one of these is checked
			if (name === 'cardclassfilter') {
				if ($(`input[name=${name}]:checked`).length > 0) {
					// nothing here
				} else {
					// If no checkboxes are checked, prevent unchecking the last one
					$(`input[name=${name}][value=${id}]`).prop('checked', true);
				}
			} else if (name === 'cardTypeFilter') {
				if ($(`input[name=${name}]:checked`).length > 0) {
					// nothing here either anymore, maybe write this again in the future
				} else {
					// If no checkboxes are checked, prevent unchecking the last one
					$(`input[name=${name}][value=${id}]`).prop('checked', true);
				}
			}
		}
	}

	// Reset everything to defaults
	function resetAllFilters() {
		localStorage.clear();
		$('#freeText').val('');
		$('#cardLaw').val('');
		$('#cardCategory').val('');
		$('#cardDateStart').val('');
		$('#cardDateEnd').val('');
		$('#cardkeywords').val('');
		$('#cardkeywordsValue').val('');

		$('#selectedCardClasses').html('');

		$('#classCheckboxes')
			.find('input[type=checkbox]')
			.each(function () {
				const value = $(this).val();
				const name = $(this).attr('data-name');

				$(this).prop('checked', true);

				const li = $('<li class="keyword"></li>');
				$(li).attr('data-id', value);

				$(li).html(
					'<button class="removekeyword" aria-label="Poista" data-type="cardclassfilter" data-id="' +
						value +
						'"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">' +
						name +
						'</span>',
				);

				// $('#selectedCardClasses').append(
				// 	`<li class="keyword" data-id="${split[0]}"><button class="removekeyword" aria-label="Poista" data-type="cardclassfilter" data-id="${split[0]}"><span class="material-symbols" aria-hidden="true">close</span></button><span class="name">${split[1]}</span></li>`,
				// );

				$('#selectedCardClasses').append(li);
			});

		$('#typeCheckboxes')
			.find('input[type=checkbox]')
			.each(function () {
				$(this).prop('checked', true);
			});

		$('#cardSidebar ul#selectedkeywords li').each(function () {
			$(this).remove();
		});

		$('#cardSidebar figure').not('.classes').removeClass('active');

		cardSearch();
	}

	function doError(id, errorMessage) {
		$(id).addClass('error');
		$('#error-message').show();
		$('#error-message').text(errorMessage);
	}
	function clearErrors() {
		$('#searchCards input').removeClass('error');
		$('#searchCards #error-message').hide();
	}

	// Get the cards
	cardSearch();

	// Opens and closes the filters
	$('#toggleFilters').on('click', function () {
		$(this).toggleClass('active');
		$(this).parent('.title-wrapper').toggleClass('active');
		$('section.list').toggleClass('filters-active');
		$('#ajaxOverlay').toggleClass('filters-active');
		if ($('ul#selectedCardClasses li').length === 1) {
			$('ul#selectedCardClasses li').addClass('disabled');
		} else {
			$('ul#selectedCardClasses li').removeClass('disabled');
		}
		// toggle aria expanded
		if ($(this).attr('aria-expanded') === 'false') {
			$(this).attr('aria-expanded', 'true');
		} else {
			$(this).attr('aria-expanded', 'false');
		}
		$('#searchAndFilters').toggleClass('expanded');
		$('#searchCards').toggleClass('active');
		$('#cardSidebar').toggleClass('active');
	});

	// If there is a search form on the page
	if ($('#searchCards').length > 0) {
		// Immediately set the class checkboxes since we want these to be checked by default
		if ($('#classCheckboxes').length > 0) {
			// push all checked boxes to array and add to localstorage
			const checkedBoxes = [];
			$('#classCheckboxes input:checked').each(function () {
				checkedBoxes.push($(this).val());
			});
			localStorage.setItem('cardclassfilter', JSON.stringify(checkedBoxes));
		}

		let cardClassFilterLength = $('#classCheckboxes input').length;
		if (JSON.parse(localStorage.getItem('cardclassfilter')).length > 0) {
			cardClassFilterLength = JSON.parse(localStorage.getItem('cardclassfilter')).length;
		}

		// Do the same for card types
		if ($('#typeCheckboxes').length > 0) {
			// push all checked boxes to array and add to localstorage
			const typeBoxes = [];
			$('#typeCheckboxes input:checked').each(function () {
				typeBoxes.push($(this).val());
			});
			localStorage.setItem('cardTypeFilter', JSON.stringify(typeBoxes));
		}

		let cardTypeFilterLength = $('#typeCheckboxes input').length;
		if (JSON.parse(localStorage.getItem('cardTypeFilter')).length > 0) {
			cardTypeFilterLength = JSON.parse(localStorage.getItem('cardTypeFilter')).length;
		}

		if (
			localStorage.getItem('keywords') ||
			localStorage.getItem('cardLaw') ||
			localStorage.getItem('cardCategory') ||
			localStorage.getItem('cardDateStart') ||
			localStorage.getItem('cardDateEnd') ||
			localStorage.getItem('freeText') ||
			cardClassFilterLength < $('#classCheckboxes input').length ||
			cardTypeFilterLength < $('#typeCheckboxes input').length
		) {
			$('#toggleFilters').addClass('active');
			$('#toggleFilters').parent('.title-wrapper').toggleClass('active');
			$('.search').addClass('active');
			$('.sidebar').addClass('active');
			$('#searchAndFilters').addClass('expanded');
		}
		// Refresh filters on page load
		updateSingularFilters();

		// Init autocomplete fields
		//autoCompleteField();
		// autoCompleteField('municipalities', 2);

		// Search & filter button events
		$('#searchCards button.searchTrigger').on('click', () => {
			// Set localstorage values if input not empty
			if ($('#freeText').val() !== '') {
				localStorage.setItem('freeText', $('#freeText').val());
			}
			if ($('#cardDateStart').val() !== '') {
				localStorage.setItem('cardDateStart', $('#cardDateStart').val());
			}
			if ($('#cardDateEnd').val() !== '') {
				localStorage.setItem('cardDateEnd', $('#cardDateEnd').val());
			}
			if ($('#cardLaw').val() !== '') {
				const cardLawID = $('#cardLaw').val();
				const cardLawName = $('#cardLaw').find(':selected').attr('data-name');
				const toStore = `${cardLawID}|${cardLawName}`;
				localStorage.setItem('cardLaw', toStore);
			}
			if ($('#cardCategory').val() !== '') {
				const cardCategoryID = $('#cardCategory').val();
				const cardCategoryName = $('#cardCategory').find(':selected').attr('data-name');
				const toStore = `${cardCategoryID}|${cardCategoryName}`;
				localStorage.setItem('cardCategory', toStore);
			}
			const checkedBoxes = [];
			$('#classCheckboxes input:checked').each(function () {
				checkedBoxes.push($(this).val());
			});
			localStorage.setItem('cardclassfilter', JSON.stringify(checkedBoxes));
			const checkedTypesBoxes = [];
			$('#typeCheckboxes input:checked').each(function () {
				checkedTypesBoxes.push($(this).val());
			});
			localStorage.setItem('cardTypeFilter', JSON.stringify(checkedTypesBoxes));

			// Execute
			applyFilters('cardclassfilter');
			applyFilters('cardTypeFilter');
			cardSearch();
			updateSingularFilters();
		});
		// Set the fields values from localstorage if they exist
		if (localStorage.getItem('freeText') !== null) {
			$('#freeText').val(localStorage.getItem('freeText'));
		}
		if (localStorage.getItem('cardDateStart') !== null) {
			$('#cardDateStart').val(localStorage.getItem('cardDateStart'));
		}
		if (localStorage.getItem('cardDateEnd') !== null) {
			$('#cardDateEnd').val(localStorage.getItem('cardDateEnd'));
		}
		if (localStorage.getItem('cardLaw') !== null) {
			const cardLaw = localStorage.getItem('cardLaw');
			const splitLaw = cardLaw.split('|');
			$('#cardLaw').val(splitLaw[0]);
		}
		if (localStorage.getItem('cardCategory') !== null) {
			const cardCategory = localStorage.getItem('cardCategory');
			const splitCategory = cardCategory.split('|');
			$('#cardCategory').val(splitCategory[0]);
		}

		// Applies keyword filters to sidebar
		$('#keywordssearch').on('click', () => {
			// if this length 3 or over
			if ($('#cardkeywords').val().length >= 3) {
				applyFilters('keywords');
			} else {
				doError('#cardkeywords', 'Syötä vähintään kolme merkkiä.');
			}
		});

		// Do the same thing on enter press
		$('#cardkeywords').on('keypress', (e) => {
			if (e.which === 13) {
				applyFilters('keywords');
			}
		});
		/*
		$('#municipalitiessearch').on('click', () => {
			applyFilters('municipalities');
		});
*/
		// If there are keywords in localstorage, fetch them and display them
		if ($('#selectedkeywords').length > 0) {
			updateFilters('keywords');
		}

		/*	if($('#selectedmunicipalities').length > 0) {
			updateFilters('municipalities');
		}  */

		// Demolish all filters
		if ($('#resetFilters').length > 0) {
			$('#resetFilters').on('click', () => {
				resetAllFilters();
			});
		}

		// Sort order / display post types immediately on change
		if ($('#filterOrder').length > 0) {
			$('#filterOrder').on('change', () => {
				cardSearch();
			});
		}

		// Adds a class to the wrapping li element when there is only one filter which disables the remove button
		// TODO - this doesn't seem to work properly
		if ($('ul#selectedCardClasses li').length === 1) {
			$('ul#selectedCardClasses li').addClass('disabled');
		} else {
			$('ul#selectedCardClasses li').removeClass('disabled');
		}

		// Applies card class filters to sidebar and prevents the user from removing the last filter
		if ($('input[name="cardclassfilter"]').length > 0) {
			// Do this immediately on page load
			applyFilters('cardclassfilter');
			$('input[name="cardclassfilter"]').on('change', function () {
				if ($('input[name="cardclassfilter"]:checked').length > 0) {
					// nothing to do
				} else {
					$(this).prop('checked', true);
				}
			});
		}

		if ($('input[name="cardTypeFilter"]').length > 0) {
			// Do this immediately on page load
			applyFilters('cardTypeFilter');
			$('input[name="cardTypeFilter"]').on('change', function () {
				if ($('input[name="cardTypeFilter"]:checked').length > 0) {
					// nothing to do
				} else {
					$(this).prop('checked', true);
				}
			});
		}

		// Remove keyword from localstorage and from the DOM
		$(document).on('click', '.removekeyword', function () {
			const id = $(this).attr('data-id').toString();
			const type = $(this).attr('data-type').toString();
			removeFilters(id, type);
		});
		// freetext on enter press set localstorage value and execute search
		$('#freeText').on('keypress', function (e) {
			if (e.which === 13) {
				e.preventDefault();
				localStorage.setItem('freeText', $(this).val());
				cardSearch();
			}
		});

		// Make spans fire the checkbox change event
		$('.boxes span.check').on('click', function () {
			const id = $(this).attr('data-id');
			$('.boxes input[name=cardTypeFilter]').each(function () {
				if ($(this).val() === id) {
					// check or uncheck the checkbox with same value depending on whether it's checked or not
					$(this).prop('checked', !$(this).prop('checked'));
					cardTypeFilters(id, 'cardTypeFilter');
				}
			});
		});
		// same as above but for card classes
		$('#classCheckboxes span.check').on('click', function () {
			const id = $(this).attr('data-id');
			$('#classCheckboxes input[name=cardclassfilter]').each(function () {
				if ($(this).val() === id) {
					// check or uncheck the checkbox with same value depending on whether it's checked or not
					$(this).prop('checked', !$(this).prop('checked'));
					cardTypeFilters(id, 'cardclassfilter');
				}
			});
		});
	}
});
