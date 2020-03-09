/*Toggle button on admin tables*/
const toggleBtnArray = document.querySelectorAll('.jxp-admin-toggle>input');

for (const toggleBtn of toggleBtnArray){
    toggleBtn.addEventListener('change',registerStatusToggle);
}

function registerStatusToggle(e) {
    //Gets the slug of the related trick
    let relatedTrick = e.target.getAttribute('data-slug');

    //Sends a request for changing the status
    window.location = "/tricks/" + relatedTrick;
}