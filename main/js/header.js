window.onscroll = function() {
    const isLogoPolyEmpty = document.querySelector('.logo-header')
    stickyMenu(isLogoPolyEmpty);
};
const menuButton = document.querySelector('.menuButton');
const redirectionButton = document.querySelector('a')
const sticky = menuButton.offsetTop;
function stickyMenu(isLogoPolyEmpty) {

    if (window.pageYOffset > sticky && isLogoPolyEmpty == null) {
        var logoPoly = document.createElement("img")
        logoPoly.src ="../../assets/logopoly-header.png";
        logoPoly.className="logo-header";
        logoPoly.height = 48.25;
        menuButton.insertBefore(logoPoly, redirectionButton);
        menuButton.classList.add("sticky"); 
    } else {
        if (window.pageYOffset < sticky) {
            isLogoPolyEmpty.remove();
            menuButton.classList.remove("sticky");
        }
    }
}


