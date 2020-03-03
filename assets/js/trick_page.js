import '../css/front/tricks/trick_page.scss';

const toggleBtn = document.getElementById('media-slider-toggle');
const mediaSection = document.getElementById('media-slider-element');

//Event listeners for media section visibility
window.addEventListener("resize", displayMedia);
window.onload = displayMedia;

//Displays media section depending on the window size
function displayMedia(){
    if(document.body.clientWidth < 576){
        mediaSection.classList.add('hidden');
        toggleBtn.classList.remove('hidden');
    }
    else {
        mediaSection.classList.remove('hidden');
        toggleBtn.classList.add('hidden');
    }
}

//Toggles media-section visibility
toggleBtn.addEventListener('click',function () {
    mediaSection.classList.toggle('hidden');

    if(mediaSection.classList.contains('hidden')){
        toggleBtn.firstElementChild.textContent = 'Voir les medias ';
    }
    else{
        toggleBtn.firstElementChild.textContent = 'Masquer les medias ';
    }
});



