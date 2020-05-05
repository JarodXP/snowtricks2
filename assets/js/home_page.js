import {CardClicker} from "./CardClicker";
import {TrickLister} from "./AJAX/TricksGridLister";
import {TrickRemover} from "./AJAX/TrickRemover";

/*Sets a 'click' listener on the cards to redirect to the corresponding trick page*/
CardClicker.setClickHandler();

//Sets the event handler for the grid lister
TrickLister.setEventHandler();

//Sets the event handler for the trick remover and the <card> (parents[3]) as parent wrapper
TrickRemover.setEventHandler('#section-trick',2);

