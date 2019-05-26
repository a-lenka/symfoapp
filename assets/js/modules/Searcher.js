// Imports
import * as M from 'materialize-css';
import AjaxSender   from '../../js/modules/AjaxSender';
import Sorter       from './Sorter';

/**
 * Handle search events
 *
 * @module ../../js/modules/Searcher
 * @type {{setSearchListeners}}
 */
let Searcher = function() {

    let helper = {
        getCurrentLocale: function() {
            return document.getElementsByTagName('html')[0].getAttribute('lang');
        },
    };

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

    let table = {
        class: 'table.responsive-table.highlight',

        get elem() {
            let tableElem = document.querySelectorAll(table.class)[0];

            if(tableElem) {
                return tableElem;
            }
        },

        get rows() {
            let elem = table.elem;
            let tbodyRows = elem.children[1].rows;

            if(!tbodyRows) {
                throw new Error('There are no rows in the table');
            }

            return tbodyRows;
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
                    return document.querySelectorAll(eventManager.triggers.switchPerformedTasksButton.selector)[0];
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
        searchInput.value = '';
    };


    let searchItems = function(event) {
        let path = `http://symfoapp/${helper.getCurrentLocale()}/task/list/search/empty_request`;

        if(searchInput.elem.value.length > 1) {
            path = `http://symfoapp/${helper.getCurrentLocale()}/task/list/search/${searchInput.elem.value}`;
        }

        AjaxSender.sendGet(path, function(xhr) {
            let sidenav  = document.querySelector('.sidenav');
            let instance = M.Sidenav.getInstance(sidenav);
            instance.close();


            Sorter.appendSortedContent(xhr);

            /** TODO Replace this to Table component? */
            table.rows.forEach(r => {
                if(r.innerText.includes('Done') || r.innerText.includes('Готово')) {
                    r.classList.remove('hide');
                }
            });


            let icon = eventManager.triggers.switchPerformedTasksButton.elem.children[0];
            icon.innerText = 'radio_button_checked'
        });
    };


    return {
        setSearchListeners: eventManager.setSearchListeners,
    };
}();


export default Searcher;
