// Imports

import * as M from 'materialize-css';

/**
 * Handle flash messages from Symfony Controllers
 *
 * @module ../../js/modules/Flash
 * @type {{send}}
 */
let Flasher = function() {

    let messages = {
        get elems() {
            return document.getElementsByClassName('flash-container');
        }
    };


    let send = function() {
        if(messages.elems.length > 0) {
            for(let i = 0; i < messages.elems.length; i++) {
                M.toast({
                    html: messages.elems[i].innerHTML,
                    classes: messages.elems[i].classList[1]
                });
            }
        }
    };


    return {
        send: send,
    };
}();


export default Flasher;
