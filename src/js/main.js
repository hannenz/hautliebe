/**
 * src/js/main.js
 *
 * main javascript file
 */

function APP () {

	var self = this;

	self.debug = false;

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
			topMenuHeight = topMenu.outerHeight()+15,
			// All list items
			menuItems = topMenu.find("a"),
			// Anchors corresponding to menu items
			scrollItems = menuItems.map(function(){
			  var item = $($(this).attr("href"));
			  if (item.length) { return item; }
			});

		console.log (topMenuHeight);
		// Bind click handler to menu items
		// so we can get a fancy scroll animation
		menuItems.click(function(e){
		  var href = $(this).attr("href"),
			  offsetTop = href === "#" ? 0 : $(href).offset().top-topMenuHeight+1;
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
		var map = new L.map('map').setView([48.4010452, 9.9914267], 17);
		L.tileLayer('https://{s}.tiles.mapbox.com/v3/hannenz.iodb36pi/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);
	};

	this.main = function() {

		this.scrollSpy ();
		this.createMap ();

		// Flickity carousel
		window.onload = function () {
			$('.carousel').flickity({
				cellAlign: 'left',
				wrapAround: true,
				contain: true
			});
		}

		// this.initThrottleResize();

		// Lazy loadiong images
		// this.bLazy = new Blazy({
		// 	selector: '.lazy',
		// 	offset: 100,
		// 	successClass: 'lazy--loaded',
		// 	errorClass: 'lazy--failed',
		// 	error: function(el) {
		// 	 	el.src = '/img/noimage.svg';
		// 	}
		// });
	};
};

document.addEventListener('DOMContentLoaded', function() {

	var app = new APP();
	app.init();

});

