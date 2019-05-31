/**
 * Handle multiply actions
 *
 * @module ../../js/utils/Helper
 * @type {{getCurrentLocale}}
 */
let Helper = function() {

    let getCurrentLocale = function() {

        return document
            .getElementsByTagName('html')[0]
            .getAttribute('lang');
    };

    return {
        getCurrentLocale: getCurrentLocale,
    };
}();


export default Helper;
