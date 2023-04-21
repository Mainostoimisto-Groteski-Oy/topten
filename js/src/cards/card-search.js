/* global Ajax */
jQuery(document).ready(($) => {
	function cardSearch() {
		const freeText = $('#freeText').val();
		const cardKeywords = $('#cardKeywords').val();
		const keywordArray = cardKeywords.split(',');
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
				keywordArray,
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
	$('#searchCards button.searchTrigger').on('click', () => {
		cardSearch();
	});

});
