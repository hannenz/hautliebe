/**
 * src/js/scars_icon.js
 *
 * @author Johannes Braun <johannes.braun@hannenz.de>
 * @package hautliebe
 */
function ScarsIcon (el) {

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
		self.path = self.s.select ('#scars-path');
		self.origPath = 'M53.226 20.289l13.201-6.842 3.645 14.42L84.78 30.08l-5.535 13.81 10.603 10.436-12.591 7.917 2.435 14.672-14.835-.982-6.658 13.292-11.424-9.513-13.202 6.842-3.645-14.42-14.708-2.21 5.535-13.81-10.603-10.437 12.591-7.917-2.436-14.672 14.836.981 6.658-13.292z';
		self.destPath = 'm 65.589186,17.110641 c 2.572515,1.219343 7.330574,4.472429 9.400383,6.427044 2.069809,1.954615 5.589732,6.518803 6.954228,9.017358 1.364496,2.498556 3.301731,7.927074 3.827348,10.724995 0.525616,2.797919 0.690531,8.559384 0.325818,11.382788 -0.364714,2.823405 -1.988261,8.353847 -3.207604,10.926361 -1.219343,2.572515 -4.472428,7.330572 -6.427043,9.400381 -1.954615,2.069809 -6.518805,5.589733 -9.01736,6.95423 -2.498556,1.364496 -7.927073,3.30173 -10.724992,3.827346 -2.797921,0.525617 -8.559387,0.690532 -11.382792,0.325819 C 42.513768,85.732249 36.983328,84.108702 34.410814,82.889359 31.838299,81.670016 27.08024,78.41693 25.010431,76.462315 22.940622,74.5077 19.420699,69.943512 18.056203,67.444957 16.691707,64.946401 14.754472,59.517883 14.228855,56.719962 13.703239,53.922043 13.538324,48.160578 13.903037,45.337174 c 0.364714,-2.823405 1.988261,-8.353847 3.207604,-10.926361 1.219343,-2.572515 4.472428,-7.330572 6.427043,-9.400381 1.954615,-2.069809 6.518805,-5.589733 9.01736,-6.95423 2.498556,-1.364496 7.927073,-3.30173 10.724992,-3.827346 2.797921,-0.525617 8.559387,-0.690532 11.382792,-0.325819 2.823404,0.364714 8.353844,1.988261 10.926358,3.207604 z';
		self.startFrame = origPath;
		self.endFrame = destPath;
		self.s.node.parentNode.parentNode.addEventListener ('mouseenter', self.animateIn, false);
		self.s.node.parentNode.parentNode.addEventListener ('mouseleave', self.animateOut, false);
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
		this.animateOut();
		// Make sure to animate back into initial state
	}

	this.init ();
}


