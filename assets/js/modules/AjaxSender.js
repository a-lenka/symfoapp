// Imports
import Logger from '../../js/modules/Logger.js';

/**
 * Send Ajax Requests
 *
 * @module ../../js/modules/AjaxSender
 * @type {{sendGet, sendPost}}
 */
let AjaxSender = function() {

    /**
     * Create and send Ajax request object
     *
     * @param {string}   method          - Request method
     * @param {string}   path            - Request path
     * @param {callback} success         - Success callback
     * @param {(FormData|Object)} [data] - Request data
     */
    let sendRequest = function(method, path, success, data) {

        let requestData = (data === null) ? '' : data;

        let xhr = new XMLHttpRequest();
        xhr.open(method, path);
        // To pass `xhr.responseText`  into `modal.init(xhr)`
        xhr.runCallback = success;
        // To `isXmlHttpRequest()` works correctly
        xhr.setRequestHeader("X-Requested-With","XMLHttpRequest");
        // Without this, if the root click `Account` he see it in the Modal
        xhr.url = path;
        xhr.send(data);

        xhr.onreadystatechange = function() {
            if(xhr.readyState !== 4) {return;}

            if(xhr.readyState === 4
                && xhr.status === 200
                || xhr.status === 403) {
                Logger.logXhrData(xhr);

                xhr.runCallback(xhr);
            }

            if(xhr.status !== 200) {
                console.error(
                    'Error: ' + this.status ? this.statusText : 'XHR failed'
                );
            }
        };
    };


    /**
     * Send GET request
     *
     * @param {string}   path            - Request path
     * @param {callback} success         - Success callback
     * @param {(FormData|Object)} [data] - Request data (optional)
     */
    let sendGet = function(path, success, data=null) {
        sendRequest('GET', path, success, data);
    };


    /**
     * Send POST request
     *
     * @param {string}   path            - Request path
     * @param {callback} success         - Success callback
     * @param {(FormData|Object)} [data] - Request data (optional)
     */
    let sendPost = function(path, success, data=null) {
        sendRequest('POST', path, success, data);
    };


    return {
        sendGet : sendGet,
        sendPost: sendPost,
    }
}();


export default AjaxSender;
