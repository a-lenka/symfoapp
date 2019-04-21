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

// Events
document.addEventListener('DOMContentLoaded', function() {
    Materializer.initComponents();
    ModalWidget.listenCallFormEvent();
});

// Allow using history navigation in browser
window.onpopstate = function(event) {
    window.location.reload(true);
};
