// Imports
import AjaxSender   from '../../js/modules/AjaxSender';
import Materializer from '../../js/modules/Materializer';
import Logger       from '../../js/modules/Logger';

/**
 * Handle Materialize CSS Modal events
 *
 * @module ../../js/modules/ModalWidget
 * @type {{setRequestListeners}}
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
        },
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

            if(checker.confirmFormIsAppended(event)) {
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
                console.debug('The overlay must be here, but it\'s not found');
            }

            console.log('Find overlay');
            return overlayElem;
        },
    };


    let checker = {
        confirmRequestModalEvent: function(event) {
            let attrName  = eventManager.triggers.form.attrName;
            let attrValue = eventManager.triggers.form.attrValue;

            let hasModalAttributes  = event.target.getAttribute(attrName) === attrValue;
            let hasClassBtnFloating = event.target.outerHTML.indexOf('red') === -1;

            let isRequestModalEvent = event && hasModalAttributes && hasClassBtnFloating;

            Logger.logEvent(event);
            console.log('Check if it is Request Modal event? : ' + isRequestModalEvent);
            return isRequestModalEvent;
        },

        confirmFullPageInResponse: function(xhr) {
            let isFullPageInResponse = xhr.responseText.match("^<!DOCTYPE html>");

            console.log('Check if it is full page in Response? : ' + isFullPageInResponse);
            return isFullPageInResponse;
        },

        confirmModalForForm: function(xhr) {
            let path        = xhr.getResponseHeader('X-Target-URL');
            let isFormModal = path.indexOf('details') === 1
                || path.indexOf('confirm') === 1;

            console.log('Check if it is Form Modal? : ' + isFormModal);
            return isFormModal;
        },

        confirmFormIsAppended: function(event) {
            let isFormAppended = event.animationName === 'selectWasInserted';
            console.log('Check if the form was appended? : ' + isFormAppended);
            return isFormAppended;
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

        setRequestFormListeners: function() {
            console.log('Set Request form listeners');
            let listener = eventManager.listeners.requestForm.elem;

            listener.addEventListener('click', requestFormContent);
            listener.addEventListener('animationstart', form.reInitComponents);
        },

        setSubmitFormListeners: function() {
            console.log('Set Submit form listeners');

            let listener = eventManager.listeners.submitForm.elem;
            listener.addEventListener('submit', submitFormContent);
        },

        setCancelModalListener: function() {
            console.log('Set Cancel Modal listeners');
            overlay.elem.addEventListener('click', cancelModal);
        },
    };


    let requestFormContent = function(event) {
        console.log('Request Form content');

        if(checker.confirmRequestModalEvent(event)) {
            event.preventDefault();

            // To have path for any modals
            let path = event.target.pathname.trim();
            AjaxSender.sendGet(path, appendFormContent);
        }
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

            // We should not set submit listeners
            // If here is details or confirm modals
            if (form.elem && !checker.confirmModalForForm(xhr)) {
                Materializer.reInitFormFields();
                eventManager.setSubmitFormListeners();
            }
        }
    };


    let submitFormContent = function(event) {
        event.preventDefault();

        let formData = new FormData(form.elem);
        if(!formData) {
            throw new Error('The Form Data is empty');
        }

        let path = form.actionAttr;
        console.log('Submit form');
        AjaxSender.sendPost(path, appendFormContent, formData);
    };


    let cancelModal = function() {
        console.log('Cancel Modal');

        modal.clearSelf();
        window.history.back();
    };


    return {
        setRequestListeners: eventManager.setRequestFormListeners,
        setCancelModalListener: eventManager.setCancelModalListener,
        appendFormContent: appendFormContent,
    };
}();


export default ModalWidget;
