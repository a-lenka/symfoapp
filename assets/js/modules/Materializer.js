/**
 * Manage Materialize CSS components
 *
 * @module ../../js/modules/Materializer
 * @type {{initJS, reInitFormFields}}
 */
let Materializer = function() {

    /**
     * Initialize all Materialize CSS components at once
     */
    let initJS = function() {
        M.AutoInit();

        reInitInputs();
        reInitSelects();
        reInitDropdowns();
        reInitDatePickers();
        reInitTimePickers();
    };


    /**
     * Reinitialize form inputs to render `active` class
     * when form is in Modal window
     */
    let reInitInputs = function() {
        M.updateTextFields();
    };


    /**
     * Configure Materialize Dropdown component
     */
    let reInitDropdowns = function() {
        let dropdowns = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdowns, {'constrainWidth': false});

    };


    /**
     * Default Materialize CSS format in unreadable for the server
     */
    let reInitDatePickers = function() {
        let datepickers = document.querySelectorAll('.datepicker');
        let instances = M.Datepicker.init(datepickers, {
            'format': 'yyyy-mm-dd',
            'autoClose': true,
            'firstDay': 1,
            'showMonthAfterYear': true,
            'showDaysInNextAndPreviousMonths': true,
        });
    };


    /**
     * Pickers must be configured to work
     */
    let reInitTimePickers = function() {
        let timepickers = document.querySelectorAll('.timepicker');
        let instances = M.Timepicker.init(timepickers, {
            'twelveHour': false,
            'autoClose': true,
        });
    };


    /**
     * Reinitialize Select Component
     */
    let reInitSelects = function() {
        let selects   = document.querySelectorAll('select');
        let instances = M.FormSelect.init(selects, {
            'dropdownOptions': {'constrainWidth': false},
        });
    };


    /**
     * Some Materialize CSS Components do not work with dynamic content,
     * so they need to be reinitialized after form will be inserted into Modal
     */
    let reInitFormFields = function() {
        reInitInputs();
        reInitSelects();
        reInitDatePickers();
        reInitTimePickers();
    };


    return {
        initJS: initJS,
        reInitFormFields: reInitFormFields,
    }
}();


export default Materializer;
