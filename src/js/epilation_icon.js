/**
 * src/js/epilation_icon.js
 *
 * @author Johannes Braun <johannes.braun@hannenz.de>
 * @package hautliebe
 */
function EpilationIcon () {

	var self = this;
	var s, path;
	var hairPath,
		circlePath,
		origPath,
		destPath,
		startFrame,
		endFrame,
		permanentAnimation,
		hairPathLength
	;

	this.init = function () {
		self.s = Snap ('#icon-epilation');
		self.hairPath = self.s.select ('#epilation-hair');
		self.circlePath = self.s.select ('#epilation-circle');
		self.origPath = 'M71.848 20.907c8.294 6.24 13.872 15.97 14.464 27.145 1.062 20.055-14.334 37.173-34.388 38.236C31.869 87.35 14.75 71.954 13.688 51.899c-1.062-20.054 14.334-37.173 34.388-38.235';
		self.destPath = 'M 63.057299,16.121032 C 79.279633,21.483413 86.592204,38.71521 86.362854,50 85.954781,70.078504 70.08265,86.362854 50,86.362854 29.91735,86.362854 13.70575,70.082533 13.637146,50 13.568852,30.008408 32.524564,6.3634295 62.696163,15.979296';
		self.startFrame = origPath;
		self.endFrame = destPath;

		self.s.node.addEventListener ('mouseenter', self.animateIn, false);
		self.s.node.addEventListener ('mouseleave', self.animateOut, false);
		self.permanentAnimation = false;

        self.hairPathLength = self.hairPath.getTotalLength ();
		self.hairPath.node.style.strokeDasharray = self.hairPathLength + 'px ' + self.hairPathLength + 'px';
		self.hairPath.node.style.strokeDashoffset = 0;
		self.hairPath.node.style.transition = '350ms linear';
	}



	this.animate = function (callback) {

		self.path.animate ({
			d: self.endFrame
		}, 1000, mina.easin, callback);
	};



	this.animateIn = function () {
		if (this.permanentAnimation) {
			return;
		}
		self.hairPath.node.style.strokeDashoffset = self.hairPathLength + 'px';
		window.setTimeout (function () {
			self.circlePath.animate ({
				d: self.destPath
			}, 350, mina.linear);
		}, 75);
	};



	this.animateOut = function () {
		if (this.permanentAnimation) {
			return;
		}
		window.setTimeout (function () {
			self.hairPath.node.style.strokeDashoffset = 0;
		}, 75);
		self.circlePath.animate ({
			d: self.origPath
		}, 350, mina.linear);
	};


	this.animateInSlow = function () {
		self.hairPath.node.style.strokeDashoffset = self.hairPathLength + 'px';
		window.setTimeout (function () {
			self.circlePath.animate ({
				d: self.destPath
			}, 700 * 1.25, mina.linear);
		}, 150 * 1.25);
	};



	this.animateOutSlow = function () {
		window.setTimeout (function () {
			self.hairPath.node.style.strokeDashoffset = 0;
		}, 150 * 1.25);
		self.circlePath.animate ({
			d: self.origPath
		}, 700 * 1.25, mina.linear);
	};

	this.animateInOut = function () {

		var dur = 850 * 1.25;
		self.hairPath.node.style.transition = '800ms linear';
		self.permanentAnimation = true;
		// setTimeout(_doAnim, 3 * dur);
		_doAnim ();
		function _doAnim () {
			window.setTimeout (self.animateOutSlow, dur);
			self.animateInSlow ();
			if (self.permanentAnimation) {
				window.setTimeout(_doAnim, 3 * dur);
			}
		}
	}

	this.stopAnimation = function () {
		this.permanentAnimation = false;
	}

	this.init ();
}


