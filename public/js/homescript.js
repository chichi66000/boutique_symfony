window.addEventListener('load', homeJS);

function homeJS() {
    const linkCart = document.getElementsByClassName('indexNewsLinkGalleryCart');

    [...linkCart].forEach((elem, index) => {
        elem.addEventListener('click', function(e) {
            e.preventDefault();
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const nbProductOfCart = document.getElementById('headerNbProductOfCart');
                    nbProductOfCart.textContent = this.responseText;
                }
            }
            xhttp.open("POST", "src/controllers/cart.php", false);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send('idprod=' + elem.getAttribute('data-product'));
        })
    });
}