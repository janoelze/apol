class CommentsMgr {
	constructor() {
		this.commentSelector = ".comment-body";
		this.commentParentSelector = ".thing.t1";
		this.commentHeaderSelector = ".meta";

		this.init();
	}

	init() {
		this.addClickListener();
	}

	addClickListener() {
		let commentHeaders = document.querySelectorAll(this.commentHeaderSelector);
		commentHeaders.forEach((commentHeader) => {
			commentHeader.addEventListener("click", (e) =>
				this.collapseComment(e.target)
			);
		});
	}

	collapseComment(target) {
		let parentComment = target.closest(this.commentParentSelector);

		// Toggle the 'collapsed' class
		let isCollapsed = parentComment.classList.toggle("collapsed");

		// If the comment is now collapsed, scroll to the next sibling
		if (isCollapsed) {
			this.scrollTonextCommentSibling(target);
		}
	}

	scrollTonextCommentSibling(target) {
		let nextComment = target.closest(
			this.commentParentSelector
		).nextElementSibling;
		nextComment.scrollIntoView();
	}
}

function initFormAutoSave() {
	let forms = document.querySelectorAll("form.autosave");

	forms.forEach(function (form) {
		console.log("form", form);
		form.addEventListener("change", function (event) {
			console.log("change");
			htmx.ajax(form);
		});
	});
}

function initPullToRefresh() {
	PullToRefresh.destroyAll();
	window.ptr = PullToRefresh.init({
		mainElement: ".container",
		triggerElement: ".container",
		onRefresh() {
			window.location.reload();
		},
	});
}

function setOverScrollAmount(val) {
	let ptrSvgElem = document.querySelector(".ptr--icon"); // this is a circle svg
	let minVal = 0;
	let maxVal = 400;
	let root = document.querySelector(".overscroll-amount");
	root.innerHTML = `${val}`;

	// update circle border stroke percentage
	let percent = (val / maxVal) * 100;
	let dashArray = (percent / maxVal) * 283;
	ptrSvgElem.style.strokeDasharray = `${dashArray} 283`;

	if (val <= minVal) {
		root.style.display = "none";
	} else {
		root.style.display = "block";
	}

	if (val >= maxVal) {
		root.style.fontWeight = "800";
	} else {
		root.style.fontWeight = "400";
	}
}

function overScrollDetection() {
	var startY, startScrollTop;

	console.log("overscroll detection");

	document.addEventListener("touchstart", function (e) {
		var target = e.target;
		startY = e.touches[0].pageY;
		startScrollTop = target.scrollTop;

		if (startScrollTop === 0) {
			document.body.classList.add("overscroll-start");
		} else if (startScrollTop + target.clientHeight === target.scrollHeight) {
			document.body.classList.add("overscroll-end");
		}
	});

	document.addEventListener("touchmove", function (e) {
		var target = e.target;
		var currentY = e.touches[0].pageY;
		var distanceY = currentY - startY;

		if (distanceY > 0 && startScrollTop === 0) {
			setOverScrollAmount(distanceY);
			document.body.classList.remove("overscroll-end");
			document.body.classList.add("overscroll-start");
		} else if (
			distanceY < 0 &&
			startScrollTop + target.clientHeight === target.scrollHeight
		) {
			setOverScrollAmount(distanceY);
			document.body.classList.remove("overscroll-start");
			document.body.classList.add("overscroll-end");
		} else {
			setOverScrollAmount(distanceY);
			document.body.classList.remove("overscroll-start", "overscroll-end");
		}
	});

	document.addEventListener("touchend", function (e) {
		var target = e.target;
		document.body.classList.remove("overscroll-start", "overscroll-end");
		setOverScrollAmount(0);
	});
}

function initScrolltoTop() {
	const scrollToTopElem = document.querySelector(".scroll-to-top");
	const containerEl = document.querySelector(".container");
	const topPosition = {
		top: 0,
		left: 0,
		behavior: "smooth",
	};

	scrollToTopElem.removeEventListener("click");

	scrollToTopElem.addEventListener("click", (e) => {
		containerEl.scrollTo(topPosition);
	});
}

let commentsInstance = null;

function getVisibleVideos() {
	let videos = document.querySelectorAll("video");
	let visibleVideos = [];
	let invisibleVideos = [];
	videos.forEach((video) => {
		let rect = video.getBoundingClientRect();
		let visiblePercent = (rect.top + rect.height) / rect.height;
		if (visiblePercent > 0.45) {
			visibleVideos.push(video);
		} else {
			invisibleVideos.push(video);
		}
	});
	return {
		visibleVideos,
		invisibleVideos,
	};
}

