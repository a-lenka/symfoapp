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
    };


    return {
        initComponents: initComponents,
    }
}();


export default Materializer;
