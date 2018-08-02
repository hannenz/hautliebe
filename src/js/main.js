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



	this.animateWrinklesIcon = function () {
		var s = Snap ('#icon-wrinkles');
		var path =  s.select ('#wrinkles-path');
		var origPath = path.attr ('d');
		var destPath = 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z';

		function doAnimate () {
			path.animate ({
				d: destPath
			}, 1000, mina.easein, swap);
		}

		function swap () {
			var tmp = destPath;
			destPath = origPath;
			origPath = tmp;
			doAnimate ();
		}

		doAnimate ();
	};



    this.createWrinklesIcon = function () {

		var s = Snap('#icon-wrinkles');
		var path = s.select ('#wrinkles-path');
		var origPath = path.attr ('d');

        s.node.addEventListener ('mouseenter', function (ev) {
            path.animate ({
                d: 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z'
            }, 350, mina.easein);
        });

        s.node.addEventListener ('mouseleave', function (ev) {
			console.log ('mouseleave');
            path.animate ({
                d: origPath
            }, 350, mina.easein);
        });
    };

    this.createScarIcon = function () {
        var s = Snap ('#icon-scars');
        var path = s.select ('#scars-path');
		var origPath = path.attr ('d');

        s.node.addEventListener ('mouseenter', function (ev) {
            path.animate ({
                d: 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z'
            }, 350, mina.easein);
        });

        s.node.addEventListener ('mouseleave', function (ev) {
            path.animate ({
                d: origPath
            }, 350, mina.easein);
        });
    };

    this.createEpilationIcon = function () {
        var s = Snap ('#icon-epilation');
        var hairPath = s.select ('#epilation-hair');
        var circlePath = s.select ('#epilation-circle');
		var origCirclePath = circlePath.attr ('d');

        var hairPathLength = hairPath.getTotalLength ();
        hairPath.node.style.strokeDasharray = hairPathLength + 'px ' + hairPathLength + 'px';
        hairPath.node.style.strokeDashoffset = 0; 
        hairPath.node.style.transition = '350ms linear';

        s.node.addEventListener ('mouseenter', function (ev) {
            hairPath.node.style.strokeDashoffset = hairPathLength + 'px';
            setTimeout (function () {
                circlePath.animate ({
                    d: 'M 63.057299,16.121032 C 79.279633,21.483413 86.592204,38.71521 86.362854,50 85.954781,70.078504 70.08265,86.362854 50,86.362854 29.91735,86.362854 13.70575,70.082533 13.637146,50 13.568852,30.008408 32.524564,6.3634295 62.696163,15.979296'
                }, 350, mina.linear);
            }, 75);
        });

        s.node.addEventListener ('mouseleave', function (ev) {
            setTimeout (function () {
                hairPath.node.style.strokeDashoffset = 0;
            }, 75);
            circlePath.animate ({
                d: origCirclePath
            }, 350, mina.linear); 
        });
    };


	/**
	 * ScrollSpy
	 * https://jsfiddle.net/mekwall/up4nu/
	 */
	this.scrollSpy = function () {
		// Cache selectors
		var lastId,
			topMenu = $(".main-nav"),
			topMenuHeight = topMenu.outerHeight() + 15,
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
				popup: "<b>HAUTLIEBE®</b><br>Hafenbad 31<br>89073 Ulm",
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
				return false;
			});
			$el.find ('.tabs__trigger').first().addClass ('tabs__trigger--is-active');
			$el.find ('.tabs__panel').first().addClass ('tabs__panel--is-active');
		});
	};

	this.setupHiddenSection = function () {
		
		var hiddenSection = document.querySelector ('.section--hidden');
		console.log (hiddenSection);
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
			$('.compare').twentytwenty ({
				before_label: 'Vorher',
				after_label: 'Nach 1 Behandlung'
			});
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
		var heartLineTween = new TimelineMax()
			.add (TweenMax.to (heartLinePath, 1, { strokeDashoffset: 0, ease: Linear.easeNone }));

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


        this.createWrinklesIcon ();
        this.createScarIcon ();
        this.createEpilationIcon ();
		this.setupTabs ();
		this.setupHiddenSection ();

		this.animateWrinklesIcon ();
	};
};

document.addEventListener('DOMContentLoaded', function() {

	var app = new APP();
	app.init();

});

