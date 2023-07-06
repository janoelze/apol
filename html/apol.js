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
        mainElement: '.container',
        triggerElement: '.container',
        onRefresh() {
            window.location.reload();
        }
    });
}

function onLoad() {
    console.log("hello :^)");
}

window.addEventListener("DOMContentLoaded", onLoad);
window.addEventListener("load", onLoad);
window.addEventListener("htmx:afterSwap", onLoad);
