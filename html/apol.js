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

function setOverScrollAmount(val){
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
	}else{
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

function onLoad() {
	console.log("hello :^)");
	overScrollDetection();
}

window.addEventListener("DOMContentLoaded", onLoad);
window.addEventListener("load", onLoad);
window.addEventListener("htmx:afterSwap", onLoad);
