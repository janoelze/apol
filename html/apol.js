function runVideos() {
	document.querySelectorAll("video").forEach(function (video) {
		video.play();
	});
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

	// var form = document.getElementById("myForm");
	// var initialFormData = new FormData(form);

	// function hasFormChanged() {
	// 	var currentFormData = new FormData(form);
	// 	for (var pair of currentFormData.entries()) {
	// 		if (
	// 			!initialFormData.has(pair[0]) ||
	// 			initialFormData.get(pair[0]) !== pair[1]
	// 		) {
	// 			return true;
	// 		}
	// 	}
	// 	return false;
	// }

	// function autosaveForm() {
	// 	if (hasFormChanged()) {
	// 		htmx.ajax(form);
	// 		initialFormData = new FormData(form);
	// 	}
	// }

	// setInterval(autosaveForm, 5000);
}

function onLoad() {
	// console.log("onLoad");
	// initFormAutoSave();
	runVideos();
}

window.addEventListener("DOMContentLoaded", onLoad);
window.addEventListener("load", onLoad);
window.addEventListener("htmx:afterSwap", onLoad);
