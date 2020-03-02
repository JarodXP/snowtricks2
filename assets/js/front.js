const desktopNav = document.getElementById('desktop-front-nav');
const mobileNav = document.getElementById('mobile-front-nav');

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