/* eslint-disable no-param-reassign */
/* eslint-disable no-underscore-dangle */
/* global Ajax */
jQuery(document).ready(($) => {
	// Fetch cards from database via query
	// cardStatusType is an acf field set in korttiluettelo template that determines which cards are shown (valid, expired or 2025 law)
	function cardSearch(cardStatusType = 'valid') {
		// if value is in localstorage, use it instead of form value

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
			jQuery('#cardCategory').val(splitCategory[0]);
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
		const cardTypes = [];
		$('input[name="cardTypeFilter"]:checked').each(function () {
			cardTypes.push($(this).val());
		});
		let cardClasses = '';
		if (localStorage.getItem('cardclassfilter')) {
			cardClasses = JSON.parse(localStorage.getItem('cardclassfilter'));
		} else {
			cardClasses = [];
			$('input[name="cardclassfilter"]:checked').each(function () {
				cardClasses.push($(this).val());
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
				console.log(errorThrown);
			},
		});
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
						jQuery(terms).each(function () {
							$(`#selected${type}`).append(
								`<li class="keyword" data-id="${this.value}"><button class="removekeyword" data-type="${type}" data-id="${this.value}"></button><span>${this.label}</span></li>`
							);
						});
					}
				},
				error(errorThrown) {
					console.log(errorThrown);
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
					`<li class="keyword" data-id="${splitLaw[0]}"><button class="removekeyword" data-type="cardLaw" data-id="${splitLaw[0]}"></button><span>${splitLaw[1]}</span></li>`
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
					`<li class="keyword" data-id="${splitCategory[0]}"><button class="removekeyword" data-type="cardCategory" data-id="${splitCategory[0]}"></button><span>${splitCategory[1]}</span></li>`
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
					`<li class="keyword" data-id="dateStart"><button class="removekeyword" data-type="cardDateStart" data-time="${cardDateStart}" data-id="dateStart"></button><span>${dateString}</span></li>`
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
					`<li class="keyword" data-id="dateEnd"><button class="removekeyword" data-type="cardDateEnd" data-time="${cardDateEnd}" data-id="dateEnd"></button><span>${dateString}</span></li>`
				);
			}
		}
		if (type === 'freeText') {
			const freeText = localStorage.getItem('freeText');
			if (freeText) {
				$('#selectedFreeText').html('');
				$(`#selectedFreeText`).parent('figure').addClass('active');
				$('#selectedFreeText').append(
					`<li class="keyword" data-id="freeText"><button class="removekeyword" data-type="freeText" data-id="freeText"></button><span>${freeText}</span></li>`
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
						`<li class="keyword" data-id="${keyword}"><button class="removekeyword" data-type="${type}" data-id="${keyword}"></button><span>${$(
							`#card${type}`
						).val()}</span></li>`
					);
				}
			} else {
				// If there are no items in DOM, append it
				$(`#selected${type}`).parent('figure').addClass('active');
				$(`#selected${type}`).append(
					`<li class="keyword" data-id="${keyword}"><button class="removekeyword" data-type="${type}" data-id="${keyword}"></button><span>${$(
						`#card${type}`
					).val()}</span></li>`
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
						`<li class="keyword" data-id="${split[0]}"><button class="removekeyword" data-type="cardclassfilter" data-id="${split[0]}"></button><span>${split[1]}</span></li>`
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
				console.log(storage.length);
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
					// TODO: check why this is fucky
					$(`ul#selectedCardClasses li.keyword[data-id="${id}"]`).addClass('disabled');
				}
			} else {
				$(`ul#selectedCardClasses li.keyword[data-id="${id}"]`).addClass('disabled');
			}
		}
		cardSearch();
	}

	// jquery autocomplete for keywords (and municipalities, but not in use)
	function autoCompleteField(type, minLen) {
		// match the type to the correct taxonomy
		let typeName = '';
		if (type === 'keywords') {
			typeName = 'asiasanat';
		} else if (type === 'municipalities') {
			typeName = 'kunta';
		}
		$(`#card${type}`).autocomplete({
			source(request, response) {
				const userInput = $(`#card${type}`).val();
				$.ajax({
					url: Ajax.url,
					method: 'POST',
					data: {
						action: 'topten_fetch_suggestions',
						nonce: Ajax.nonce,
						userInput,
						type: typeName,
					},
					success(data) {
						response(
							$.map(data.data, (item) => ({
								label: item.label,
								value: item.value,
							}))
						);
					},
					error(errorThrown) {
						console.log(errorThrown);
					},
				});
			},
			// When user selects an item from the list, display values
			select(e, ui) {
				$(`#card${type}`).val(ui.item.label);
				$(`#card${type}Value`).val(ui.item.value);
				return false;
			},
			minLength: minLen,
			// Show the list of values
		})._renderItem = function (ul, item) {
			return $('<li></li>')
				.data('item.autocomplete', item.label)
				.append(`<div data-id="${item.value}">${item.label}</div>`)
				.appendTo(ul);
		};
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
					cardSearch();
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
		$('#classCheckboxes')
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

	// Applies ajax overlay when ajax is running
	const ajaxOverlay = $('#ajaxOverlay');
	$(document)
		.ajaxStart(() => {
			$(ajaxOverlay).fadeIn(200);
		})
		.ajaxStop(() => {
			$(ajaxOverlay).fadeOut(200);
		});

	// ACF Field that tells code what kind of cards we want to search for
	const cardStatusType = $('#primary').attr('data-template');
	// Get the cards
	// if we have keyword as query string via urlsearchparams
	const urlParams = new URLSearchParams(window.location.search);
	const keyword = urlParams.get('keyword');
	if (keyword) {
		localStorage.clear();
		if ($('#classCheckboxes').length > 0) {
			// push all checked boxes to array and add to localstorage
			const checkedBoxes = [];
			$('#classCheckboxes input:checked').each(function () {
				checkedBoxes.push($(this).val());
			});
			localStorage.setItem('cardclassfilter', JSON.stringify(checkedBoxes));
		}
		$(`#cardkeywordsValue`).val(keyword);
		applyFilters('keywords');
	}

	cardSearch(cardStatusType);

	// Opens and closes the filters
	$('#toggleFilters').on('click', function () {
		$(this).toggleClass('active');

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

		if (
			localStorage.getItem('keywords') ||
			localStorage.getItem('cardLaw') ||
			localStorage.getItem('cardCategory') ||
			localStorage.getItem('cardDateStart') ||
			localStorage.getItem('cardDateEnd') ||
			localStorage.getItem('freeText') ||
			cardClassFilterLength < $('#classCheckboxes input').length
		) {
			$('.toggler').addClass('active');
			$('.search').addClass('active');
			$('.sidebar').addClass('active');
		}
		// Refresh filters on page load
		updateSingularFilters();

		// Init autocomplete fields
		autoCompleteField('keywords', 3);
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
			// Execute
			applyFilters('cardclassfilter');
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
			jQuery('#cardCategory').val(splitCategory[0]);
		}
		// Applies keyword filters to sidebar
		$('#keywordssearch').on('click', () => {
			applyFilters('keywords');
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

		// Adds a class to the wrapping li element when there is only one filter which disables the remove button // TODO - this doesn't seem to work properly
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

		// Card type filter checkbox change
		$('input[name="cardTypeFilter"]').on('change', function () {
			const id = $(this).val();
			cardTypeFilters(id, 'cardTypeFilter');
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
