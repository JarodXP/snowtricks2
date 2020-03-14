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

//Allows to use FontAwesome in Pseudo-elements
window.FontAwesomeConfig = {
    searchPseudoElements: true
};

const navElement = {

    //Toggles type of menu depending on window size
    toggleNav() {
        const desktopNav = document.getElementById('desktop-nav');
        const mobileNav = document.getElementById('mobile-nav');

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
    },

    //Toggles connection sub-menu on click
    toggleConnectionSubMenu() {

        const connectionMenuElement = document.getElementById('connection-menu');

        connectionMenuElement.classList.toggle('hidden');

        if(document.body.clientWidth < 576){
            connectionMenuElement.style.bottom = - parseInt(getComputedStyle(connectionMenuElement).bottom) + "px";
        }
        else {
            connectionMenuElement.style.width =
                document.querySelector('#desktop-nav .connection-block').offsetWidth + "px";
            connectionMenuElement.style.left =
                document.querySelector('#desktop-nav .connection-block').offsetLeft + "px";

            connectionMenuElement.style.top = - parseInt(getComputedStyle(connectionMenuElement).top) + "px";
        }
    },
};

//Event listener for toggling nav bar
window.addEventListener("resize", navElement.toggleNav);
window.onload = navElement.toggleNav;

//Event listener for toggling connection sub-menu
const connectionBlocks = document.getElementsByClassName('connection-block');

for(let connection of connectionBlocks){
    connection.addEventListener("click",navElement.toggleConnectionSubMenu);
}