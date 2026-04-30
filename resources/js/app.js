import './bootstrap';

import $ from 'jquery';
window.$ = window.jQuery = $;

import moment from 'moment';
window.moment = moment;

import 'daterangepicker';

// import Swal from 'sweetalert2';
// window.Swal = Swal; // make swal global

import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
    duration: 5000,
    position: { x: 'right', y: 'top' },
    ripple: true,
    dismissible: true,
    types: [
        {
            type: 'warning',
            background: '#f89406',
            icon: false,
            dismissible: true,
        },
        {
            type: 'info',
            background: '#2f96b4',
            icon: false,
            dismissible: true,
        }
    ]
});

// Toastr jesi API — purana blade code same kaam karega
window.toastr = {
    success: (msg, title) => notyf.success(title ? `<strong>${title}</strong><br>${msg}` : msg),
    error:   (msg, title) => notyf.error(title ? `<strong>${title}</strong><br>${msg}` : msg),
    warning: (msg, title) => notyf.open({ type: 'warning', message: title ? `<strong>${title}</strong><br>${msg}` : msg }),
    info:    (msg, title) => notyf.open({ type: 'info',    message: title ? `<strong>${title}</strong><br>${msg}` : msg }),
};

window.notyf = notyf;
