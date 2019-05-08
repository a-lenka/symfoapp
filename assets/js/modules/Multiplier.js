// Imports
import AjaxSender from '../../js/modules/AjaxSender';

/**
 * Handle multiply actions
 *
 * @module ../../js/modules/Multiplier
 * @type {{setMultiplyListeners}}
 */
let Multiplier = function() {

    let checkbox = {
        html: '<label><input type="checkbox"><span></span></label>',
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
            let tbodyRows = table.elem.children[1].rows;

            if(!tbodyRows) {
                throw new Error('There are no rows in the table');
            } else {console.log('Find rows');}

            return tbodyRows;
        }
    };


    let checker = {
        confirmAppendCheckboxEvent: function(event) {
            let className = eventManager.triggers.checkboxButton.className;
            let isAppendEvent = event.target.className.includes(className);

            console.log('Check if it is Check checkbox event? : ' + isAppendEvent);

            return isAppendEvent;
        }
    };


    let eventManager = {
        triggers: {
            checkboxButton: {
                className: 'btn-floating yellow darken-1',
                selector : 'div.fixed-action-btn a.yellow',

                get elem() {
                    let btn = document.querySelectorAll(eventManager.trigger.selector)[0];

                    if(!btn) {
                        throw new Error('Checkbox button not found');
                    } else {console.log('Find checkbox button');}

                    return btn;
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
                console.log('Set Multiply listeners');
            }
        },
    };


    let appendCheckboxes = function(event) {
        if(checker.confirmAppendCheckboxEvent(event)) {
            let rows = table.rows;

            for(let i = 0; i < rows.length; i++) {
                rows[i].cells[0].innerHTML = checkbox.html;
            }

            console.log('Append checkboxes');
        }
    };


    return {
        setMultiplyListeners: eventManager.setMultiplyListeners,
    };
}();


export default Multiplier;
