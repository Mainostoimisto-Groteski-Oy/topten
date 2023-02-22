document.addEventListener('DOMContentLoaded', () => {
	const nodelist = document.querySelectorAll('.animate-on-scroll');
	let elements = [...nodelist];

	function triggerAnimation() {
		elements = elements.filter((el) => {
			const element = el;

			const { top } = element.getBoundingClientRect();

			if (top + top / 5 < window.innerHeight) {
				const animationName = element.dataset.animation;

				element.classList.add('animate__animated', animationName);

				return false;
			}

			return element;
		});
	}

	triggerAnimation();

	window.addEventListener('scroll', triggerAnimation);
});
