// Imports
import Logger     from '../../js/modules/Logger';
import AjaxSender from '../../js/modules/AjaxSender';

/**
 * Handle Materialize CSS Modal events
 *
 * @module ../../js/modules/ModalWidget
 * @type {{
 *  listenCallFormEvent: (listener.listenCallFormEvent|listenCallFormEvent)
 * }}
 */
let ModalWidget = function() {

    // Materialize Modal element
    let modal = {
        id: 'materialize-modal',

        get instance() {
            return document.getElementById(modal.id);
        },

        init: function(xhr) {
            let elem = container.instance;
            elem.innerHTML = xhr.responseText;
            modal.instance.append(elem);
        }
    };

    // Modal Container into which content will be inserted
    let container = {
        tag  : 'div',
        class: 'modal-content',

        get instance() {
            let instance = document.createElement(container.tag);
            instance.classList.add(container.class);
            return instance;
        }
    };

    // Listens Modal Events
    let listener = {
        class: 'modal-listeners',
        tag  : 'nav',

        get location() {
            return document.getElementsByClassName(listener.class)[0];
        },

        confirmEvent: function(event) {
            return event.target.getAttribute(trigger.attrName) === trigger.attrValue;
        },

        listenCallFormEvent: function(event) {

            if(event && listener.confirmEvent(event)) {
                event.preventDefault();

                let path = event.target.pathname.trim();
                AjaxSender.sendGet(path, modal.init);
            }

            listener.location.addEventListener('click', listener.listenCallFormEvent);
        },
    };

    // Fire Modal events
    let trigger = {
        attrName : 'data-target',
        attrValue: modal.id,
    };


    return {
        listenCallFormEvent: listener.listenCallFormEvent,
    };
}();


export default ModalWidget;
