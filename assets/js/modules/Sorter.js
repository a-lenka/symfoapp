// Imports
import AjaxSender   from '../../js/modules/AjaxSender';
import Materializer from './Materializer';

/**
 * Handle sort items events
 *
 * @module ../../js/modules/Sorter
 * @type {{setSortListeners}}
 */
let Sorter = function() {

    let box = {
        class: 'container',

        get elem() {
            let containerElem = document.getElementsByClassName(box.class)[0];
            if(!containerElem) { console.warn('Container for sorted items was not found'); }
            return containerElem;
        },
    };


    let checker = {
        confirmSortEvent: function(event) {
            return event && event.target.href !== undefined
                && event.target.href.includes('sorted')
                && event.target.parentElement.localName === 'th';
        },
    };


    let eventManager = {

        listeners: {
            class: 'sort-listeners',
            tag  : 'body',

            get elem() {
                let listener = document.getElementsByClassName(eventManager.listeners.class)[0];
                if(!listener) { throw new Error('The Sort listener was not found'); }
                return listener;
            },
        },

        setSortListeners: function() {
            let sortListener = eventManager.listeners.elem;
            sortListener.addEventListener('click', requestSortedItems);
        },
    };


    let requestSortedItems = function(event) {
        if(checker.confirmSortEvent(event)) {
            event.preventDefault();

            let path = event.target.pathname.trim();
            AjaxSender.sendGet(path, appendSortedContent);
        }
    };


    let appendSortedContent = function(xhr) {
        if(!xhr.responseText) {
            throw new Error('XHR is empty');
        }

        box.elem.innerHTML = '';
        box.elem.insertAdjacentHTML('afterbegin', xhr.responseText);
        Materializer.reInitFloatingActions();
    };


    return {
        setSortListeners: eventManager.setSortListeners,
    };
}();


export default Sorter;
