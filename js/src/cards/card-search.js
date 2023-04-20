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
				console.log(data);
			},
			error(errorThrown) {
				console.log(errorThrown);
			},
		});
	}
	$('#searchCards select.searchTrigger').on('change', () => {
		cardSearch();
	});
	$('#searchCards button.searchTrigger').on('click', () => {
		cardSearch();
	});
	$('#searchCards input.searchTrigger').on('click', () => {
		cardSearch();
	});
});
