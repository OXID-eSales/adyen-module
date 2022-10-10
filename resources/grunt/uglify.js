module.exports = {

    options: {
        preserveComments: false
    },
    moduleproduction: {
        files: {
            "../out/src/js/adyen.min.js": [
                "build/js/adyen.js",
                "node_modules/@adyen/adyen-web/dist/adyen.js",
            ]
        }
    }
};