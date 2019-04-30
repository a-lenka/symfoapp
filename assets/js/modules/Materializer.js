/**
 * Manage Materialize CSS components
 *
 * @module ../../js/modules/Materializer
 * @type {{initComponents, reInitFormFields}}
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
     * Reinitialize form inputs to render `active` class
     * when form is in Modal window
     */
    let reInitInputs = function() {
        M.updateTextFields();
    };


    /**
     * Reinitialize Select Component
     */
    let reInitSelects = function() {
        let selects   = document.querySelectorAll('select');
        let instances = M.FormSelect.init(selects, {});
    };


    /**
     * Some Materialize CSS Components do not work with dynamic content,
     * so they need to be reinitialized after form will be inserted into Modal
     */
    let reInitFormFields = function() {
        reInitInputs();
        reInitSelects();
    };


    return {
        initComponents  : initComponents,
        reInitFormFields: reInitFormFields,
    }
}();


export default Materializer;
