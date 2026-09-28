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
        setLabel("yellow-pane-create-bar", rail.getAttribute("data-label-create"));
        setLabel("yellow-pane-delete-bar", rail.getAttribute("data-label-delete"));
        if (!window.yellow || !window.yellow.edit) return;
        keepPaneOpen();
        bindTools(rail);
        processAction(window.location.hash.indexOf("#pfadi-")===0 ?
            window.location.hash.substring(7) : "", true);
    }

    // the bar says "+" and "-", in the rail the buttons say what they do
    function setLabel(id, text) {
        var element = document.getElementById(id);
        if (element && text) element.textContent = text;
    }

    // clicking next to the window that edits a page should not throw the text away
    function keepPaneOpen() {
        var edit = window.yellow.edit;
        var click = edit.click;
        edit.click = function (e) {
            var modal = this.paneId=="yellow-pane-edit" || this.paneId=="yellow-pane-create" ||
                this.paneId=="yellow-pane-delete";
            if (!modal) return click.call(this, e);
            if (this.popupId && !document.getElementById(this.popupId).contains(e.target)) {
                this.hidePopup(this.popupId, true);
            }
        };
    }

    // a button for the page that is open does not lead anywhere, it acts right away
    function bindTools(rail) {
        rail.querySelectorAll(".editrail-tool").forEach(function (tool) {
            tool.addEventListener("click", function (e) {
                var url = new URL(tool.href, window.location.href);
                if (url.pathname!=window.location.pathname) return;
                e.preventDefault();
                processAction(url.hash.substring(7), false);
            });
        });
    }

    // a button of the page tree asks for this, in the fragment or right here
    function processAction(action, fromHash) {
        if (!action) return;
        if (fromHash) window.history.replaceState(null, "", window.location.pathname);
        if (action=="status") {
            toggleStatus();
        } else if (action=="edit" || action=="create" || action=="delete") {
            window.yellow.edit.processAction(action, "none");
        }
    }

    // shows or hides a page, the same way the edit extension saves a page
    function toggleStatus() {
        var page = window.yellow.page;
        var raw = page.rawDataSource;
        if (!raw) return;
        var lines = raw.split(/\r?\n/);
        var end = 0;
        for (var i = 1; i<lines.length; i++) {
            if (lines[i].trim()=="---") { end = i; break; }
        }
        if (lines[0].trim()!="---" || !end) return;
        var found = -1;
        for (var j = 1; j<end; j++) {
            if (/^status\s*:/i.test(lines[j])) found = j;
        }
        if (found!==-1) {
            lines.splice(found, 1);
        } else {
            lines.splice(end, 0, "Status: unlisted");
        }
        window.yellow.toolbox.submitForm({
            "action": "edit",
            "yellowcsrftoken": window.yellow.edit.getCookie("yellowcsrftoken"),
            "rawdatasource": raw,
            "rawdataedit": lines.join(page.rawDataEndOfLine=="crlf" ? "\r\n" : "\n"),
            "rawdataendofline": page.rawDataEndOfLine
        });
    }

    document.addEventListener("DOMContentLoaded", setupEditRailToggle);
    document.addEventListener("DOMContentLoaded", function () {
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
        document.querySelectorAll(".banner-carousel").forEach(setupCarousel);
    });
    window.addEventListener("load", setupEditRail);
})();
