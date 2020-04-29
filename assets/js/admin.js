const $ = require('jquery');

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
        this.trickRow = button.parents().eq(3);
        this.tokenValue = button.parent().find("input[name='remove_token']").attr('value');

        if (!this.httpRequest) {
            alert('Abandonned :( Couldn\'t create XMLHTTP instance');
            return false;
        }

        //Sets the ajax object
        $.ajax({
            url: 'ajax/remove-trick/',
            method: 'POST',
            data: 'remove_token='+encodeURIComponent(this.tokenValue),
            success: function () {
                this.trickRow.remove();
                alert('The trick ' + trickRemover.trickSlug + ' has been removed');
            },
            error: function () {
                alert('A problem has raised: ' + trickRemover.httpRequest.responseText);
            }
        })
    },
};

removeBtns = $(".remove-btn");

for(let button of removeBtns){
    button.on('click', function (e){
        e.preventDefault();
        trickRemover.makeRequest(button, button.getAttribute('data-trick-slug'))
    });
}
