import Splide from '@splidejs/splide';
import '@splidejs/splide/dist/css/splide.min.css';
// import { breakpoints } from '/js/src/variables/breakpoints';

// https://splidejs.com/guides/options/

// Tämä on omana funktionaan jotta saadaan koodit toimimaan myös editorissa
function initSliders() {
	const sliders = document.querySelectorAll('.splide.carousel');

	sliders.forEach((slider) => {
		const splide = new Splide(slider, {
			// type: 'loop',
			// perPage: 1,
			// perMove: 1,
			type: 'fade',
			pagination: false,
			autoplay: true,
			speed: 2500,
			interval: 7500,
			rewind: true,
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
