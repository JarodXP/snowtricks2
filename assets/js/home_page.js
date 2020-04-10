/*Sets a 'click' listener on the cards to redirect to the corresponding trick page*/
const cards = document.getElementsByClassName('card');

for(let card of cards){
    card.addEventListener('click',function () {

        //Gets the link located in the trick name anchor
        let trickLink = card.children[1].children[0].children[0].getAttribute('href');
        window.location.replace(trickLink);
    })
}