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
		document.querySelectorAll("video").forEach((video) => {
			if (self.getVideoVisibilityPercentage(video) > 0.45) {
				self.visibleVideos.push(video);
			} else {
				self.invisibleVideos.push(video);
			}
		});
	}
	activateVideo(video) {
		let videoId = video.getAttribute("id");
		let hasSourceElement = video.innerHTML.includes("source");
		// let hasDisabledSource = video.innerHTML.includes("disabledsource");
		// let hasRef = typeof this.playerRefs[videoId] !== "undefined";

		if (typeof this.playerRefs[videoId] === "undefined") {
			let mp4Url = video.getAttribute("data-mp4-url");
			let posterUrl = video.getAttribute("data-poster-url");
			let hlsUrl = video.getAttribute("data-hls-url");
			let dashUrl = video.getAttribute("data-dash-url");
			let videoWidth = video.getAttribute("data-width");
			let audioUrl = false;

			if (mp4Url) {
				audioUrl = mp4Url.replace("DASH_720.mp4", "audio");
				audioUrl = audioUrl.replace("DASH_1080.mp4", "audio");
				audioUrl = audioUrl.replace("DASH_360.mp4", "audio");
			}

			// alert('a');

			// let sourceEl = document.createElement("source");
			// sourceEl.setAttribute("src", mp4Url);
			// sourceEl.setAttribute("type", "video/mp4");
			// video.appendChild(sourceEl);


			// let dash = dashjs.MediaPlayer().create();
			// dash.initialize(video, dashUrl, true);

			// alert(dashUrl);
			// video.innerHTML = video.innerHTML.replace("disabledsource", "source");
			// alert(hlsUrl);
			// if (Hls.isSupported()) {

			// 	var hls = new Hls();
			// 	hls.loadSource(hlsUrl);
			// 	hls.attachMedia(video);
			// 	hls.on(Hls.Events.MANIFEST_PARSED, function () {
			// 		video.play();
			// 	});
			// }

			this.playerRefs[videoId] = new Plyr(video, {
				controls: ["play", "progress", "current-time", "mute"],
				autoplay: true,
				muted: true,
				playsinline: true,
			});

			this.playerRefs[videoId].source = {
				type: "video",
				title: videoId,
				poster: posterUrl,
				sources: [
					{
						src: hlsUrl,
						type: "application/x-mpegURL"
					}
				]
			};
		};

		// if(!hasRef){
			
		// };

		


			// if (typeof this.playerRefs[videoId] !== "undefined") {
			// 	if (this.playerRefs[videoId].paused) {
			// 		this.playerRefs[videoId].play();
			// 	}
			// } else {
			// 	video.innerHTML = video.innerHTML.replace("disabledsource", "source");
			// 	video.parentNode.innerHTML += `hallo`;
			// }
	}
	deactivateVideo(video) {
		video.pause();
	}
	tick() {
		this.updateVideoIndex();
		this.visibleVideos.forEach(this.activateVideo.bind(this));
		this.invisibleVideos.forEach(this.deactivateVideo.bind(this));
		window.setTimeout(this.tick.bind(this), this.tickInterval);
	}
}

// function visibleVideoDetection() {
// 	let { visibleVideos, invisibleVideos } = getVisibleVideos();
// 	visibleVideos.forEach(activateVideo);
// 	invisibleVideos.forEach(deactivateVideo);
// 	window.setTimeout(visibleVideoDetection, 350);
// }

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
