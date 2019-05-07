// Imports
import AjaxSender   from '../../js/modules/AjaxSender';
import Logger from "./Logger";

/**
 * Handle sort items events
 *
 * @module ../../js/modules/Sorter
 * @type {{}}
 */
let Sorter = function() {

    let container = {
        class: 'container',

        get elem() {
            console.log('Find Container for sorted items');

            let containerElem = document.getElementsByClassName(container.class)[0];
            if(!containerElem) {
                throw new Error('Container for sorted items not found. Class is wrong');
            }

            return containerElem;
        },
    };


    let eventManager = {
        listeners: {
            class: 'sort-listeners',
            tag  : 'body',

            get elem() {
                console.log('Find Sort listener');
                let listener = document.getElementsByClassName(eventManager.listeners.class)[0];

                if(!listener) {
                    throw new Error('The Sort listener was not found. The class is wrong');
                }

                return listener;
            },
        },

        confirmSortEvent: function(event) {
            Logger.logEvent(event);

            let isSortEvent = event && event.target.href && event.target.href.includes('sorted');
            console.log('Check if it is Sort event? : ' + isSortEvent);
            return isSortEvent;
        },

        setSortListener: function() {
            console.log('Set Sort listener');

            let sortListener = eventManager.listeners.elem;
            sortListener.addEventListener('click', eventManager.requestSortedItems);
        },

        requestSortedItems: function(event) {
            console.log('Request Sorted items');

            if(eventManager.confirmSortEvent(event)) {
                event.preventDefault();

                // To have path for any modals
                let path = event.target.pathname.trim();
                AjaxSender.sendGet(path, appendSortedContent);
            }
        },
    };


    let appendSortedContent = function(xhr) {
        if(!xhr.responseText) {
            throw new Error('XHR is empty');
        }

        console.log('Append Sorted content');
        let template = document.createElement('template');
        template.innerHTML = xhr.responseText;

        let content = document.importNode(template.content, true);
        let parent  = container.elem;
        parent.innerHTML = '';
        parent.append(content);
    };


    return {
        requestSortedItems: eventManager.setSortListener,
    };
}();


export default Sorter;
