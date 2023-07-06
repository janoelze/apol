class CommentsClass {
    constructor() {
        this.commentSelector = '.comment-body';
        this.commentParentSelector = '.thing.t1';
        this.commentHeaderSelector = '.meta';

        this.init();
    }

    init() {
        this.addClickListener();
    }

    addClickListener() {
        let commentHeaders = document.querySelectorAll(this.commentHeaderSelector);
        commentHeaders.forEach(commentHeader => {
            commentHeader.addEventListener('click', (e) => this.collapseComment(e.target));
        });
    }

    collapseComment(target) {
        let parentComment = target.closest(this.commentParentSelector);

        // Toggle the 'collapsed' class
        let isCollapsed = parentComment.classList.toggle('collapsed');

        // If the comment is now collapsed, scroll to the next sibling
        if (isCollapsed) {
            this.scrollTonextCommentSibling(target);
        }
    }

    scrollTonextCommentSibling(target) {
        let nextComment = target.closest(this.commentParentSelector).nextElementSibling;
        nextComment.scrollIntoView();
    }
}

function initFormAutoSave() {
    let forms = document.querySelectorAll("form.autosave");

    forms.forEach(function(form) {
        console.log("form", form);
        form.addEventListener("change", function(event) {
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

    document.addEventListener("touchstart", function(e) {
        var target = e.target;
        startY = e.touches[0].pageY;
        startScrollTop = target.scrollTop;

        if (startScrollTop === 0) {
            document.body.classList.add("overscroll-start");
        } else if (startScrollTop + target.clientHeight === target.scrollHeight) {
            document.body.classList.add("overscroll-end");
        }
    });

    document.addEventListener("touchmove", function(e) {
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

    document.addEventListener("touchend", function(e) {
        var target = e.target;
        document.body.classList.remove("overscroll-start", "overscroll-end");
        setOverScrollAmount(0);
    });
}

function initScrolltoTop() {
    //remove all listeners from scroll to top
    document.querySelector('.scroll-to-top').removeEventListener('click', function() {});

    document.querySelector('.scroll-to-top').addEventListener('click', function() {
        // scroll to top logic
        //scroll the .content div to the top
        document.querySelector('.container').scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
        });
    });
}

let commentsInstance = null;

function getVisibleVideos(){
	let videos = document.querySelectorAll('video');
	let visibleVideos = [];
	let invisibleVideos = [];
	videos.forEach(video => {
		let rect = video.getBoundingClientRect();
		let visiblePercent = (rect.top + rect.height) / rect.height;
		if(visiblePercent > 0.6){
			visibleVideos.push(video);
		}else{
			invisibleVideos.push(video);
		}
	});
	return {
		visibleVideos,
		invisibleVideos
	};
}

function activateVideo(video){
	let innerHTML = video.innerHTML.replace('disabledsource', 'source');
	video.innerHTML = innerHTML;
	if(video.paused){
		video.play();
	}
}

function deactivateVideo(video){
	video.pause();
}

function visibleVideoDetection(){
	let {visibleVideos, invisibleVideos} = getVisibleVideos();
	visibleVideos.forEach(activateVideo);
	invisibleVideos.forEach(deactivateVideo);
	window.setTimeout(visibleVideoDetection, 500);
}

function onLoad() {
    console.log("hello :^)");
    overScrollDetection();
		visibleVideoDetection();
    if (!commentsInstance) {
        commentsInstance = new CommentsClass();
    }
}

window.addEventListener("DOMContentLoaded", onLoad);
window.addEventListener("load", onLoad);
window.addEventListener("htmx:afterSwap", onLoad);