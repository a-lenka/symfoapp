// Imports
import AjaxSender   from '../../js/modules/AjaxSender';

/**
 * Handle sort items events
 *
 * @module ../../js/modules/Sorter
 * @type {{}}
 */
let Sorter = function() {

    let container = {
        class: 'container',

        get instance() {
            return document.getElementsByClassName(container.class)[0];
        },

        appendXhrContent: function(xhr) {
            let template = document.createElement('template');
            template.innerHTML = xhr.responseText;

            let clone = document.importNode(template.content, true);
            container.instance.innerHTML = '';
            container.instance.append(clone);
        }
    };

    // Listens Modal Events
    let listener = {
        class: 'sort-listeners',
        tag  : 'body',

        get location() {
            return document.getElementsByClassName(listener.class)[0];
        },

        confirmSortEvent: function(event) {
            if(event.target.href) {
                return event.target.href.includes('sorted');
            }
        },

        setSortListener: function() {
            listener.location.addEventListener('click', listener.listenSortEvent);
        },

        listenSortEvent: function(event) {

            if(event && listener.confirmSortEvent(event)) {
                event.preventDefault();

                // To have path for any modals
                let path = event.target.pathname.trim();
                AjaxSender.sendGet(path, container.appendXhrContent);
            }
        },
    };


    return {
        listenSortEvent: listener.setSortListener,
    };
}();


export default Sorter;
