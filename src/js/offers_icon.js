/**
 * src/js/offers_icon.js
 *
 * @author Johannes Braun <johannes.braun@hannenz.de>
 * @package hautliebe
 */
function OffersIcon (el) {

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
		self.path = self.s.select ('#plus-path');
		self.origPath = 'M 8.6125838,50 H 91.38742  M 49.999999,8.6125815 V 91.387419';
		self.destPath = 'M 21.140085,50 H 78.859919 M 50,21.140083        v 57.719835';
		self.startFrame = origPath;
		self.endFrame = destPath;
		self.s.node.parentNode.parentNode.addEventListener ('mouseenter', self.animateIn, false);
		self.s.node.parentNode.parentNode.addEventListener ('mouseleave', self.animateOut, false);
		self.permanentAnimation = false;
	};



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


