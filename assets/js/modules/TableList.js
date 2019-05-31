/**
 * Handle multiply actions
 *
 * @module ../../js/modules/TableList
 * @type {{table, checkbox}}
 */
let TableList = function() {

    let checkbox = {
        html    : `<label><input type="checkbox"><span></span></label>`,
        selector: 'input[type="checkbox"]',

        get elems() {
            return document.querySelectorAll(checkbox.selector);
        },

        getElemsAsArray: function() {
            return Object.values(checkbox.elems);
        },

        getCheckedElems: function() {
            let checkboxes = checkbox.getElemsAsArray();
            return checkboxes.filter(cb => cb.checked === true);
        },
    };


    let table = {
        class: 'table.responsive-table.highlight',

        get elem() {
            let tableElem = document.querySelector(table.class);

            if(!tableElem) { console.log('Table not found'); }
            return tableElem;
        },

        get rows() {
            let tbodyRows = table.elem.children[1].rows;

            if(!tbodyRows) { throw new Error('There are no rows in the table'); }
            return tbodyRows;
        },

        getIdCellsArray() {
            return Array.from(table.rows, r => r.children[0]);
        },

        extractIdFromCheckedCheckbox: function(checkbox) {
            let label    = checkbox.parentElement;
            let td       = label.parentElement;
            let row      = td.parentElement;
            let targetTd = row.children[row.children.length - 1];
            let link     = targetTd.children[0];
            return link.href.match(/\d+/);
        },

        extractIdsAsArray: function() {
            let checkboxes = checkbox.getCheckedElems();
            return Array.from(checkboxes, cb => table.extractIdFromCheckedCheckbox(cb));
        },

        replaceIdsWithCheckboxes: function() {
            table.rows.forEach(function(r) { r.children[0].innerHTML = checkbox.html; });
        },

        showPerformedTasks: function() {
            table.rows.forEach(r => {
                if(r.innerText.includes('Done') || r.innerText.includes('Готово')) {
                    r.classList.remove('hide');
                }
            });
        },
    };


    return {
        table   : table,
        checkbox: checkbox,
    };
}();


export default TableList;
