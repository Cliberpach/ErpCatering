
import './bootstrap';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css'; 

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
};
window.toastr = toastr;



