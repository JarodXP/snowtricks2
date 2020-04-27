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

const trickRemover = {
    httpRequest: new XMLHttpRequest(),
    trickSlug:'',
    trickRow:'',
    tokenValue:'',

    makeRequest(button, trickSlug){
        this.trickSlug = trickSlug;
        this.trickRow = button.parentNode.parentNode.parentNode;
        this.tokenValue = button.parentNode.firstElementChild.getAttribute('value');

        if (!this.httpRequest) {
            alert('Abandonned :( Couldn\'t create XMLHTTP instance');
            return false;
        }

        this.httpRequest.onreadystatechange = this.processContent;
        this.httpRequest.open('POST', 'https://127.0.0.1:8000/ajax/remove-trick/' + trickSlug);
        this.httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        this.httpRequest.send("remove_token="+encodeURIComponent(this.tokenValue));
    },

    processContent() {
        if (trickRemover.httpRequest.readyState === XMLHttpRequest.DONE) {
            if (trickRemover.httpRequest.status === 200) {
                trickRemover.removeTrickOnClient();
            } else {
                alert('A problem has raised: ' + trickRemover.httpRequest.responseText);
            }
        }
    },

    removeTrickOnClient(){
        trickRemover.trickRow.remove();
        alert('The trick ' + trickRemover.trickSlug + ' has been removed');
    }
};

removeBtns = document.getElementsByClassName("remove-btn");

for(let button of removeBtns){
    button.addEventListener('click', function (e){
        e.preventDefault();
        trickRemover.makeRequest(button, button.getAttribute('data-trick-name'))
    })
}
