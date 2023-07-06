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

function onLoad() {
    console.log("hello :^)");
    initScrolltoTop();
}

window.addEventListener("DOMContentLoaded", onLoad);
window.addEventListener("load", onLoad);
window.addEventListener("htmx:afterSwap", onLoad);