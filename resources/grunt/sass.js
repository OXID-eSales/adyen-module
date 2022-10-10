const sass = require('node-sass');

module.exports = {
    moduledevelopment: {
        options: {
            implementation: sass,
            update: true,
            style: 'nested'
        },
        files: {
            "../out/src/css/adyen.css": "build/scss/adyen.scss"
        }
    },

    moduleproduction: {
        options: {
            implementation: sass,
            update: true,
            style: 'compressed'
        },
        files: {
            "../out/src/css/adyen.css": "build/scss/adyen.scss"
        }
    }
};

