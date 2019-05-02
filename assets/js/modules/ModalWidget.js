// Imports
import AjaxSender   from '../../js/modules/AjaxSender';
import Materializer from '../../js/modules/Materializer';

/**
 * Handle Materialize CSS Modal events
 *
 * @module ../../js/modules/ModalWidget
 * @type {{
 *  listenCallModalEvent: (listener.listenCallModalEvent|listenCallModalEvent)
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

            // Delete modal content after overlay click
            if(overlay.instance) {
                listener.setCancelModalListener();

                // Since form is available on the page
                if(form.instance) {
                    Materializer.reInitFormFields();
                    listener.setSubmitListener();
                }
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
        tag  : 'body',

        get getLocation() {
            return document.getElementsByClassName(listener.class)[0];
        },

        get postLocation() {
            return form.instance;
        },

        confirmCallModalEvent: function(event) {
            console.log(event);
            return event.target.getAttribute(trigger.attrName) === trigger.attrValue;
        },

        confirmFullPageInResponse: function(xhr) {
            return xhr.responseText.match("^<!DOCTYPE html>");
        },

        setCallFormListener: function() {
            listener.getLocation.addEventListener('click', listener.listenCallModalEvent);
            listener.getLocation.addEventListener('animationstart', listener.listenReInitComponentsEvent);
        },

        listenCallModalEvent: function(event) {

            if(event && listener.confirmCallModalEvent(event)) {
                event.preventDefault();

                // To have path for any modals
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

        listenCancelModal: function() {
            modal.clear();
            window.history.back();
        },

        setCancelModalListener: function() {
            overlay.instance.addEventListener('click', listener.listenCancelModal);
        },

        listenReInitComponentsEvent: function(event) {
            console.log(event);
            if(event.animationName === 'selectWasInserted') {
                Materializer.reInitFormFields();
            }
        },
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
        listenCallModalEvent: listener.setCallFormListener,
    };
}();


export default ModalWidget;
