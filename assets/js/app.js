// any CSS you import will output into a single css file (app.scss in this case)
import '../css/app.scss';

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
// require('bootstrap/js/dist/popover');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

const desktopNav = document.getElementById('desktop-nav');
const mobileNav = document.getElementById('mobile-nav');

//Allows to use FontAwesome in Pseudo-elements
window.FontAwesomeConfig = {
    searchPseudoElements: true
};

//Event listener for toggling nav bar
window.addEventListener("resize", toggleNav);
window.onload = toggleNav;

//Toggles type of menu depending on window size
function toggleNav() {
//Toggles the ".hidden" class
    if(document.body.clientWidth < 576){
        if(!desktopNav.classList.contains('hidden')){
            desktopNav.classList.add('hidden');
        }
        if(mobileNav.classList.contains('hidden')){
            mobileNav.classList.remove('hidden')
        }
    }
    else{
        if(!mobileNav.classList.contains('hidden')){
            mobileNav.classList.add('hidden');
        }
        if(desktopNav.classList.contains('hidden')){
            desktopNav.classList.remove('hidden')
        }
    }
}

