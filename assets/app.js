/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// start the Stimulus application
import './bootstrap';
import * as bootstrap from 'bootstrap';
var $ = require('jquery');
import '@progress/kendo-ui';
var swal = require('sweetalert');
import './js/ajax-grid-table.js';
//var dt      = require( 'datatables.net' );
//var buttons = require( 'datatables.net-buttons' );

//import $ from 'jquery' DOESN'T WORK
global.$ = global.jQuery = $;
global.swal = swal;
window.swal = swal;


// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
/*require( 'datatables.net-buttons/js/buttons.colVis.js' )(); 
require( 'datatables.net-buttons/js/buttons.html5.js' )();  
require( 'datatables.net-buttons/js/buttons.print.js' )();  
require( 'datatables.net' )();  
require( 'datatables.net-colreorder' )();  
require( 'datatables.net-fixedcolumns' )();  
require( 'datatables.net-fixedheader' )();  
require( 'datatables.net-keytable' )();  
require( 'datatables.net-rowgroup' )();  
require( 'datatables.net-rowreorder' )();  
require( 'datatables.net-responsive' )();  
require( 'datatables.net-scroller' )();  
require( 'datatables.net-searchbuilder' )();  
require( 'datatables.net-searchpanes' )();  
require( 'datatables.net-select' )();  
require( 'datatables.net-staterestore' )();  */

