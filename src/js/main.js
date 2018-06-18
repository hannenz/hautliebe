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
		});
	};



	this.createMap = function () {

		var mapElem = document.getElementById('map'),
			lat = parseFloat(mapElem.getAttribute('data-latitude')),
			lng = parseFloat(mapElem.getAttribute('data-longitude')),
			zoom = parseInt(mapElem.getAttribute('data-zoom')),
			map = new L.map('map').setView([lat, lng], zoom);

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



		// Flickity carousel
		window.onload = function () {
			$('.carousel').flickity({
				cellAlign: 'left',
				wrapAround: true,
				contain: true
			});
		}
	};
};

document.addEventListener('DOMContentLoaded', function() {

	var app = new APP();
	app.init();

});

