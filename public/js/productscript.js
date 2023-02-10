window.addEventListener('load', productJS);

function productJS() {
    const bigImage = document.getElementById('bigImage');
    const miniImage1 = document.getElementById('miniImage1');
    const miniImage2 = document.getElementById('miniImage2');
    const miniImage3 = document.getElementById('miniImage3');

    miniImage1.addEventListener('click', function() {
        const srcBigImage = bigImage.getAttribute('src');
        const srcMiniImage1 = miniImage1.getAttribute('src');

        bigImage.setAttribute('src', srcMiniImage1);
        this.setAttribute('src', srcBigImage);        

    });

    miniImage2.addEventListener('click', function() {
        const srcBigImage = bigImage.getAttribute('src');
        const srcMiniImage2 = miniImage2.getAttribute('src');

        bigImage.setAttribute('src', srcMiniImage2);
        this.setAttribute('src', srcBigImage);        

    });

    miniImage3.addEventListener('click', function() {
        const srcBigImage = bigImage.getAttribute('src');
        const srcMiniImage3 = miniImage3.getAttribute('src');

        bigImage.setAttribute('src', srcMiniImage3);
        this.setAttribute('src', srcBigImage);        

    });

}