class VideoMgr {
	constructor() {
		this.playerRefs = {};
		this.uninitiatedVideos = [];
		this.visibleVideos = [];
		this.invisibleVideos = [];
		this.tickInterval = 450;
		this.tick();
	}
	getVideoVisibilityPercentage(video) {
		let rect = video.getBoundingClientRect();
		let visiblePercent = (rect.top + rect.height) / rect.height;
		return visiblePercent;
	}
	updateVideoIndex() {
		let self = this;

		self.uninitiatedVideos = [];
		self.visibleVideos = [];
		self.invisibleVideos = [];

		document.querySelectorAll("video").forEach((video) => {
			let parentNode = video.parentNode;
			if (parentNode && parentNode.classList.contains("raw-video")) {
				self.uninitiatedVideos.push(video);
				return;
			}
		});

		document.querySelectorAll(".plyr--video video").forEach((video) => {
			if (self.getVideoVisibilityPercentage(video) > 0.45) {
				self.visibleVideos.push(video);
			} else {
				self.invisibleVideos.push(video);
			}
		});
	}
	initiateVideo(video) {
		let videoId = video.getAttribute("id");
		let isiOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

		// isiOS = false;

		console.log("initiating", videoId);

		let mp4Url = video.getAttribute("data-mp4-url");
		let posterUrl = video.getAttribute("data-poster-url");
		let hlsUrl = video.getAttribute("data-hls-url");
		let dashUrl = video.getAttribute("data-dash-url");
		let videoWidth = video.getAttribute("data-width");
		let videoHeight = video.getAttribute("data-height");
		let hasAudio = video.getAttribute("data-has-audio") === "1";

		let controls = ["play", "progress", "current-time"];
		
		if (hasAudio) {
			controls.push("mute");
		}

		this.playerRefs[videoId] = new Plyr(video, {
			controls: controls,
			playsinline: true,
			muted: true,
			autoplay: true,
		});

		console.log("videoId", this.playerRefs[videoId].elements);

		console.log("isiOS", isiOS);

		if (isiOS) {
			this.playerRefs[videoId].source = {
				type: "video",
				title: videoId,
				poster: posterUrl,
				sources: [
					{
						src: hlsUrl,
						type: "application/x-mpegURL",
					},
				],
			};
		}else{
			this.playerRefs[videoId].source = {
				type: "video",
				title: videoId,
				poster: posterUrl,
				sources: [
					{
						src: mp4Url,
						type: "video/mp4",
					},
				],
			};
		}

		let containerEl = this.playerRefs[videoId].elements.container;
		containerEl.style.aspectRatio = `${videoWidth}/${videoHeight}`;

		let videoEl = containerEl.querySelector("video");
		videoEl.muted = true;
	}
	activateVideo(video) {
		let userHasInteracted = this.hasUserInteracted();

		if (!userHasInteracted) {
			return;
		}

		video.play();
	}
	deactivateVideo(video) {
		let isPlaying = !video.paused;
		if (isPlaying) {
			video.pause();
		}
	}
	isVideoPlayable(video) {
		return video.canPlayType && video.duration > 0;
	}
	hasUserInteracted() {
		return (
			document.visibilityState === "visible" ||
			document.visibilityState === "hidden" ||
			document.hidden === false
		);
	}
	tick() {
		this.updateVideoIndex();
		this.uninitiatedVideos.forEach(this.initiateVideo.bind(this));
		this.visibleVideos.forEach(this.activateVideo.bind(this));
		this.invisibleVideos.forEach(this.deactivateVideo.bind(this));
		window.setTimeout(this.tick.bind(this), this.tickInterval);
	}
}

function onLoad() {
	console.log("hello :^)");
	// overScrollDetection();
	if (!window.commentsMgrRef) {
		window.commentsMgrRef = new CommentsMgr();
	}
	if (!window.videoMgrRef) {
		window.videoMgrRef = new VideoMgr();
	}
}

function afterSwap() {
	window.videoMgrRef = new VideoMgr();
	window.commentsMgrRef = new CommentsMgr();
}

window.addEventListener("DOMContentLoaded", onLoad);
window.addEventListener("load", onLoad);
window.addEventListener("htmx:afterSwap", afterSwap);
