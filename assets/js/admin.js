import {trickRemover} from './AJAX/_trickRemover';
import {statusToggler} from './AJAX/_statusToggler';

//Sets the event handler for the TrickRemover and the <tr> (parents[2]) as parent wrapper
trickRemover.setEventHandler('tr',1);

//Sets the event handler for the StatusToggler
statusToggler.setEventHandler(true);