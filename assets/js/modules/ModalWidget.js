// Imports
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

        appendXhrContent: function(xhr) {
            let elem = container.instance;

            elem.innerHTML = xhr.responseText;
            modal.clear();
            modal.instance.append(elem);

            // Since form is available on the page
            if(form.instance) {
                listener.setSubmitListener();
                listener.setCancelSubmitListener();
            }
        },

        clear: function() {
            modal.instance.innerHTML = '';
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

        get getLocation() {
            return document.getElementsByClassName(listener.class)[0];
        },

        get postLocation() {
            return form.instance;
        },

        confirmCallFormEvent: function(event) {
            return event.target.getAttribute(trigger.attrName) === trigger.attrValue;
        },

        confirmFullPageInResponse: function(xhr) {
            return xhr.responseText.match("^<!DOCTYPE html>");
        },

        setCallFormListener: function() {
            listener.getLocation.addEventListener('click', listener.listenCallFormEvent);
        },

        listenCallFormEvent: function(event) {

            if(event && listener.confirmCallFormEvent(event)) {
                event.preventDefault();

                // To have path for any form, not only login
                let path = event.target.pathname.trim();
                AjaxSender.sendGet(path, modal.appendXhrContent);
            }
        },

        setSubmitListener: function() {
            listener.postLocation.addEventListener('submit', listener.listenSubmitFormEvent);
        },

        listenSubmitFormEvent: function(event) {
            event.preventDefault();

            let formData = new FormData(form.instance);
            let path = form.actionAttr;
            AjaxSender.sendPost(path, modal.appendXhrContent, formData);
        },

        listenCancelSubmit: function() {
            modal.clear();
            window.history.back();
        },

        setCancelSubmitListener: function() {
            overlay.instance.addEventListener('click', listener.listenCancelSubmit);
        }
    };

    // Modal form
    let form = {
        get instance() {
            if(document.forms) {
                return document.forms[0];
            }
        },

        get actionAttr() {
            return form.instance.getAttribute('action');
        },

        get submitButton() {
            return document.querySelectorAll('button[type="submit"]')[0];
        },
    };

    // Modal overlay
    let overlay = {
        class: 'modal-overlay',

        get instance() {
            return document.getElementsByClassName(overlay.class)[0];
        },
    };

    // Fire Modal events
    let trigger = {
        attrName : 'data-target',
        attrValue: modal.id,
    };


    return {
        listenCallFormEvent: listener.setCallFormListener,
    };
}();


export default ModalWidget;
