window.TemplateCache =  new (function() {
    "use strict";

    var cachedTemplatesMap = {};

    /**
     * @param {string} originalUrl
     * @param {string} cachedUrl
     */
    this.add = function (originalUrl, cachedUrl) {
        cachedTemplatesMap[originalUrl] = cachedUrl;
    };

    /**
     * @param {string} originalUrl
     * @returns {string|undefined}
     */
    this.get = function (originalUrl) {
        return (cachedTemplatesMap.hasOwnProperty(originalUrl))
            ? cachedTemplatesMap[originalUrl]
            : undefined;
    };
})();
