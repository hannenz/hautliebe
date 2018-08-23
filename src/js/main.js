/**
 * src/js/main.js
 *
 * @author Johannes Braun
 * @package hautliebe
 *
 * main javascript file
 */

function APP () {



	var self = this;
	self.debug = false;
	self.mapHasBeenCreated = false;
	self.wrinklesIcons = [];
	self.scarsIcons = [];
	self.epilationIcons = [];
	self.offersIcons = [];

	var icons = document.querySelectorAll ('.icon--wrinkles');
	icons.forEach (function (icon) {
		self.wrinklesIcons.push (new WrinklesIcon (icon));
	});
	icons = document.querySelectorAll ('.icon--scars');
	icons.forEach (function (icon) {
		self.scarsIcons.push (new ScarsIcon (icon));
	});
	icons = document.querySelectorAll ('.icon--epilation');
	icons.forEach (function (icon) {
		self.epilationIcons.push (new EpilationIcon (icon));
	});
	icons = document.querySelectorAll ('.icon--offers');
	icons.forEach (function (icon) {
		self.offersIcons.push (new OffersIcon (icon));
	});

	this.init = function() {

		if (this.debug) {
			console.log('APP::init');
		}

		this.pageInit();
	};



	this.pageInit = function() {

		if (this.debug) {
			console.log('APP::pageInit');
		}

		document.body.classList.add('page-has-loaded');

		this.main();
	};



	/**
	 * ScrollSpy
	 * https://jsfiddle.net/mekwall/up4nu/
	 */
	this.scrollSpy = function () {
		// Cache selectors
		var lastId,
			topMenu = $(".main-nav"),
			topMenuHeight = 70; //topMenu.outerHeight() + 15,
			// All list items
			menuItems = topMenu.find("a"),
			// Anchors corresponding to menu items
			scrollItems = menuItems.map(function(){
				var item = $($(this).attr("href"));
				if (item.length) { 
					return item;
				}
			});

		// Bind click handler to menu items
		// so we can get a fancy scroll animation
		menuItems.click(function(e){
			var href = $(this).attr("href"),
				offsetTop = href === "#" ? 0 : $(href).offset().top - topMenuHeight + 1;
			$('html, body').stop().animate({ 
				scrollTop: offsetTop
			}, 300);
			e.preventDefault();
		});

		// Bind to scroll
		$(window).scroll(function(){
			// Get container scroll position
			var fromTop = $(this).scrollTop()+topMenuHeight;

			// Get id of current scroll item
			var cur = scrollItems.map(function(){
				if ($(this).offset().top < fromTop)
					return this;
			});
			// Get the id of the current element
			cur = cur[cur.length-1];
			var id = cur && cur.length ? cur[0].id : "";

			if (lastId !== id) {
				lastId = id;
				// Set/remove active class
				menuItems
					.parent().removeClass("active")
					.end().filter("[href='#"+id+"']").parent().addClass("active");
			} 

			// Close mobile menu
			document.body.classList.remove ('menu-is-open');
		});
	};



	this.createMap = function () {

		var mapElem = document.getElementById('map'),
			lat = parseFloat(mapElem.getAttribute('data-latitude')),
			lng = parseFloat(mapElem.getAttribute('data-longitude')),
			zoom = parseInt(mapElem.getAttribute('data-zoom')),
			map = new L.map('map', { scrollWheelZoom: false }).setView([lat, lng], zoom);
		
		// "Click to scroll"
		map.addEventListener ('focus', function () {
			map.scrollWheelZoom.enable ();
		});
		map.addEventListener ('blur', function () {
			map.scrollWheelZoom.disable ();
		});

		L.tileLayer('https://{s}.tiles.mapbox.com/v3/hannenz.iodb36pi/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);

		var pins = [
			{
				iconUrl: '/dist/img/pin_heart.png',
				latLng: [ 48.40091, 9.99367 ],
				popup: "<b>HAUTLIEBE<sup>®</sup></b><br>Hafenbad&nbsp;31<br>89073 Ulm",
				iconSize: 120
			},
			{
				iconUrl: '/dist/img/pin_bus.png',
				latLng: [ 48.40198, 9.99213 ],
				popup: "Haltestelle &bdquo;Justizgebäude&ldquo;",
				iconSize: 100
			},
			{
				iconUrl: '/dist/img/pin_parking1.png',
				latLng: [ 48.40073, 9.99491 ],
				popup: "Parkhaus &bdquo;Kornhaus&ldquo;",
				iconSize: 100
			},
			{
				iconUrl: '/dist/img/pin_parking2.png',
				latLng: [ 48.40111, 9.99604 ],
				popup: "Parkhaus &bdquo;Frauenstraße&ldquo;",
				iconSize: 100
			}
		];

		pins.forEach (function (pin, i) {
			var icon = new L.Icon ({
				iconUrl: pin.iconUrl,
				iconSize: [ pin.iconSize, pin.iconSize ],
				iconAnchor: [ pin.iconSize / 2 , pin.iconSize ]
			});

			var marker = L.marker (pin.latLng, { icon: icon }).addTo (map);
			
			var popupOptions = {
				offset: L.point (0, -pin.iconSize * 2 / 3)
			};

			var popup = L.popup (popupOptions)
				.setLatLng (pin.latLng)
				.setContent (pin.popup)
			;
			if (i == 0) {
				popup.openOn (map);
			}
			marker.bindPopup (popup, popupOptions);

		});
	};


	this.setupTabs = function () {
		var $wrapper = $('<div class="tabs__wrapper" />');
		$wrapper.insertAfter ($('#tab-3'));
		$wrapper.append ($('#tab-1'));
		$wrapper.append ($('#tab-2'));
		$wrapper.append ($('#tab-3'));

		$wrapper = $('<div class="tabs__wrapper" />');
		$wrapper.insertAfter ($('#tab-5'));
		$wrapper.append ($('#tab-4'));
		$wrapper.append ($('#tab-5'));

		$('.tabs').each (function (i, el) {
			var $el = $(el);
			$el.find('.tabs__trigger').on ('click', function (ev) {
				ev.stopPropagation ();
				$el.find ('.tabs__trigger').removeClass ('tabs__trigger--is-active');
				$el.find ('.tabs__panel').removeClass ('tabs__panel--is-active');
				$el.find ($(this).attr('href')).addClass ('tabs__panel--is-active');
				$(this).addClass ('tabs__trigger--is-active');

				self.wrinklesIcons.forEach (function (icon) {
					icon.stopAnimation ();
				});
				self.scarsIcons.forEach (function (icon) {
					icon.stopAnimation ();
				});
				self.epilationIcons.forEach (function (icon) {
					icon.stopAnimation ();
				});
				self.offersIcons.forEach (function (icon) {
					icon.stopAnimation ();
				});

				switch (this.getAttribute('href')) {
					case '#treatment-1':
					case '#price-category-1':
						self.scarsIcons.forEach (function (icon) {
							icon.animateInOut ();
						});
						break;
					case '#treatment-2':
					case '#price-category-2':
						self.wrinklesIcons.forEach (function (icon) {
							icon.animateInOut ();
						});
						break;
					case '#treatment-3':
					case '#price-category-3':
						self.epilationIcons.forEach (function (icon) {
							icon.animateOut ();
						});
						self.epilationIcons.forEach (function (icon) {
							icon.animateInOut ();
						});
						break;
					case '#price-offers':
						self.offersIcons.forEach (function (icon) {
							icon.animateInOut ();
						});
						break;
				}
				return false;
			});

			$el.find ('.tabs__trigger').first().addClass ('tabs__trigger--is-active');
			$el.find ('.tabs__panel').first().addClass ('tabs__panel--is-active');
			
		});
			self.epilationIcons[0].animateInOut ();
			self.offersIcons[0].animateInOut ();
	};

	this.setupHiddenSection = function () {
		
		var hiddenSection = document.querySelector ('.section--hidden');
		hiddenSection.classList.add ('section--is-hidden');
		var trigger = document.querySelector ('[href="#' + hiddenSection.id + '"]');
		trigger.addEventListener ('click', function () {
			hiddenSection.classList.toggle ('section--is-hidden');		
		});
	};

	this.main = function() {

		this.scrollSpy ();



		// Flickity carousel

		window.onload = function () {

			var nSlides = $('.carousel > *').length;
			var flickityOptions = {
				cellAlign: 'left',
				wrapAround: true,
				contain: true
			};
			if (nSlides <= 1) {
				flickityOptions.prevNextButtons = false;
				flickityOptions.pageDots = false;
			}
			$('.carousel').flickity(flickityOptions);

			// Sticky header
			new ScrollMagic.Scene ({
				offset: 120,
			})
			.setClassToggle (document.body, 'page-has-scrolled')
			.addTo (ctl);

			// TwentyTwenty: Comparison slider
			// $('.compare').twentytwenty ({
			// 	before_label: 'Vorher',
			// 	after_label: 'Nach 1 Behandlung'
			// });
		}



		// Load the map only when scrolling near to it

		var ctl = new ScrollMagic.Controller ();
		var loadMap = new ScrollMagic.Scene ({
			triggerElement: '#map',
			triggerHook: 1
		})
		.on ('enter', function () {
				if (!self.mapHasBeenCreated) {
					self.createMap ();
					self.mapHasBeenCreated = true;
				}
			}
		)
		.addTo (ctl);




		// Heart line animation (timeline)

		var heartLinePath = document.getElementById ('path814');
		var pathLength = heartLinePath.getTotalLength ();
		heartLinePath.style.strokeDasharray = pathLength + ' ' + pathLength;
		heartLinePath.style.strokeDashoffset = pathLength;
		var heartLineTween = 
			(TweenMax.to (heartLinePath, 1, { strokeDashoffset: 0, ease: Linear.easeNone }));

		var heartLine = new ScrollMagic.Scene ({
			triggerElement: '.about__timeline',
			triggerHook: 0.8,
			duration: 0, //"50%",
			tweenChanges: true
		})
		.setTween (heartLineTween)
		.addTo (ctl);

		var timelineStaggerTween = new TweenMax.staggerFromTo ('.timeline__text ul > li', 1,
			{ opacity: 0, x: 140 }, { opacity: 1, x: 0 },
			0.25
		);
		var timelineStagger = new ScrollMagic.Scene ({
			triggerElement: '.about__timeline',
			triggerHook: 1,
			duration: 0 //"75%"
		})
		.setTween (timelineStaggerTween)
		.addTo (ctl);


        // this.createWrinklesIcon ();
        // this.createScarIcon ();
        // this.createEpilationIcon ();
		this.setupTabs ();
		this.setupHiddenSection ();

	};
};

document.addEventListener('DOMContentLoaded', function() {

	var app = new APP();
	app.init();

});

