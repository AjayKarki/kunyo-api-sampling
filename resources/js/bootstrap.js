try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    window.router = require('./route');
    window.utils  = require('./utils');
} catch (e) {}
