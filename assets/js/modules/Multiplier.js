// Imports
import AjaxSender  from '../../js/modules/AjaxSender';
import ModalWidget from '../../js/modules/ModalWidget';

/**
 * Handle multiply actions
 *
 * @module ../../js/modules/Multiplier
 * @type {{setMultiplyListeners}}
 */
let Multiplier = function() {

    let helper = {
        getCurrentLocale: function() {
            return document.getElementsByTagName('html')[0].getAttribute('lang');
        },
    };


    let checkbox = {
        html: '<label><input type="checkbox"><span></span></label>',
        selector: 'input[type="checkbox"]',

        get elems() {
            return document.querySelectorAll(checkbox.selector);
        },
    };


    let table = {
        class: 'table.responsive-table.highlight',

        get elem() {
            let tableElem = document.querySelectorAll(table.class)[0];

            if(tableElem) {
                console.log('Find table');
                return tableElem;
            } else {
                console.log('There are no table on this page');
            }
        },

        get rows() {
            let elem = table.elem;
            let tbodyRows = elem.children[1].rows;

            if(!tbodyRows) {
                throw new Error('There are no rows in the table');
            } else {console.log('Find rows');}

            return tbodyRows;
        },

        getCheckedCheckboxes: function() {
            let values = Object.values(checkbox.elems);
            return values.filter(checkbox => checkbox.checked === true);
        },

        extractId: function(checkbox) {
            let label    = checkbox.parentElement;
            let td       = label.parentElement;
            let row      = td.parentElement;
            let targetTd = row.children[row.children.length - 1];
            let link     = targetTd.children[0];
            return link.href.match(/\d+/);
        },

        extractAllIds: function() {
            let checked = table.getCheckedCheckboxes();
            return Array.from(checked, checkbox => table.extractId(checkbox));
        },
    };


    let checker = {
        confirmAppendCheckboxEvent: function(event) {
            let className     = eventManager.triggers.checkboxButton.className;
            let isAppendEvent = event.target.className.includes(className);

            console.log('Check if it is Check checkbox event? : ' + isAppendEvent);

            return isAppendEvent;
        },

        confirmRequestCheckedItemsEvent: function(event) {
            let className      = eventManager.triggers.confirmButton.className;
            let isRequestEvent = event.target.className.includes(className);

            console.log('Check if it is Request checked items event? : ' + isRequestEvent);

            return isRequestEvent;
        },

        confirmDeletePermanentlyEvent: function(event) {
            let isDeleteEvent = (event.target.pathname)
                ? event.target.pathname.includes('list/delete')
                : false;

            console.log('Check if it is Delete permanently event? : ' + isDeleteEvent);

            return isDeleteEvent;
        }
    };


    let eventManager = {
        triggers: {
            checkboxButton: {
                className: 'btn-floating orange darken-1',
                selector : 'div.fixed-action-btn a.orange',

                get elem() {
                    let btn = document.querySelectorAll(eventManager.triggers.checkboxButton.selector)[0];

                    if(!btn) {
                        throw new Error('Checkbox button not found');
                    } else {
                        console.log('Find checkbox button');

                        return btn;
                    }
                },
            },

            confirmButton: {
                className: 'btn-floating red darken-1',
                selector: 'div.fixed-action-btn a.red',

                get elem() {
                    return document.querySelectorAll(eventManager.triggers.confirmButton.selector)[0];
                },

                activate: function() {
                    let btn = eventManager.triggers.confirmButton.elem;
                    console.log(btn);
                    btn.removeAttribute('disabled');
                },

                deactivate: function() {
                    let btn = eventManager.triggers.confirmButton.elem;
                    btn.setAttribute('disabled', true);
                },
            },

            deletePermanentlyButton: {
                selector: 'a.btn.waves-effect.waves-light.red',

                get elem() {
                    return document.querySelectorAll(eventManager.triggers.deletePermanentlyButton.selector)[0];
                },
            },
        },

        listeners: {
            chooseItems: {
                get elem() {
                    return document.body;
                }
            },
        },

        setMultiplyListeners: function() {
            if(table.elem) {
                eventManager.listeners.chooseItems.elem.addEventListener('click', appendCheckboxes);
                eventManager.listeners.chooseItems.elem.addEventListener('click', requestCheckedItems);
                eventManager.listeners.chooseItems.elem.addEventListener('click', deletePermanently);
                console.log('Set Multiply listeners');
            }
        },
    };


    let appendCheckboxes = function(event) {
        if(checker.confirmAppendCheckboxEvent(event)) {
            let rows = document.getElementsByTagName('table')[0].rows;

            for(let i = 0; i < rows.length; i++) {
                rows[i].cells[0].innerHTML = checkbox.html;
            }

            eventManager.triggers.confirmButton.activate();

            console.log('Append checkboxes');
        }
    };


    let requestCheckedItems = function(event) {
        if(checker.confirmRequestCheckedItemsEvent(event)) {
            event.preventDefault();

            let path = eventManager.triggers.confirmButton.elem.getAttribute('href');
            let ids  = table.extractAllIds();

            eventManager.triggers.confirmButton.deactivate();

            AjaxSender.sendPost(path, function (xhr) {
                ModalWidget.appendFormContent(xhr);
            }, JSON.stringify(ids));
        }
    };


    let deletePermanently = function(event) {
        if(checker.confirmDeletePermanentlyEvent(event)) {
            event.preventDefault();

            let path   = eventManager.triggers.deletePermanentlyButton.elem.getAttribute('href');
            let emails = table.extractAllIds();

            console.log(emails);

            AjaxSender.sendPost(path, function (xhr) {
                ModalWidget.appendFormContent(xhr);
            }, JSON.stringify(emails));
        }
    };


    return {
        setMultiplyListeners: eventManager.setMultiplyListeners,
    };
}();


export default Multiplier;
