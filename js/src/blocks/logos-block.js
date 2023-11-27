import Splide from '@splidejs/splide';
import '@splidejs/splide/dist/css/splide.min.css';
// import { breakpoints } from '/js/src/variables/breakpoints';

// https://splidejs.com/guides/options/

// Tämä on omana funktionaan jotta saadaan koodit toimimaan myös editorissa
function initSliders() {
	const sliders = document.querySelectorAll('.splide.carousel.logos');

	sliders.forEach((slider) => {
		const splide = new Splide(slider, {
			type: 'loop',
			perPage: 1,
			perMove: 1,
			autoplay: true,
			speed: 2500,
			interval: 7500,
			rewind: true,
			arrows: false,
			pagination: true,
			i18n: {
				prev: 'Edellinen',
				next: 'Seuraava',
				slideX: 'Mene diaan %s',
				pageX: 'Mene sivulle %s',
				first: 'Mene ensimmäiseen diaan',
				last: 'Mene viimeiseen diaan',
				play: 'Aloita automaattinen toisto',
				pause: 'Keskeytä automaattinen toisto',
				select: 'Valitse sivu',
				slideLabel: 'Dia %s',
			},
		});

		splide.mount();
	});
}

document.addEventListener('DOMContentLoaded', () => {
	// Kutsutaan funktiota kun sivu on ladattu
	initSliders();

	// Jos ollaan editorissa, ladataan funktio kun ACF on valmis
	if (window.acf) {
		window.acf.addAction('render_block_preview', initSliders);
	}
});
