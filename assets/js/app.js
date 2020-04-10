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
        const desktopNav = document.getElementsByClassName('desktop-nav')[0];
        const mobileNav = document.getElementsByClassName('mobile-nav')[0];

        //Toggles the ".collapse" class
        if(document.body.clientWidth < 576){
            if(!desktopNav.classList.contains('collapse')){
                desktopNav.classList.add('collapse');
            }
            if(mobileNav.classList.contains('collapse')){
                mobileNav.classList.remove('collapse')
            }
        }
        else{
            if(!mobileNav.classList.contains('collapse')){
                mobileNav.classList.add('collapse');
            }
            if(desktopNav.classList.contains('collapse')){
                desktopNav.classList.remove('collapse')
            }
        }
    }
};

//Event listener for toggling nav bar
window.addEventListener("resize", navElement.toggleNav);
window.onload = navElement.toggleNav;

//Event listener for toggling connection sub-menu
const connectionBlocks = document.getElementsByClassName('connection-block');

for(let connection of connectionBlocks){
    connection.addEventListener("click",navElement.toggleConnectionSubMenu);
}

//Event listener for load buttons
const loadBtns = document.getElementsByClassName('load-btn');

for(let button of loadBtns){
    button.addEventListener('click',function () {
        let spinner = button.children[0];

        button.setAttribute('disabled',"");
        spinner.classList.toggle('collapse');
    })
}