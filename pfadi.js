// Pfadi extension, https://github.com/pfadfinder26/yellow-pfadi
// Based on Datenstrom Yellow, https://datenstrom.se/yellow/
// Gives a row of cards that scrolls sideways a button at each side, and moves a banner with
// several images along and loops it. Without this file a card row is still scrolled by hand and
// the banner stays a carousel that can be swiped and scrolled, it just does not move by itself.
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

    // a row of cards that scrolls sideways gets a button at each side, it stops at both ends
    function setupCardScroll(row) {
        var german = (document.documentElement.lang || "").indexOf("de")===0;
        var scroller = document.createElement("div");
        scroller.className = "cards-scroller";
        row.parentNode.insertBefore(scroller, row);
        scroller.appendChild(row);
        var buttons = [-1, 1].map(function (direction) {
            var button = document.createElement("button");
            button.type = "button";
            button.className = "cards-button cards-button-"+(direction<0 ? "previous" : "next");
            button.setAttribute("aria-label", direction<0 ?
                (german ? "Zurück" : "Previous") : (german ? "Weiter" : "Next"));
            button.addEventListener("click", function () {
                var card = row.firstElementChild;
                var step = card ? card.getBoundingClientRect().width+24 : row.clientWidth;
                row.scrollBy({left: direction*step, behavior: "smooth"});
            });
            scroller.appendChild(button);
            return button;
        });
        // cards that wait for their date stand in front, the row starts at today
        var today = row.querySelector(".card:not(.entry-scheduled)");
        if (today && today!==row.firstElementChild) {
            var behavior = row.style.scrollBehavior;
            row.style.scrollBehavior = "auto";
            row.scrollLeft = today.offsetLeft-row.offsetLeft;
            row.style.scrollBehavior = behavior;
        }
        function update() {
            var scrollable = row.scrollWidth-row.clientWidth;
            scroller.classList.toggle("cards-scroller-idle", scrollable<=2);
            buttons[0].disabled = row.scrollLeft<=2;
            buttons[1].disabled = row.scrollLeft>=scrollable-2;
        }
        row.addEventListener("scroll", update);
        window.addEventListener("resize", update);
        update();
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".cards-scroll").forEach(setupCardScroll);
    });
    document.addEventListener("DOMContentLoaded", function () {
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
        document.querySelectorAll(".banner-carousel").forEach(setupCarousel);
    });
})();
