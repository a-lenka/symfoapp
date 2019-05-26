// Imports
import AjaxSender  from '../utils/AjaxSender';
import ModalWidget from '../../js/modules/ModalWidget';
import TableList   from '../../js/modules/TableList';

/**
 * Handle multiply actions
 *
 * @module ../../js/modules/Multiplier
 * @type {{setMultiplyListeners}}
 */
let Multiplier = function() {

    let switchCheckboxes = function(event) {

        if (checker.confirmAppendCheckboxEvent(event)) {
            let checkboxes = TableList.checkbox.getElemsAsArray();
            let rows       = TableList.table.rows;

            if(checkboxes.length === 0) {
                TableList.table.replaceIdsWithCheckboxes();
                eventManager.triggers.confirmButton.activate();
            } else {
                rows.forEach(function(row) {
                    let idCell     = row.children[0];
                    let actionCell = row.children[row.children.length - 1];
                    let actionLink = actionCell.children[0];

                    idCell.innerHTML = actionLink.href.match(/\d+/);
                });

                eventManager.triggers.confirmButton.deactivate();
            }
        }
    };


    let checker = {
        confirmAppendCheckboxEvent: function(event) {
            let className = eventManager.triggers.checkboxButton.className;
            return event.target.className.includes(className);
        },

        confirmRequestCheckedItemsEvent: function(event) {
            let className = eventManager.triggers.confirmButton.className;
            return event.target.className.includes(className);
        },

        confirmDeletePermanentlyEvent: function(event) {
            return (event.target.pathname)
                ? event.target.pathname.includes('list/delete')
                : false;
        },

        confirmHidePerformedEvent: function(event) {
            let className = eventManager.triggers.switchPerformedTasksButton.className;
            return event.target.className.includes(className) && !window.location.href.includes('user');
        },
    };


    let eventManager = {

        triggers: {
            checkboxButton: {
                className: 'btn-floating orange darken-1',
                selector : 'div.fixed-action-btn a.orange',

                get elem() {
                    let btn = document.querySelectorAll(eventManager.triggers.checkboxButton.selector)[0];
                    if(!btn) { throw new Error('Checkbox button not found'); }
                    return btn;
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

            switchPerformedTasksButton: {
                className: 'btn-floating indigo darken-1',
                selector : 'a.btn-floating.indigo.darken-1',

                get elem() {
                    return document.querySelectorAll(eventManager.triggers.switchPerformedTasksButton.selector)[0];
                }
            }
        },

        listeners: {
            chooseItems: {
                get elem() {
                    return document.body;
                }
            },
        },

        setMultiplyListeners: function() {
            if(TableList.table.elem) {
                eventManager.listeners.chooseItems.elem.addEventListener('click', switchCheckboxes);
                eventManager.listeners.chooseItems.elem.addEventListener('click', requestCheckedItems);
                eventManager.listeners.chooseItems.elem.addEventListener('click', deletePermanently);
                eventManager.listeners.chooseItems.elem.addEventListener('click', switchPerformedTasks);
            }
        },
    };


    let requestCheckedItems = function(event) {
        if(checker.confirmRequestCheckedItemsEvent(event)) {
            event.preventDefault();

            let path = eventManager.triggers.confirmButton.elem.getAttribute('href');
            let ids  = TableList.table.extractIdsAsArray();

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
            let emails = TableList.table.extractIdsAsArray();

            AjaxSender.sendPost(path, function (xhr) {
                ModalWidget.appendFormContent(xhr);
            }, JSON.stringify(emails));
        }
    };

    let switchPerformedTasks = function(event) {
        if(checker.confirmHidePerformedEvent(event)) {
            event.preventDefault();

            TableList.table.rows.forEach(r => {
                if(r.innerText.includes('Done') || r.innerText.includes('Готово')) {
                    r.classList.toggle('hide');
                }
            });

            let icon = eventManager.triggers.switchPerformedTasksButton.elem.children[0];
            if(icon.innerText === 'radio_button_checked') { icon.innerText = 'radio_button_unchecked'; }
            else { icon.innerText = 'radio_button_checked' }
        }
    };


    return {
        setMultiplyListeners: eventManager.setMultiplyListeners,
    };
}();


export default Multiplier;
