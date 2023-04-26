/* eslint-disable no-param-reassign */
/* eslint-disable no-underscore-dangle */
/* global Ajax */
jQuery(document).ready(($) => {
	const ajaxSpinner = $('#ajaxSpinner');
	$(document).ajaxStart(() => {
		$(ajaxSpinner).show();
	}).ajaxStop(() => {
		$(ajaxSpinner).hide();
	});
	// Fetch cards from database via query
	function cardSearch() {
		const freeText = $('#freeText').val();
		const cardkeywords = JSON.parse(localStorage.getItem('keywords'));
		const cardmunicipalities = JSON.parse(localStorage.getItem('municipalities'));
		const cardLaw = $('#cardLaw').val();
		const cardCategory = $('#cardCategory').val();
		const filterOrder = $('#filterOrder').val();
		const cardDateStart = $('#cardDateStart').val();
		const cardDateEnd = $('#cardDateEnd').val();
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
				freeText,
				cardkeywords,
				cardmunicipalities,
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
						jQuery(terms).each(function() {
							$(`#selected${type}`).append(`<li class="keyword" data-id="${this.value}">${this.label} <button class="removekeyword" data-type="${type}" data-id="${this.value}">x</button></li>`);
						});
					}
				},
				error(errorThrown) {
					console.log(errorThrown);
				},
			})
		}
	}

	function sgimm() {
		/*
					<ul class="keywords" id="selectedText"></ul>
				<ul class="keywords" id="selectedkeywords"></ul>
				<ul class="keywords" id="selectedmunicipalities"></ul>
				<ul class="keywords" id="selectedDateRange"></ul>
				<ul class="keywords" id="selectedCategory"></ul>
				<ul class="keywords" id="selectedLaw"></ul>
				*/
		// Get items from local storage
		/*
		const freeText = localStorage.getItem('freeText');
		const cardLaw = localStorage.getItem('cardLaw');
		const cardCategory = JSON.parse(localStorage.getItem('cardCategory'));
		const cardDateStart = JSON.parse(localStorage.getItem('cardDateStart'));
		const cardDateEnd = JSON.parse(localStorage.getItem('cardDateEnd'));
		if (freeText) {
			$('#selectedText').html('');
			$('#selectedText').append(`<li class="keyword" data-id="${freeText}">${freeText} <button class="removekeyword" data-type="freeText" data-id="${freeText}">x</button></li>`);
		}
		if (cardLaw) {
			$('#selectedLaw').html('');
			$('#selectedLaw').append(`<li class="keyword" data-id="${cardLaw}">${cardLaw} <button class="removekeyword" data-type="cardLaw" data-id="${cardLaw}">x</button></li>`);
		}
		*/
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
				$(`#selected${type}`).append(`<li class="keyword" data-id="${keyword}">${$(`#card${type}`).val()} <button class="removekeyword" data-type="${type}" data-id="${keyword}">x</button></li>`);	
			} 
		} else {
			// If there are no items in DOM, append it
			$(`#selected${type}`).append(`<li class="keyword" data-id="${keyword}">${$(`#card${type}`).val()} <button class="removekeyword" data-type="${type}" data-id="${keyword}">x</button></li>`);
		}
	}
	
	function removeFilters(id, type) {
		// Remove keyword div with this data-id attribute
		$(`li.keyword[data-id="${id}"]`).remove();
		const storage = JSON.parse(localStorage.getItem(type));
		$(storage).each(function() {
			const storedItem = this.toString();
			if (storedItem === id) {
				// Remove this id from the array
				storage.splice(storage.indexOf(id), 1);
				// Push back to localstorage
				if(storage.length <= 0) {
					localStorage.removeItem(type);
				} else {
					localStorage.setItem(type, JSON.stringify(storage));
				}
			}
		});
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

	// If there is a search form on the page
	if($('#searchCards').length > 0) {

		// Init autocomplete fields
		autoCompleteField('keywords', 3);
		autoCompleteField('municipalities', 2);

		// Search & filter button events
		$('#searchCards button.searchTrigger').on('click', () => {
			cardSearch();
			sgimm();
			if($('#freeText').val() !== '') {
				localStorage.setItem('freeText', $('#freeText').val());
			}
			if($('#cardDateStart').val() !== '') {
				localStorage.setItem('dateStart', $('#cardDateStart').val());
			}
			if($('#cardDateEnd').val() !== '') {
				localStorage.setItem('dateEnd', $('#cardDateEnd').val());
			}
			if($('#cardLaw').val() !== '') {
				localStorage.setItem('cardLaw', $('#cardLaw').val());
			}
			if($('#cardCategory').val() !== '') {
				localStorage.setItem('cardCategory', $('#cardCategory').val());
			}
		});
		
		$('#keywordssearch').on('click', () => {
			applyFilters('keywords');
		});

		$('#municipalitiessearch').on('click', () => {
			applyFilters('municipalities');
		});

		// If there are keywords or municipalities in localstorage, fetch them and display them
		if($('#selectedkeywords').length > 0) {
			updateFilters('keywords');
		} 

		if($('#selectedmunicipalities').length > 0) {
			updateFilters('municipalities');
		} 

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
