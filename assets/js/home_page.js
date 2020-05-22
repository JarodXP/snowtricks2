import {CardClicker} from "./CardClicker";
import {LoadButton} from "./LoadButton";
import {TrickGridLister} from "./AJAX/TricksGridLister";
import {ElementRemover} from "./AJAX/ElementRemover";

/*Sets a 'click' listener on the cards to redirect to the corresponding trick page*/
CardClicker.setClickHandler();

//Sets the event handler for the load buttons (starts and stops spinner)
LoadButton.setClickHandler('#section-trick');

//Sets the event handler for the grid commentLister
TrickGridLister.setEventHandler();

//Initializes the EntityRemover object to handle the AJAX removal
ElementRemover.initialize('#section-trick',2,'data-trick-slug','/ajax/remove-trick/');

