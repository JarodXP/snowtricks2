import {ElementRemover} from './AJAX/ElementRemover';
import {StatusToggler} from './AJAX/StatusToggler';

//Initializes the EntityRemover object to handle the AJAX removal
ElementRemover.initialize('tr',1,'data-trick-slug','/ajax/remove-trick/');


//Sets the event handler for the StatusToggler
StatusToggler.setEventHandler(true);

