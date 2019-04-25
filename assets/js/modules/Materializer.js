/**
 * Manage Materialize CSS components
 *
 * @module ../../js/modules/Materializer
 * @type {{initComponents}}
 */
let Materializer = function() {

    /**
     * Initialize all Materialize CSS components at once
     */
    let initComponents = function() {
        M.AutoInit();

        let dropdowns = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdowns, {constrainWidth: false});
    };

    /**
     * Reinitialize Select Component
     */
    let reInitSelects = function() {
        let selects   = document.querySelectorAll('select');
        let instances = M.FormSelect.init(selects, {});
    };


    return {
        initComponents: initComponents,
        reInitSelects : reInitSelects,
    }
}();


export default Materializer;
