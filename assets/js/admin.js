import {TrickRemover} from './AJAX/TrickRemover';
import {StatusToggler} from './AJAX/StatusToggler';

//Sets the event handler for the TrickRemover and the <tr> (parents[2]) as parent wrapper
TrickRemover.setEventHandler('tr',1);

//Sets the event handler for the StatusToggler
StatusToggler.setEventHandler(true);