function showSpinner() {
    document.getElementById("spinner-background").style.display = "block";
    document.getElementById("spinner-wrapper").style.display = "block";
    setTimeout(() => {
        hideSpinner();
    }, 10000);
}

function hideSpinner() {
    document.getElementById("spinner-background").style.display = "";
    document.getElementById("spinner-wrapper").style.display = "";
}
