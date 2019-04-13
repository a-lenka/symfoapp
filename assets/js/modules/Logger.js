/**
 * Log data in convenient way
 *
 * @module ../../js/modules/Logger
 * @type {{logFormData, logXhrData}}
 * @see https://developer.mozilla.org/ru/docs/Web/API/console#Using_string_substitutions
 */
let Logger = function() {

    // Colors
    const formDataClr = 'teal';
    const xhrDataClr  = '#6699cc';

    // Font sizes
    const groupHeaderFontSize = '12px';
    const dataFontSize        = '14px';


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
            'font-size:' + groupHeaderFontSize +
            '; color: '  + formDataClr +
            '; padding: 5px;'
        );

        for(let pair of formData.entries()) {
            console.log(
                '%c' +pair[0]+ ' -> ' + pair[1],
                'font-size:' + dataFontSize +
                '; color: '  + formDataClr +
                '; padding: 2px;'
            );
        }

        console.groupEnd();
    };


    /**
     * Shows XHR response Text in collapsed mode
     *
     * @param {XMLHttpRequest} xhr - XHR
     */
    let logXhrData = function(xhr) {

        console.groupCollapsed(
            '%c[XHR Response Text]',
            'font-size: ' + groupHeaderFontSize +
            '; color:' + xhrDataClr +
            '; padding: 5px;'
        );

        console.log(xhr.responseText);

        console.groupEnd();
    };

    return {
        logFormData: logFormData,
        logXhrData : logXhrData,
    }
}();


export default Logger;
