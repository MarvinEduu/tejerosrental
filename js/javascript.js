var lastScrollTop = 0;
var delta = 5;
var navbarHeight = document.getElementById('stickyHeader').offsetHeight;
var didScroll;

window.addEventListener("scroll", function() {
    didScroll = true;
});

setInterval(function() {
    if (didScroll) {
        hasScrolled();
        didScroll = false;
    }
}, 250);

function hasScrolled() {
    var st = window.scrollY;

    // Make sure they scroll more than delta
    if (Math.abs(lastScrollTop - st) <= delta)
        return;

    // If current scroll position is greater than last scroll position and greater than navbar height, show navbar
    if (st > lastScrollTop && st > navbarHeight) {
        // Scroll Down
        document.getElementById('stickyHeader').style.transition = "top 0.3s ease-out";
        document.getElementById('stickyHeader').style.top = "-" + navbarHeight + "px";
    } else {
        // Scroll Up
        if (st + window.innerHeight < document.body.clientHeight) {
            document.getElementById('stickyHeader').style.transition = "top 0.3s ease-in";
            document.getElementById('stickyHeader').style.top = "0";
        }
    }

    lastScrollTop = st;
}

