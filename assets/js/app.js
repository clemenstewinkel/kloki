/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import '../css/app.css';
import $ from 'jquery';
import 'bootstrap';
import 'popper.js';
import 'bootstrap-select';
import '@fortawesome/fontawesome-free';
import '@fortawesome/fontawesome-free/css/all.min.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
var autocomplete = require('autocomplete.js');
global.$ = $;
