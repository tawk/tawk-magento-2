define([], function () {
    'use strict';

    return function (config) {
        var visitor = config.visitor,
        embedUrl = config.embedUrl,
        /* eslint-disable-next-line no-unused-vars */
        Tawk_LoadStart = new Date();

        window.Tawk_API = window.Tawk_API || {};

        if (visitor) {
            window.Tawk_API.visitor = {
                name : visitor.name,
                email : visitor.email
            };
        }

        (function () {
            var s1 = document.createElement('script'),s0 = document.getElementsByTagName('script')[0];

            s1.async = true;
            s1.src = embedUrl;
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin','*');
            s0.parentNode.insertBefore(s1,s0);
        })();
    };
});

