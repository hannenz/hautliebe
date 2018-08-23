/**
 * src/js/wrinkles_icon.js
 *
 * @author Johannes Braun <johannes.braun@hannenz.de>
 * @package hautliebe
 */
function WrinklesIcon (el) {

	var self = this;
	var s, path, el = el;
	var origPath,
		destPath,
		startFrame,
		endFrame,
		permanentAnimation
	;

	this.init = function () {
		self.s = Snap (el);
		self.path = self.s.select ('#wrinkles-path');
		self.origPath = 'M 64.54 19.325c2.758 1.308 9.696.565 11.915 2.66 2.22 2.097 1.875 9.066 3.338 11.745 1.463 2.679 7.512 6.156 8.076 9.156.563 3-3.812 8.436-4.203 11.463-.39 3.027 2.46 9.396 1.152 12.154-1.307 2.758-8.042 4.585-10.138 6.804-2.095 2.219-3.533 9.047-6.212 10.51-2.679 1.463-9.2-1.018-12.2-.455-3 .564-8.177 5.243-11.204 4.852-3.027-.391-6.845-6.232-9.603-7.54-2.759-1.307-9.697-.564-11.916-2.66-2.22-2.096-1.875-9.065-3.338-11.744-1.463-2.679-7.512-6.156-8.076-9.156-.563-3 3.812-8.436 4.203-11.463.39-3.027-2.46-9.396-1.152-12.154 1.307-2.758 8.042-4.585 10.138-6.804 2.095-2.219 3.533-9.047 6.212-10.51 2.679-1.463 9.2 1.018 12.2.455 3-.564 8.177-5.243 11.204-4.852 3.027.391 6.845 6.232 9.603 7.54z';
		self.destPath = 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z';
		self.startFrame = origPath;
		self.endFrame = destPath;
		self.s.node.addEventListener ('mouseenter', self.animateIn, false);
		self.s.node.addEventListener ('mouseleave', self.animateOut, false);
		self.permanentAnimation = false;
	}



	this.animate = function (callback) {

		self.path.animate ({
			d: self.endFrame
		}, 1000, mina.easin, callback);
	};



	this.animateIn = function () {
		if (self.permanentAnimation) {
			return;
		}
		self.path.animate ({
			d: self.destPath
		}, 350, mina.easin);
	};



	this.animateOut = function () {
		if (self.permanentAnimation) {
			return;
		}
		self.path.animate ({
			d: self.origPath
		}, 350, mina.easin);
	};


	this.animateInOut = function () {

		// Reset start/end frame
		self.startFrame = self.origPath;
		self.endFrame = self.destPath;

		self.permanentAnimation = true;

		// Animate with callback
		self.animate (swap);

		// Swap start/end frame each time the animation ends
		function swap () {
			var tmp = self.endFrame;
			self.endFrame = self.startFrame;
			self.startFrame = tmp;

			if (self.permanentAnimation === true) {
				self.animate (swap);
			}
		}
	}

	this.stopAnimation = function () {
		this.permanentAnimation = false;
	}

	this.init ();
}


