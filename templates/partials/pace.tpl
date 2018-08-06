	<style type="text/css">
		.pace {
			-webkit-pointer-events: none; pointer-events: none;
			-webkit-user-select: none; -moz-user-select: none; user-select: none;
			position: fixed;
			z-index: 8000;
			top: 0; left: 0;
			width: 100vw; height: 100vh;
			background-color: #fff;
			transition: opacity 500ms ease-out;
			display: flex; }
		.pace-inactive {
			opacity: 0; }
		.pace .pace-progress {
			visibility: hidden;
			background: rgb(238, 208, 212);
			position: fixed;
			z-index: 2000;
			top: 0; left: 0%;
			width: 100%; height: 100vh; }
		.pace svg {
			margin: auto; }
	</style>
	<script src="/dist/js/vendor/pace.min.js"></script>
	<script>
		var iv, h, hp, p;
		Pace.on('start', function () {
			p = document.querySelector ('.pace');
			while (p.hasChildNodes ()) {
				p.removeChild (p.lastChild);
			}
			h = document.createElementNS ('http://www.w3.org/2000/svg', 'svg');
			hp = document.createElementNS ('http://www.w3.org/2000/svg', 'path');
			h.setAttribute ('width', 100);
			h.setAttribute ('height', 100);
			h.setAttribute ('viewBox', '0 0 100 100');
			hp.setAttribute ('d', 'M 25.588103,14.41136 C 35.780661,12.754519 45.854552,19.56175 49.99964,30.148367 54.144728,19.562496 64.218618,12.754519 74.411175,14.41136 c 11.72924,1.906113 19.164128,14.29063 16.605726,27.663271 -2.111353,15.192193 -40.607528,43.766728 -40.607528,43.766728 0,0 -37.719254,-28.574535 -41.4262494,-43.766728 C 6.4247218,28.70199 13.858864,16.317473 25.588103,14.41136 Z');
			h.appendChild (hp);
			p.appendChild (h);
			hp.style.fill = 'none';
			hp.style.stroke = '#fabfcd';
			hp.style.strokeLinecap = 'round';
			hp.style.strokeLinejoin = 'round';
			hp.style.strokeWidth = 10;
			hp.style.strokeDasharray = '256 256';
			hp.style.strokeDashoffset = 256;
			hp.style.transition = '150ms ease-out';
			iv = window.setInterval (function () {
				hp.style.strokeDashoffset = 256 - (parseInt (p.firstChild.getAttribute ('data-progress')) * 2.56);
			}, 50);
		});
		Pace.on('hide', function () {
			window.clearInterval (iv);
			hp.style.strokeDashoffset = 0;
		});
		window.setTimeout (function () {
			Pace.stop ();
			p.parentNode.removeChild (p);
		}, 6000);
	</script>

