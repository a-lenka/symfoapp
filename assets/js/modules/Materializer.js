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


    return {
        initComponents: initComponents,
    }
}();


export default Materializer;
