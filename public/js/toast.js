function feedback (message, type) {
    if (type == 'success') {
        $('#notifysuccess').find('.toast-body').html(message);
        $('#notifysuccess').toast('show');
    } else if (type == 'error') {
        $('#notifyerror').find('.toast-body').html(message);
        $('#notifyerror').toast('show');
    } else if (type == 'warning') {
        $('#notifywarning').find('.toast-body').html(message);
        $('#notifywarning').toast('show');
    }
}