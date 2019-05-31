// Imports
import * as M from 'materialize-css';
import AjaxSender from '../utils/AjaxSender';
import Sorter     from './Sorter';
import TableList  from './TableList';
import Helper     from '../../js/utils/Helper';

/**
 * Handle search events
 *
 * @module ../../js/modules/Searcher
 * @type {{setSearchListeners}}
 */
let Searcher = function() {

    let box = {
        class: 'search-field',

        get elem() {
            let boxElem = document.getElementsByClassName(box.class)[0];
            if(!boxElem) { console.warn('Container for search form was not found'); }
            return boxElem;
        },
    };

    let searchInput = {
        selector: 'input',

        get elem() {
            return box.elem.querySelector(searchInput.selector);
        },
    };

    let eventManager = {

        triggers: {
            clearIcon: {
                selector: 'div.search-field > i',

                get elem() {
                    return document.querySelector(eventManager.triggers.clearIcon.selector);
                }
            },

            searchIcon: {
                selector: 'div.search-field > label',

                get elem() {
                    return document.querySelector(eventManager.triggers.searchIcon.selector);
                }
            },

            switchPerformedTasksButton: {
                className: 'btn-floating indigo darken-1',
                selector : 'a.btn-floating.indigo.darken-1',

                get elem() {
                    return document.querySelector(eventManager.triggers.switchPerformedTasksButton.selector);
                },

                get icon() {
                    return eventManager.triggers.switchPerformedTasksButton.elem.children[0];
                },
            },
        },

        setSearchListeners: function() {
            if(box.elem) {
                eventManager.triggers.clearIcon.elem.addEventListener('click', clearText);
                eventManager.triggers.searchIcon.elem.addEventListener('click', searchItems);
            }
        },
    };


    let clearText = function(event) {
        searchInput.elem.value = '';
    };


    let searchItems = function(event) {
        let path = `http://symfoapp/${Helper.getCurrentLocale()}/task/list/search/empty_request`;

        if(searchInput.elem.value.length > 1) {
            path = `http://symfoapp/${Helper.getCurrentLocale()}/task/list/search/${searchInput.elem.value}`;
        }

        AjaxSender.sendGet(path, function(xhr) {
            // Close sidenav
            let sidenav  = document.querySelector('.sidenav');
            let instance = M.Sidenav.getInstance(sidenav);
            instance.close();

            // Insert Table with search result
            Sorter.appendSortedContent(xhr);

            // Show performed tasks
            TableList.table.showPerformedTasks();

            // Switch icon to 'show performed tasks'
            let icon = eventManager.triggers.switchPerformedTasksButton.icon;
            icon.innerText = 'radio_button_checked'
        });
    };


    return {
        setSearchListeners: eventManager.setSearchListeners,
    };
}();


export default Searcher;
