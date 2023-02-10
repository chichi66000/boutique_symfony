window.addEventListener('load', navJS);

function navJS() {
    const navMenuBurger = document.getElementById('navMenuBurger');
    const navMobileSide = document.getElementById('navMobileSide');
    const navMobileClose = document.getElementById('navMobileClose');

    // show the mobile menu on right 
    navMenuBurger.addEventListener('click', () => {
        navMobileSide.style.transform = 'scale(1)';
    });

    // close the mobile menu on right 
    navMobileClose.addEventListener('click', () => {
        navMobileSide.style.transform = 'scale(0)';
    });
}