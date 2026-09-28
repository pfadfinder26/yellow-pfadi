// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/
// Moves the editing buttons into the rail at the side of the window, and moves a banner with several images along and loops it. Without this file the
// banner stays a carousel that can be swiped and scrolled, it just does not move by itself.
(function () {
    "use strict";
    var interval = 6000;

    function setupCarousel(banner) {
        var slides = banner.children.length;
        if (slides<2) return;
        var clone = banner.firstElementChild.cloneNode(true);
        clone.setAttribute("aria-hidden", "true");
        banner.appendChild(clone);
        var paused = false;
        ["pointerenter", "focusin", "touchstart"].forEach(function (name) {
            banner.addEventListener(name, function () { paused = true; });
        });
        ["pointerleave", "focusout"].forEach(function (name) {
            banner.addEventListener(name, function () { paused = false; });
        });
        function jump(position) {
            var behavior = banner.style.scrollBehavior;
            banner.style.scrollBehavior = "auto";
            banner.scrollLeft = position;
            banner.offsetWidth;
            banner.style.scrollBehavior = behavior;
        }
        function loop() {
            if (banner.clientWidth>0 && Math.round(banner.scrollLeft/banner.clientWidth)>=slides) jump(0);
        }
        if ("onscrollend" in window) {
            banner.addEventListener("scrollend", loop);
        } else {
            var timer;
            banner.addEventListener("scroll", function () {
                window.clearTimeout(timer);
                timer = window.setTimeout(loop, 200);
            });
        }
        function advance() {
            if (paused || document.hidden || banner.clientWidth===0) return;
            var width = banner.clientWidth;
            var current = Math.round(banner.scrollLeft/width);
            banner.scrollTo({left: (current+1)*width, behavior: "smooth"});
        }
        window.setInterval(advance, interval);
    }

    // the rail stays open or closed the way the editor left it
    function setupEditRailToggle() {
        var toggle = document.getElementById("editrail-toggle");
        if (!toggle) return;
        try {
            toggle.checked = window.localStorage.getItem("pfadi-editrail")=="open";
        } catch (e) {}
        toggle.addEventListener("change", function () {
            try {
                window.localStorage.setItem("pfadi-editrail", toggle.checked ? "open" : "closed");
            } catch (e) {}
        });
    }

    // the edit extension builds its bar at the top of the page, it goes into the rail
    function setupEditRail() {
        var rail = document.getElementById("editrail");
        var bar = document.getElementById("yellow-bar");
        if (!rail || !bar) return;
        rail.querySelector(".editrail-actions").appendChild(bar);
        if (window.yellow && window.yellow.edit) window.yellow.edit.bindActions(rail);
    }

    document.addEventListener("DOMContentLoaded", setupEditRailToggle);
    document.addEventListener("DOMContentLoaded", function () {
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
        document.querySelectorAll(".banner-carousel").forEach(setupCarousel);
    });
    window.addEventListener("load", setupEditRail);
})();
