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

    this.createWrinklesIcon = function () {
        var s = new Snap (100, 100);
        // var path = s.path ('m 67.320227,14.877942 c 3.22593,1.529055 11.343497,0.650118 13.939035,3.101203 2.595538,2.451084 2.182369,10.605635 3.893445,13.738819 1.711077,3.133186 8.794952,7.193497 9.454075,10.702085 0.659123,3.508588 -4.468261,9.862903 -4.925612,13.403448 -0.45735,3.540546 2.887035,10.989204 1.35798,14.215135 -1.529054,3.225929 -9.412168,5.352877 -11.863253,7.948415 C 76.724812,80.582585 75.052265,88.574456 71.91908,90.285532 68.785895,91.996609 61.158133,89.083768 57.649545,89.74289 54.140957,90.402013 48.090334,95.884474 44.549788,95.427124 41.009243,94.969774 36.550379,88.12975 33.324449,86.600695 30.098519,85.07164 21.980952,85.950577 19.385414,83.499493 16.789876,81.048408 17.203045,72.893858 15.491969,69.760673 13.780892,66.627488 6.6970169,62.567177 6.0378939,59.058589 5.378771,55.550001 10.506156,49.195686 10.963506,45.65514 11.420856,42.114595 8.0764714,34.665936 9.6055261,31.440006 c 1.5290549,-3.22593 9.4121689,-5.352878 11.8632529,-7.948415 2.451085,-2.595538 4.123632,-10.587409 7.256818,-12.298486 3.133185,-1.711076 10.760947,1.201765 14.269534,0.542642 3.508588,-0.659123 9.559211,-6.1415832 13.099757,-5.6842331 3.540546,0.45735 7.99941,7.2973741 11.225339,8.8264281 z');
        var path = s.path ('m 64.861771,20.064681 c 2.758095,1.307306 9.696389,0.564965 11.915513,2.660585 2.219124,2.095619 1.874873,9.065014 3.337802,11.743814 1.462931,2.6788 7.512466,6.15646 8.076,9.156221 0.563535,2.99976 -3.811478,8.435773 -4.202502,11.462856 -0.391023,3.027084 2.459036,9.396398 1.15173,12.154492 -1.307306,2.758094 -8.041974,4.584353 -10.137594,6.803477 -2.09562,2.219124 -3.533662,9.04723 -6.212462,10.51016 -2.6788,1.46293 -9.200709,-1.018134 -12.200469,-0.454599 -2.99976,0.563534 -8.176621,5.242328 -11.203705,4.851304 C 42.359001,88.561968 38.540999,82.721262 35.782905,81.413957 33.02481,80.106651 26.086516,80.848992 23.867392,78.753372 21.648268,76.657753 21.99252,69.688357 20.52959,67.009558 c -1.462931,-2.6788 -7.512465,-6.156461 -8.076,-9.156221 -0.563535,-2.99976 3.811478,-8.435774 4.202502,-11.462857 0.391024,-3.027083 -2.459035,-9.396397 -1.15173,-12.154492 1.307306,-2.758094 8.041974,-4.584353 10.137594,-6.803476 2.09562,-2.219124 3.533662,-9.04723 6.212462,-10.510161 2.6788,-1.46293 9.200709,1.018135 12.200469,0.4546 2.999761,-0.563535 8.176621,-5.242328 11.203705,-4.851304 3.027083,0.391023 6.845085,6.231728 9.603179,7.539034 z');
        s.attr ({
            'viewBox': '0 0 100 100',
            'class': 'icon icon--wrinkles'
        });

        s.node.addEventListener ('mouseenter', function (ev) {
            console.log ('mouseenter');
            path.animate ({
                d: 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z'
            }, 350, mina.easein);
        });

        s.node.addEventListener ('mouseleave', function (ev) {
            path.animate ({
                d: 'm 64.861771,20.064681 c 2.758095,1.307306 9.696389,0.564965 11.915513,2.660585 2.219124,2.095619 1.874873,9.065014 3.337802,11.743814 1.462931,2.6788 7.512466,6.15646 8.076,9.156221 0.563535,2.99976 -3.811478,8.435773 -4.202502,11.462856 -0.391023,3.027084 2.459036,9.396398 1.15173,12.154492 -1.307306,2.758094 -8.041974,4.584353 -10.137594,6.803477 -2.09562,2.219124 -3.533662,9.04723 -6.212462,10.51016 -2.6788,1.46293 -9.200709,-1.018134 -12.200469,-0.454599 -2.99976,0.563534 -8.176621,5.242328 -11.203705,4.851304 C 42.359001,88.561968 38.540999,82.721262 35.782905,81.413957 33.02481,80.106651 26.086516,80.848992 23.867392,78.753372 21.648268,76.657753 21.99252,69.688357 20.52959,67.009558 c -1.462931,-2.6788 -7.512465,-6.156461 -8.076,-9.156221 -0.563535,-2.99976 3.811478,-8.435774 4.202502,-11.462857 0.391024,-3.027083 -2.459035,-9.396397 -1.15173,-12.154492 1.307306,-2.758094 8.041974,-4.584353 10.137594,-6.803476 2.09562,-2.219124 3.533662,-9.04723 6.212462,-10.510161 2.6788,-1.46293 9.200709,1.018135 12.200469,0.4546 2.999761,-0.563535 8.176621,-5.242328 11.203705,-4.851304 3.027083,0.391023 6.845085,6.231728 9.603179,7.539034 z'
            }, 350, mina.easein);
        });

        document.getElementById ('js-placeholder-wrinkles').innerHTML = '';
        document.getElementById ('js-placeholder-wrinkles').append (s.node);
    };

    this.createScarIcon = function () {
        var s = new Snap (100, 100);
        var path = s.path ('m 53.226556,20.288204 13.200969,-6.841764 3.644634,14.420575 14.708047,2.211531 -5.53538,13.809456 10.603241,10.436357 -12.591326,7.916629 2.435923,14.672503 L 64.856782,75.931557 58.199256,89.223804 46.775315,79.71048 33.573291,86.552309 29.928074,72.132617 15.2205,69.921906 20.755469,56.112686 10.152403,45.675681 22.743663,37.757995 20.307568,23.086139 35.143625,24.067427 41.801559,10.774943 Z');
        s.attr ({
            'viewBox': '0 0 100 100',
            'class': 'icon icon--scar'
        });

        s.node.addEventListener ('mouseenter', function (ev) {
            path.animate ({
                d: 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z'
            }, 350, mina.easein);
        });

        s.node.addEventListener ('mouseleave', function (ev) {
            path.animate ({
                d: 'm 53.226556,20.288204 13.200969,-6.841764 3.644634,14.420575 14.708047,2.211531 -5.53538,13.809456 10.603241,10.436357 -12.591326,7.916629 2.435923,14.672503 L 64.856782,75.931557 58.199256,89.223804 46.775315,79.71048 33.573291,86.552309 29.928074,72.132617 15.2205,69.921906 20.755469,56.112686 10.152403,45.675681 22.743663,37.757995 20.307568,23.086139 35.143625,24.067427 41.801559,10.774943 Z'
            }, 350, mina.easein);
        });

        document.getElementById ('js-placeholder-scars').innerHTML = '';
        document.getElementById ('js-placeholder-scars').append (s.node);
    };

    this.createEpilationIcon = function () {
        var s = new Snap (100, 100);
        s.attr ({
            'viewBox': '0 0 100 100',
            'class': 'icon icon--epilation'

        });
        var hairPath = s.path ('M 46.956855,50.312638 C 53.41525,44.884108 65.835241,30.973717 59.849304,4.4942936');
        var circlePath = s.path ('m 73.355192,22.128068 c 7.95201,6.670358 13.00766,16.680827 13.00766,27.872106 0,20.082648 -16.2802,36.362852 -36.36285,36.362854 -20.08265,0 -36.362854,-16.280204 -36.362854,-36.362854 0,-20.08265 16.280204,-36.362856 36.362854,-36.362854');

        var hairPathLength = hairPath.getTotalLength ();
        hairPath.node.style.strokeDasharray = hairPathLength + 'px ' + hairPathLength + 'px';
        hairPath.node.style.strokeDashoffset = 0; //hairPathLength + 'px';
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
                d: 'm 73.355192,22.128068 c 7.95201,6.670358 13.00766,16.680827 13.00766,27.872106 0,20.082648 -16.2802,36.362852 -36.36285,36.362854 -20.08265,0 -36.362854,-16.280204 -36.362854,-36.362854 0,-20.08265 16.280204,-36.362856 36.362854,-36.362854'
            }, 350, mina.linear); 
        });

        document.getElementById ('js-placeholder-epilation').innerHTML = '';
        document.getElementById ('js-placeholder-epilation').append (s.node);
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
		
		var HautliebeIcon = L.Icon.extend ({
			options: {
				iconSize: [ 100, 100 ],
				iconAnchor: [ 50, 100 ]
			}
		});

		var heartIcon = new HautliebeIcon ({ iconUrl: '/dist/img/pin_heart.png', iconSize: [150, 150], iconAnchor: [75, 150] }),
			busIcon = new HautliebeIcon ({ iconUrl: '/dist/img/pin_bus.png' }),
			parking1Icon = new HautliebeIcon ({ iconUrl: '/dist/img/pin_parking1.png' }),
			parking2Icon = new HautliebeIcon ({ iconUrl: '/dist/img/pin_parking2.png' });

		L.marker([48.40091, 9.99367 ], { icon: heartIcon }).addTo(map);
		L.marker([48.40198, 9.99213 ], { icon: busIcon }).addTo(map);
		L.marker([48.40111, 9.99604 ], { icon: parking1Icon }).addTo(map);
		L.marker([48.40073, 9.99491 ], { icon: parking2Icon }).addTo(map);
	};



	this.main = function() {

		this.scrollSpy ();



		// Flickity carousel

		window.onload = function () {
			$('.carousel').flickity({
				cellAlign: 'left',
				wrapAround: true,
				contain: true
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
			duration: "50%",
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
			duration: "75%"
		})
		.setTween (timelineStaggerTween)
		.addTo (ctl);


        this.createWrinklesIcon ();
        this.createScarIcon ();
        this.createEpilationIcon ();
	};
};

document.addEventListener('DOMContentLoaded', function() {

	var app = new APP();
	app.init();

});

