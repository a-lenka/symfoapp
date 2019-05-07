// Imports
import AjaxSender   from '../../js/modules/AjaxSender';
import Materializer from '../../js/modules/Materializer';
import Logger       from '../../js/modules/Logger';

/**
 * Handle Materialize CSS Modal events
 *
 * @module ../../js/modules/ModalWidget
 * @type {{listenCallModalEvent}}
 */
let ModalWidget = function() {

    let modal = {
        id: 'materialize-modal',

        get elem() {
            let modalElem = document.getElementById(modal.id);
            if(!modalElem) {
                throw new Error('Modal not found. ID is wrong');
            }

            console.log('Find Modal elem');
            return modalElem;
        },

        clearSelf: function() {
            console.log('Clear Modal');
            modal.elem.innerHTML = '';
        }
    };


    let container = {
        tag  : 'div',
        class: 'modal-content',

        get elem() {
            let div = document.createElement(container.tag);
            div.classList.add(container.class);

            console.log('Create Modal container');
            return div;
        }
    };


    let form = {
        get elem() {
            let form = document.forms[0];
            if(!form) {
                throw new Error('The form must be here, but it\'s not found');
            }
            if(document.forms.length > 1) {
                console.warn('Here must be only one form, but there are more');
            }

            console.log('Find form');
            return document.forms[0];
        },

        get actionAttr() {
            let formAction = form.elem.getAttribute('action');
            if(!formAction) {
                throw new Error('The form `action` attribute` is empty');
            }

            console.log('Get form action attribute');
            return formAction;
        },

        get submitButton() {
            let submitButton = document.querySelectorAll('button[type="submit"]')[0];
            if(!submitButton) {
                throw new Error('The submit button was not found');
            }

            console.log('Get submit button');
            return submitButton;
        },

        reInitComponents: function(event) {
            Logger.logEvent(event);
            let isFormAppended = event.animationName === 'selectWasInserted';
            console.log('Check if the form was appended? : ' + isFormAppended);

            if(isFormAppended) {
                console.log('ReInit form components');
                Materializer.reInitFormFields();
            }
        },
    };


    let overlay = {
        class: 'modal-overlay',

        get elem() {
            let overlayElem = document.getElementsByClassName(overlay.class)[0];
            if(!overlayElem) {
                throw new Error('The overlay must be here, but it\'s not found');
            }

            console.log('Find overlay');
            return overlayElem;
        },
    };


    let eventManager = {
        triggers: {
            form: {
                attrName : 'data-target',
                attrValue: modal.id,
            },
        },

        listeners: {
            requestForm: {
                class: 'modal-listeners',

                get elem() {
                    let listener = document.getElementsByClassName(eventManager.listeners.requestForm.class)[0];
                    if(!listener) {
                        throw new Error('The Request form listener was not found. The class is wrong');
                    }

                    console.log('Find Request form listener');
                    return listener;
                },
            },

            submitForm: {
                get elem() {
                    console.log('Find Submit form listener');
                    return form.elem;
                },
            },
        },

        confirmRequestModalEvent: function(event) {
            let attrName  = eventManager.triggers.form.attrName;
            let attrValue = eventManager.triggers.form.attrValue;

            Logger.logEvent(event);

            let isRequestModalEvent = event && event.target.getAttribute(attrName) === attrValue;
            console.log('Check if it is Request Modal event? : ' + isRequestModalEvent);
            return isRequestModalEvent;
        },

        confirmFullPageInResponse: function(xhr) {
            let isFullPageInResponse = xhr.responseText.match("^<!DOCTYPE html>");

            console.log('Check if it is full page in Response? : ' + isFullPageInResponse);
            return isFullPageInResponse;
        },

        setRequestFormListeners: function() {
            console.log('Set Request form listeners');
            let listener = eventManager.listeners.requestForm.elem;

            listener.addEventListener('click', eventManager.requestFormContent);
            listener.addEventListener('animationstart', form.reInitComponents);
        },

        requestFormContent: function(event) {
            console.log('Request Form content');

            if(eventManager.confirmRequestModalEvent(event)) {
                event.preventDefault();

                // To have path for any modals
                let path = event.target.pathname.trim();
                AjaxSender.sendGet(path, appendFormContent);
            }
        },

        setSubmitFormListeners: function() {
            console.log('Set Submit form listeners');

            let listener = eventManager.listeners.submitForm.elem;
            listener.addEventListener('submit', eventManager.submitFormContent);
        },

        submitFormContent: function(event) {
            event.preventDefault();

            let formData = new FormData(form.elem);
            if(!formData) {
                throw new Error('The Form Data is empty');
            }

            let path = form.actionAttr;
            console.log('Submit form');
            AjaxSender.sendPost(path, appendFormContent, formData);
        },

        setCancelModalListener: function() {
            console.log('Set Cancel Modal listeners');
            overlay.elem.addEventListener('click', cancelModal);
        },
    };


    let appendFormContent = function(xhr) {
        if(!xhr.responseText) {
            throw new Error('XHR is empty');
        }

        let elem       = container.elem;
        elem.innerHTML = xhr.responseText;

        console.log('Append content to Modal');
        modal.clearSelf();
        modal.elem.append(elem);

        console.log('Check Overlay is here');
        if(overlay.elem) {
            eventManager.setCancelModalListener();

            console.log('Check Form is here');
            if(form.elem) {
                Materializer.reInitFormFields();
                eventManager.setSubmitFormListeners();
            }
        }
    };


    let cancelModal = function() {
        console.log('Cancel Modal');

        modal.clearSelf();
        window.history.back();
    };


    return {
        requestFormContent: eventManager.setRequestFormListeners,
    };
}();


export default ModalWidget;
