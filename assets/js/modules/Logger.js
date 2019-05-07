/**
 * Log data in convenient way
 *
 * @module ../../js/modules/Logger
 * @type {{logFormData, logXhrData}}
 * @see https://developer.mozilla.org/ru/docs/Web/API/console#Using_string_substitutions
 */
let Logger = function() {

    // Colors
    const debugClr = '#dcdcdc';
    const infoClr  = '#afeeee';
    const errorClr = '#ff4500';

    const formDataClr  = '#a3e4d7';
    const xhrDataClr   = '#6a5acd';
    const eventDataClr = '#ffb6c1';
    const funcGroupClr = debugClr;

    // Font sizes
    const groupHeaderFontSize = '11px';
    const dataFontSize        = '12px';

    // Indents
    const groupHeaderPadding = '3px';
    const groupItemPadding   = '2px';


    /**
     * Use it to log functions, which has inner logs
     *
     * @param {string} name     - Block name
     * @param {string} fontSize
     * @param {string} color
     */
    let printGroupStart = function(name, fontSize=dataFontSize, color=debugClr) {
        console.groupCollapsed(
            '%c['   + name + ']',
            'font-size:' + fontSize +
            '; color:'   + color +
            '; padding:' + groupHeaderPadding + ';'
        );
    };


    /**
     * Print the end of the group
     */
    let printGroupEnd = function() {
        console.groupEnd();
    };


    /**
     * Shows FormData values in console
     *
     * @param {HTMLFormElement} form
     * @param {FormData}        formData
     */
    let logFormData = function(form, formData) {
        let groupTitle = (form['id'].length > 0) ? form['id'] : 'formData';

        console.groupCollapsed(
            '%c[' + groupTitle + ']',
            'font-size:'  + groupHeaderFontSize +
            '; color: '   + formDataClr +
            '; padding: ' + groupHeaderPadding + ';'
        );

        for(let pair of formData.entries()) {
            console.log(
                '%c' +pair[0]+ ' -> ' + pair[1],
                'font-size:' + dataFontSize +
                '; color:'   + formDataClr +
                '; padding:' + groupItemPadding + ';'
            );
        }

        printGroupEnd();
    };


    /**
     * Shows XHR response Text in collapsed mode
     *
     * @param {XMLHttpRequest} xhr - XHR
     */
    let logXhrData = function(xhr) {

        console.groupCollapsed(
            '%c[XHR: Response Text]',
            'font-size: ' + groupHeaderFontSize +
            '; color:'    + xhrDataClr +
            '; padding: ' + groupHeaderPadding + ';'
        );

        console.log(xhr.responseText);

        console.groupEnd();
    };


    /**
     * Shows Event in collapsed mode
     *
     * @param {Event} event - XHR
     */
    let logEvent = function(event) {

        console.groupCollapsed(
            '%c[Event: ' + event.type + ']',
            'font-size: '     + groupHeaderFontSize +
            '; color:'        + eventDataClr +
            '; padding: '     + groupHeaderPadding + ';'
        );

        console.log(event);

        console.groupEnd();
    };


    return {
        printGroupStart: printGroupStart,
        printGroupEnd  : printGroupEnd,
        logFormData: logFormData,
        logXhrData : logXhrData,
        logEvent   : logEvent,
    }
}();


export default Logger;
