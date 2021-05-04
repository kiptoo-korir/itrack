function showSpinner() {
    $('.background-spinner, .spin-wrapper').show();
    setTimeout(() => {
        hideSpinner();
    }, 10000);
}

function hideSpinner() {
    $('.background-spinner, .spin-wrapper').hide();
}