/* eslint-disable no-param-reassign */
/* eslint-disable no-underscore-dangle */
/* global Ajax */
jQuery(document).ready(($) => {
	
	// Fetch cards from database via query
	// cardStatusType is an acf field set in korttiluettelo template that determines which cards are shown (valid, expired or 2025 law)
	function cardSearch(cardStatusType = 'valid') {
		// if value is in localstorage, use it instead of form value

		let freeText = '';
		if(localStorage.getItem('freeText')) {
			freeText = localStorage.getItem('freeText');
		} else {
			freeText = $('#freeText').val(); 
		}
		let cardLaw = '';
		if(localStorage.getItem('cardLaw')) {
			cardLaw = localStorage.getItem('cardLaw');
			const splitLaw = cardLaw.split('|');
			$('#cardLaw').val(splitLaw[0]);	
		} else {
			cardLaw = $('#cardLaw').val();
		}

		let cardCategory = '';
		if(localStorage.getItem('cardCategory')) {
			cardCategory = localStorage.getItem('cardCategory');
			const splitCategory = cardCategory.split('|');
			jQuery('#cardCategory').val(splitCategory[0]);
		} else {
			cardCategory = $('#cardCategory').val();
		}
		let filterOrder = '';
		if(localStorage.getItem('filterOrder')) {
			filterOrder = localStorage.getItem('filterOrder');
		} else {
			filterOrder = $('#filterOrder').val();
		}
		let cardDateStart = '';
		if(localStorage.getItem('cardDateStart')) {
			cardDateStart = localStorage.getItem('cardDateStart');
		} else {
			cardDateStart = $('#dateStart').val();
		}
		let cardDateEnd = '';
		if(localStorage.getItem('cardDateEnd')) {
			cardDateEnd = localStorage.getItem('cardDateEnd');
		} else {
			cardDateEnd = $('#cardDateEnd').val();
		}
		
		// if these are not in locaLstorage they're not in use at all
		const cardkeywords = JSON.parse(localStorage.getItem('keywords'));
		// const cardmunicipalities = JSON.parse(localStorage.getItem('municipalities'));

		// get checked checkboxes values and push them to array
		const cardTypes = [];
		$('input[name="cardTypeFilter"]:checked').each(function() {
			cardTypes.push($(this).val());
		});
		
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
				nonce: Ajax.nonce,
			},
			success(data) {
				// PHP code handles the data so we just need to append it to the DOM
				$('#listCards').html(data);
				// Hide empty categories
				// Tulkintakortti post type has three levels of categories, the others two
				$('#tulkintakortit ul.children').each(function() {
					const tulkintaStatus = [];
					const grandparent = $(this).attr('data-parent');
					$(this).children().children('ul.grandchildren').each(function() {
						const parent = $(this).attr('data-parent');
						if($(this).children().length <= 0) {
							$(this).parent(`li.child[data-id=${parent}]`).hide();
							tulkintaStatus.push('false');
						} else {
							tulkintaStatus.push('true');
						}
					});
					if(tulkintaStatus.indexOf('true') === -1){
						$(`#tulkintakortit li.parent[data-id=${grandparent}]`).hide();
					}
				});
				
				$('#ohjekortit ul.children').each(function() {
					if($(this).children().length <= 0) {
						$(this).hide();
						$(this).parent('li.parent').hide();
					}
				});
				$('#lomakekortit ul.children').each(function() {
					if($(this).children().length <= 0) {
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
		// For items with array type localstorage
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
		if(words) {
			$.ajax({
				url: Ajax.url,
				method: 'POST',
				data: {
					action: 'topten_fetch_terms',
					nonce: Ajax.nonce,
					keywords : words,
					type : typeName,
				},
				success(data) {
					const terms = data.data;
					if(terms.length > 0) {
						$(`#selected${type}`).html('');
						$(`#selected${type}`).parent('figure').show();
						jQuery(terms).each(function() {
							$(`#selected${type}`).append(`<li class="keyword" data-id="${this.value}"><button class="removekeyword" data-type="${type}" data-id="${this.value}"></button><span>${this.label}</span></li>`);
						});
					}
				},
				error(errorThrown) {
					console.log(errorThrown);
				},
			})
		}
	}

	function showFilters(type) {
		// For items with singular type localstorage
		// Get items from local storage

		

		
		/*
		const cardDateStart = JSON.parse(localStorage.getItem('cardDateStart'));
		const cardDateEnd = JSON.parse(localStorage.getItem('cardDateEnd')); */
		if (type === 'cardLaw') {
			const cardLaw = localStorage.getItem('cardLaw');
			if(cardLaw) {
				const splitLaw = cardLaw.split('|');
				$('#selectedLaw').html('');
				$(`#selectedLaw`).parent('figure').show();
				$('#selectedLaw').append(`<li class="keyword" data-id="${splitLaw[0]}"><button class="removekeyword" data-type="cardLaw" data-id="${splitLaw[0]}"></button><span>${splitLaw[1]}</span></li>`);
			}
		}
		if(type === 'cardCategory') {
			const cardCategory = localStorage.getItem('cardCategory'); 
			if (cardCategory) {
				const splitCategory = cardCategory.split('|');
				$('#selectedCategory').html('');
				$(`#selectedCategory`).parent('figure').show();
				$('#selectedCategory').append(`<li class="keyword" data-id="${splitCategory[0]}"><button class="removekeyword" data-type="cardCategory" data-id="${splitCategory[0]}"></button><span>${splitCategory[1]}</span></li>`);
			}
		}
		if(type === 'cardDateStart') {
			const cardDateStart = localStorage.getItem('cardDateStart');
			if (cardDateStart) {
				const date = new Date(cardDateStart);
				const day = date.getDate();
				const month = date.getMonth()+1;
				const year = date.getFullYear();
				const dateString = `${day}.${month}.${year}`;
				$('#selectedDateStart').html('');
				$(`#selectedDateStart`).parent('ul').parent('figure').show();
				$('#selectedDateStart').append(`<li class="keyword" data-id="dateStart"><button class="removekeyword" data-type="cardDateStart" data-time="${cardDateStart}" data-id="dateStart"></button><span>${dateString}</span></li>`);
			}
		}
		if(type === 'cardDateEnd') {
			const cardDateEnd = localStorage.getItem('cardDateEnd');
			if (cardDateEnd) {
				$('#selectedDateEnd').html('');
				$(`#selectedDateEnd`).parent('ul').parent('figure').show();
				const date = new Date(cardDateEnd);
				const day = date.getDate();
				const month = date.getMonth()+1;
				const year = date.getFullYear();
				const dateString = `${day}.${month}.${year}`;
				$('#selectedDateEnd').append(`<li class="keyword" data-id="dateEnd"><button class="removekeyword" data-type="cardDateEnd" data-time="${cardDateEnd}" data-id="dateEnd"></button><span>${dateString}</span></li>`);
			}
		}

	}

	function updateSingularFilters() {
		showFilters('cardLaw');
		showFilters('cardCategory');
		showFilters('cardDateStart');
		showFilters('cardDateEnd');
	}

	function applyFilters(type) {
		// Get chosen items from hidden input field
		const keyword = $(`#card${type}Value`).val();
		
		// Check if item is already in local storage
		if(localStorage.getItem(type)) {
			const storage = JSON.parse(localStorage.getItem(type));
			if(storage.indexOf(keyword) === -1) {
				storage.push(keyword);
				localStorage.setItem(type, JSON.stringify(storage));
			}
		} else {
			const storage = [];
			storage.push(keyword);
			localStorage.setItem(type, JSON.stringify(storage));
		}
		// Update filters to DOM
		if($(`#selected${type}`).children().length > 0) {
			const existingChildren = [];
			// Check if item is already in DOM
			$(`#selected${type}`).children().each(function() {
				existingChildren.push($(this).attr('data-id'));
			});
			// If not, append it
			if (existingChildren.indexOf(keyword) === -1) {
				$(`#selected${type}`).parent('figure').show();
				$(`#selected${type}`).append(`<li class="keyword" data-id="${keyword}"><button class="removekeyword" data-type="${type}" data-id="${keyword}"></button><span>${$(`#card${type}`).val()}</span></li>`);	
			} 
		} else {
			// If there are no items in DOM, append it
			$(`#selected${type}`).parent('figure').show();
			$(`#selected${type}`).append(`<li class="keyword" data-id="${keyword}"><button class="removekeyword" data-type="${type}" data-id="${keyword}"></button><span>${$(`#card${type}`).val()}</span></li>`);
		}
	}
	
	function removeFilters(id, type) {

		
		if(type === 'keywords') {
			const storage = JSON.parse(localStorage.getItem(type));
			$(storage).each(function() {
				const storedItem = this.toString();
				if (storedItem === id) {
					// Remove this id from the array
					storage.splice(storage.indexOf(id), 1);
					// Push back to localstorage
					if(storage.length <= 0) {
						localStorage.removeItem(type);
						// If it is the last item, hide the whole figure
						$(`li.keyword[data-id=${id}]`).parents('figure').hide();
						
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
				$(`li.keyword[data-id=${id}]`).parents('figure').hide();
				$(`li.keyword[data-id=${id}]`).remove();
				
			}
		} else if (type === 'cardCategory') {
			const storage = localStorage.getItem(type);
			const splitCategory = storage.split('|');
			if (splitCategory[0] === id) {
				localStorage.removeItem(type);
				$(`li.keyword[data-id=${id}]`).parents('figure').hide();
				$(`li.keyword[data-id=${id}]`).remove();
			}
		} else if (type === 'cardDateStart' || type === 'cardDateEnd') {
			if (type === 'cardDateStart') {
				$('#cardDateStart').val('');
				localStorage.removeItem('cardDateStart');
				// if cardDateEnd is empty, hide the whole figure
				if(localStorage.getItem('cardDateEnd') === null) {
					$(`li.keyword[data-id=${id}]`).parents('figure').hide();
				}
				$(`li.keyword[data-id=${id}]`).remove();
			} else if (type === 'cardDateEnd') {
				// do the same thing in reverse
				$('#cardDateEnd').val('');
				localStorage.removeItem('cardDateEnd');
				if(localStorage.getItem('cardDateStart') === null) {
					$(`li.keyword[data-id=${id}]`).parents('figure').hide();
				}
				$(`li.keyword[data-id=${id}]`).remove();
			}
		}
		cardSearch();
	}

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
						type : typeName,
					},
					success(data) {
						response( $.map( data.data, ( item ) => ({
							label: item.label,
							value: item.value,
						})));
					},
					error(errorThrown) {
						console.log(errorThrown);
					},
				})
				
			},
			// When user selects an item from the list, display values
			select(e, ui) {
				$(`#card${type}`).val(ui.item.label);
				$(`#card${type}Value`).val(ui.item.value);
				return false;
			},
			minLength: minLen,
			// Show the list of values
		})._renderItem = function( ul, item ) {
			return $( '<li></li>' )
				.data( 'item.autocomplete', item.label )
				.append(`<div data-id="${item.value}">${item.label}</div>`)
				.appendTo( ul );
		};
	}

	function resetAllFilters() {
		localStorage.clear();
		$('#freeText').val('');
		$('#cardLaw').val('');
		$('#cardCategory').val('');
		$('#cardDateStart').val('');
		$('#cardDateEnd').val('');
		$('#cardSidebar ul').html('');
		cardSearch();
	}

	const ajaxOverlay = $('#ajaxOverlay');
	$(document).ajaxStart(() => {
		$(ajaxOverlay).fadeIn(200);
	}).ajaxStop(() => {
		$(ajaxOverlay).fadeOut(200);
	});
	const cardStatusType = $('#primary').attr('data-template');
	cardSearch(cardStatusType);
	$('#toggleFilters').on('click', function() {
		$(this).toggleClass('active');
		// toggle aria expanded
		if($(this).attr('aria-expanded') === 'false') {
			$(this).attr('aria-expanded', 'true');
		} else {
			$(this).attr('aria-expanded', 'false');
		}
		$('#searchCards').toggleClass('active');
		$('#cardSidebar').toggleClass('active');
	});

	// If there is a search form on the page
	if($('#searchCards').length > 0) {
		if(localStorage.getItem('keywords') || localStorage.getItem('cardLaw') || localStorage.getItem('cardCategory') || localStorage.getItem('cardDateStart') || localStorage.getItem('cardDateEnd')) {
			$('.toggler').addClass('active');
			$('.search').addClass('active');
			$('.sidebar').addClass('active');
		}
		updateSingularFilters();
		// Init autocomplete fields
		autoCompleteField('keywords', 3);
		// autoCompleteField('municipalities', 2);

		// Search & filter button events
		$('#searchCards button.searchTrigger').on('click', () => {
			if($('#cardDateStart').val() !== '') {
				localStorage.setItem('cardDateStart', $('#cardDateStart').val());
			}
			if($('#cardDateEnd').val() !== '') {
				localStorage.setItem('cardDateEnd', $('#cardDateEnd').val());
			}
			if($('#cardLaw').val() !== '') {
				const cardLawID = $('#cardLaw').val();
				const cardLawName = $('#cardLaw').find(':selected').attr('data-name');
				const toStore = `${cardLawID}|${cardLawName}`;
				localStorage.setItem('cardLaw', toStore);
			}
			if($('#cardCategory').val() !== '') {
				const cardCategoryID = $('#cardCategory').val();
				const cardCategoryName = $('#cardCategory').find(':selected').attr('data-name');
				const toStore = `${cardCategoryID}|${cardCategoryName}`;
				localStorage.setItem('cardCategory', toStore);
			}
			cardSearch();
			updateSingularFilters();
		});
		if(localStorage.getItem('cardDateStart') !== null) {
			$('#cardDateStart').val(localStorage.getItem('cardDateStart'));
		}
		if(localStorage.getItem('cardDateEnd') !== null) {
			$('#cardDateEnd').val(localStorage.getItem('cardDateEnd'));
		}
		if(localStorage.getItem('cardLaw') !== null) {
			const cardLaw = localStorage.getItem('cardLaw');
			const splitLaw = cardLaw.split('|');
			$('#cardLaw').val(splitLaw[0]);	
		}
		if(localStorage.getItem('cardCategory') !== null) {
			const cardCategory = localStorage.getItem('cardCategory');
			const splitCategory = cardCategory.split('|');
			jQuery('#cardCategory').val(splitCategory[0]);
		}
		$('#keywordssearch').on('click', () => {
			applyFilters('keywords');
		});
		/*
		$('#municipalitiessearch').on('click', () => {
			applyFilters('municipalities');
		});
*/
		// If there are keywords in localstorage, fetch them and display them
		if($('#selectedkeywords').length > 0) {
			updateFilters('keywords');
		} 

		/*	if($('#selectedmunicipalities').length > 0) {
			updateFilters('municipalities');
		}  */
		
		if($('#resetFilters').length > 0) {
			$('#resetFilters').on('click', () => {
				resetAllFilters();
			});
		} 
		// Sort order / display post types immediately on change 
		if($('#filterOrder').length > 0) {
			$('#filterOrder').on('change', () => {
				cardSearch();
			});
		}
		if($('input[name="cardTypeFilter"]').length > 0) {
			$('input[name="cardTypeFilter"]').on('change', function() {
				// if this checked length is greater than 0
				if($('input[name="cardTypeFilter"]:checked').length > 0) {
					cardSearch();
				} else {
					// If no checkboxes are checked, prevent unchecking the last one
					$(this).prop('checked', true);
				}
			});
		}

		// Remove keyword from localstorage and from the DOM
		$(document).on('click', '.removekeyword', function() {
			const id = $(this).attr('data-id').toString();
			const type = $(this).attr('data-type').toString();
			removeFilters(id, type);
			
		});
		

	}
	
});
