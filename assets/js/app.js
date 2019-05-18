/**
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS / SCSS file) in your base layout (`base.html.twig`).
 */

// CSS imports
// Any SCSS you require will output
// into a single scss file (`app.scss` in this case)
require('../scss/app.scss');

// JS imports
require('../../node_modules/materialize-css/dist/js/materialize.min.js');

// Need jQuery?
// Install it with `yarn add jquery`,
// then uncomment to require it.
// const $ = require('jquery');

// Modules
import Materializer from '../js/modules/Materializer.js';
import ModalWidget  from '../js/modules/ModalWidget.js';
import Sorter       from '../js/modules/Sorter.js';
import Multiplier   from '../js/modules/Multiplier.js';
import Flasher      from '../js/modules/Flasher.js';

// Events
document.addEventListener('DOMContentLoaded', function() {
    Materializer.initJS();
    ModalWidget.setRequestListeners();
    Sorter.setSortListeners();
    Multiplier.setMultiplyListeners();
    Flasher.send();
});

// Allow using history navigation in browser
window.onpopstate = function(event) {
    //Logger.logEvent(event);
    //window.location.reload(true);
};
