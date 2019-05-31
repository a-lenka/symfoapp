// Imports
import AjaxSender   from '../utils/AjaxSender';
import Materializer from '../../js/modules/Materializer';

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
            if(!modalElem) { throw new Error('Modal not found'); }

            return modalElem;
        },

        clearSelf: function() {
            modal.elem.innerHTML = '';
        },
    };


    let box = {
        tag  : 'div',
        class: 'modal-content',

        get elem() {
            let div = document.createElement(box.tag);
            div.classList.add(box.class);
            return div;
        }
    };


    let form = {
        get elem() {
            let forms = Array.from(document.forms);
            return forms.find(f => f.parentElement.className.includes('modal-content'));
        },

        get actionAttr() {
            let formAction = form.elem.getAttribute('action');
            if(!formAction) { throw new Error('The form `action` attribute` is empty'); }
            return formAction;
        },

        get submitButton() {
            let submitButton = document.querySelectorAll('button[type="submit"]')[0];
            if(!submitButton) { throw new Error('The submit button was not found'); }
            return submitButton;
        },

        reInitFormFields: function(event) {
            if(checker.confirmFormIsAppended(event)) {
                Materializer.reInitFormFields();
            }
        },
    };


    let overlay = {
        class: 'modal-overlay',

        get elem() {
            let overlayElem = document.getElementsByClassName(overlay.class)[0];
            if(!overlayElem) { console.log('Overlay is not here'); }
            return overlayElem;
        },
    };


    let checker = {
        confirmRequestModalEvent: function(event) {
            let attrName  = eventManager.triggers.form.attrName;
            let attrValue = eventManager.triggers.form.attrValue;

            let hasModalAttributes    = event.target.getAttribute(attrName)   === attrValue;
            let hasNoClassBtnFloating = event.target.outerHTML.indexOf('red') === -1;

            return event && hasModalAttributes && hasNoClassBtnFloating;
        },

        confirmFullPageInResponse: function(xhr) {
            return xhr.responseText.match("^<!DOCTYPE html>");
        },

        confirmModalForForm: function(xhr) {
            let path = xhr.getResponseHeader('X-Target-URL');
            return path.indexOf('details') !== -1
                || path.indexOf('confirm') !== -1;
        },

        confirmFormIsAppended: function(event) {
            return event.animationName === 'selectWasInserted';
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
                    if(!listener) { throw new Error('The Request form listener was not found'); }
                    return listener;
                },
            },

            submitForm: {
                get elem() {
                    return form.elem;
                },
            },
        },

        setRequestFormListeners: function() {
            let listener = eventManager.listeners.requestForm.elem;

            listener.addEventListener('click', requestFormContent);
            listener.addEventListener('animationstart', form.reInitFormFields);
        },

        setSubmitFormListeners: function() {
            let listener = eventManager.listeners.submitForm.elem;
            listener.addEventListener('submit', submitFormContent);
        },

        setCancelModalListener: function() {
            overlay.elem.addEventListener('click', cancelModal);
        },
    };


    let requestFormContent = function(event) {

        if(checker.confirmRequestModalEvent(event)) {
            event.preventDefault();

            // To have path for any modals
            let path = event.target.pathname.trim();
            AjaxSender.sendGet(path, appendFormContent);
        }
    };


    let appendFormContent = function(xhr) {
        if(!xhr.responseText) { throw new Error('XHR is empty'); }

        // `box.elem` doesn't work here` only through the variable
        let boxElem = box.elem;
        boxElem.innerHTML = xhr.responseText;

        modal.clearSelf();
        modal.elem.append(boxElem);

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
        if(!formData) { throw new Error('The Form Data is empty'); }

        let path = form.actionAttr;
        AjaxSender.sendPost(path, appendFormContent, formData);
    };


    let cancelModal = function() {
        modal.clearSelf();
        window.history.back();
    };


    return {
        setRequestListeners   : eventManager.setRequestFormListeners,
        setCancelModalListener: eventManager.setCancelModalListener,
        // Used in Multiplier
        appendFormContent     : appendFormContent,
    };
}();


export default ModalWidget;
