/* eslint-disable no-param-reassign */
/* eslint-disable no-underscore-dangle */
/* global Ajax */
jQuery(document).ready(($) => {
	
	function cardSearch() {
		const freeText = $('#freeText').val();
		const cardKeywords = JSON.parse(localStorage.getItem('keywords'));
		const cardMunicipality = $('#cardMunicipality').val();
		const municipalityArray = cardMunicipality.split(',');
		const cardLaw = $('#cardLaw').val();
		const cardCategory = $('#cardCategory').val();
		const filterOrder = $('#filterOrder').val();
		const cardTulkinta = $('#cardTulkinta').is(':checked');
		const cardDateStart = $('#cardDateStart').val();
		const cardDateEnd = $('#cardDateEnd').val();
		const cardOhje = $('#cardOhje').is(':checked');
		const cardLomake = $('#cardLomake').is(':checked');

		$.ajax({
			url: Ajax.url,
			method: 'POST',
			data: {
				action: 'topten_card_search',
				freeText,
				cardKeywords,
				municipalityArray,
				cardLaw,
				cardCategory,
				cardDateStart,
				cardDateEnd,
				filterOrder,
				cardTulkinta,
				cardOhje,
				cardLomake,
				nonce: Ajax.nonce,
			},
			success(data) {
				// Data on käsitelty PHP:llä joten tarvitsee enää tulostaa se
				console.log(data);
				$('#listCards').html(data);

				// Piilota tyhjät kategoriat
				// Tulkintakorteilla on kolmitasoinen hierarkia, muilla kaksi
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

	function updateKeywords() {
		const words = JSON.parse(localStorage.getItem('keywords'));
		
		if(words) {
			
			$.ajax({
				url: Ajax.url,
				method: 'POST',
				data: {
					action: 'topten_fetch_terms',
					nonce: Ajax.nonce,
					keywords : words,
					type : 'asiasanat',
				},
				success(data) {
					const terms = data.data;
					if(terms.length > 0) {
						jQuery(terms).each(function() {
							$('#selectedKeywords').append(`<div class="keyword" data-id="${this.value}">${this.label} <button class="removeKeyword" data-id="${this.value}">x</button></div>`);
						});
					}
				},
				error(errorThrown) {
					console.log(errorThrown);
				},
			})
		}
	}

	$('#searchCards button.searchTrigger').on('click', () => {
		cardSearch();
	});
	if($('#searchCards').length > 0) {
		$('#cardKeywords').autocomplete({
			source(request, response) {
				const userInput = $('#cardKeywords').val();
				$.ajax({
					url: Ajax.url,
					method: 'POST',
					data: {
						action: 'topten_fetch_suggestions',
						nonce: Ajax.nonce,
						userInput,
						type : 'asiasanat',
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
			select(e, ui) {
				$("#cardKeywords").val(ui.item.label);
				$('#cardKeywordsValue').val(ui.item.value);
				return false;
			},
			minLength: 3,
		}).data( 'ui-autocomplete' )._renderItem = function( ul, item ) {
			return $( '<li></li>' )
				.data( 'item.autocomplete', item.label )
				.append(`<div data-id="${item.value}">${item.label}</div>`)
				.appendTo( ul );
		};

		$('#keywordSearch').on('click', () => {
			const keyword = $('#cardKeywordsValue').val();
			//  localStorage.setItem('checkedBoxes', JSON.stringify(this.indexes)); 
			if(localStorage.getItem('keywords')) {
				const storage = JSON.parse(localStorage.getItem('keywords'));
				if(storage.indexOf(keyword) === -1) {
					storage.push(keyword);
					localStorage.setItem('keywords', JSON.stringify(storage));
				}
			} else {
				const storage = [];
				storage.push(keyword);
				localStorage.setItem('keywords', JSON.stringify(storage));
			}
			if($('#selectedKeywords').children().length > 0) {
				$('#selectedKeywords').children().each(function() {
					if($(this).attr('data-id') !== keyword) {
						$('#selectedKeywords').append(`<div class="keyword" data-id="${keyword}">${$('#cardKeywords').val()} <button class="removeKeyword" data-id="${keyword}">x</button></div>`);	
					} 
				});
			} else {
				$('#selectedKeywords').append(`<div class="keyword" data-id="${keyword}">${$('#cardKeywords').val()} <button class="removeKeyword" data-id="${keyword}">x</button></div>`);
			}
			
		});
		// If this div exists
		if($('#selectedKeywords').length > 0) {
			updateKeywords();
		} 
		$(document).on('click', '.removeKeyword', function() {
			
			const id = $(this).attr('data-id').toString();
			// remove keyword div with this data-id attribute
			$(`div.keyword[data-id="${id}"]`).remove();
			console.log($(`div.keyword[data-id${id}]`));
			const storage = JSON.parse(localStorage.getItem('keywords'));
			$(storage).each(function() {
				const storedItem = this.toString();
				if (storedItem === id) {
					// remove this id from the array
					storage.splice(storage.indexOf(id), 1);
					// push back to localstorage
					if(storage.length <= 0) {
						localStorage.removeItem('keywords');
					} else {
						localStorage.setItem('keywords', JSON.stringify(storage));
					}
				}
			});
		});
		
	}
	
});
