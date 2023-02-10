
// window.addEventListener('load', validate());
let inscription = document.getElementById('inscription');
inscription.addEventListener('click', function (e) {
    // déclarer les variables
    let civilite = document.forms['inscription']['civilite'];
    let pseudo = document.forms['inscription']['pseudo'];
    let first_name = document.forms['inscription']['first_name'];
    let last_name = document.forms['inscription']['last_name'];
    let tel = document.forms['inscription']['tel'];
    let email = document.forms['inscription']['email'];
    let address = document.forms['inscription']['address'];
    let city = document.forms['inscription']['city'];
    let pc = document.forms['inscription']['pc'];
    let password = document.forms['inscription']['password'];
    let passwordConfirm = document.forms['inscription']['passwordConfirm'];

    let first_nameValid = last_nameValid = pseudoValid = telValid = emailValid = addressValid = cityValid = pcValid = passwordValid = false;
    let regexfirst_name = /^[a-zA-Z]+(?:[\s\'\-][a-zA-Z]+)*$/;
    
    // validate champs, pas plus de 50 lettres
    
    if(pseudo.value.length > 50) {
        pseudo.nextElementSibling.textContent = "Pas plus de 50 characters";
    }
    else {
        pseudo.nextElementSibling.textContent ="";
        pseudoValid = true;

    }

    // validate tel que chiffres
    let regexTel = /^[\+\(\s.\-\/\d\)]{5,30}$/;
    if (tel.value == "") {
        tel.nextElementSibling.textContent = "required";
    }
    else if (regexTel.test(tel.value) == false || tel.value.length > 30) {
        tel.nextElementSibling.textContent = "Accept les chiffres seulement et pas plus de 30";
    }
    else {
        tel.nextElementSibling.textContent = "";
        telValid = true;
    }

    // validate champs first_name/ last_name avec que des lettres, espace, ' -
    if (first_name.value == "") {
        first_name.nextElementSibling.textContent = "required";
        
    }
    else if (regexfirst_name.test(first_name.value.trim()) == false || first_name.value.length > 50) {
        first_name.nextElementSibling.textContent= "Accepte lettres, espace, -, ' et pas plus de 50 characters";
        
    }
    else {
        first_name.nextElementSibling.textContent = "";
        first_nameValid = true;
    }

    if (last_name.value == "") {
        last_name.nextElementSibling.textContent = "required";
    }
    else if (regexfirst_name.test(last_name.value.trim()) == false || last_name.value.length > 50) {
        last_name.nextElementSibling.textContent= "Accepte lettres, espace, -, ' et pas plus de 50 characters";
    }
    else {
        last_name.nextElementSibling.textContent = "";
        last_nameValid = true;
    }

    // validate champs address, city, pc pas vide et pas trop long
    if (address.value == "") {
        address.nextElementSibling.textContent = "required";
        
    }
    else if(address.value.length > 150) {
        address.nextElementSibling.textContent= "Adresse trop long!";
        
    }
    else {
        address.nextElementSibling.textContent = "";
        addressValid = true;
    }

    if (city.value == "") {
        city.nextElementSibling.textContent = "required";
    }
    else if(city.value.length > 50) {
        city.nextElementSibling.textContent= "Ville trop long!";
    }
    else {
        city.nextElementSibling.textContent = "";
        cityValid = true;
    }

    if (pc.value == "") {
        pc.nextElementSibling.textContent = "required";

    }
    else if(pc.value.length > 20) {
        pc.nextElementSibling.textContent= "Code Postal trop long ou invalid!";

    }
    else {
        pc.nextElementSibling.textContent = "";
        pcValid = true;
    }

    // validate email not vide et email valid
    let regexEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (email.value == "") {
        email.nextElementSibling.textContent = "required";

    }
    else if (regexEmail.test(email.value) == false) {
        email.nextElementSibling.textContent = "Email invalid";

    }
    else {
        email.nextElementSibling.textContent = "";
        emailValid = true;
    }

    // validate password /passwordConfirm pas vide et match
    let regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,20}$/;
    let ePasswordConfirm = document.getElementById('ePasswordConfirm');
    let ePassword = document.getElementById('ePassword')
    if (password.value == "" || passwordConfirm.value == "") {
        password.nextElementSibling.textContent = "required";
        passwordConfirm.nextElementSibling.textContent = "required";

    }
    else if (regexPassword.test(password.value) == false) {
       
        ePassword.textContent = "Les mots de passe doivent être entre 8 et 20 characters, 1 majuscule, 1 minuscule, 1 chiffre ";
    }
    
    else if (password.value !== passwordConfirm.value) {
        ePassword.textContent = "Les mots de passe doivent être identiques. ";
        ePasswordConfirm.textContent = "Les mots de passe doivent être identiques. ";
    }
    else {
        ePassword.textContent = "";
        ePasswordConfirm.textContent = "";
        passwordValid = true;

    }

    if (first_nameValid && pseudoValid && last_nameValid && telValid && emailValid && cityValid && addressValid && pcValid && passwordValid ) {
        return true;
    }
    else {
        e.preventDefault();
    }
    
    
});

// //show/ hide password && changer icon eye-off / eye-on avec le clic sur icon eye password
// let eye = document.getElementById('eye');
// let eyeConfirm = document.getElementById('eyeConfirm');
// eye.addEventListener('click', function () {
//     let password = document.forms['inscription']['password'];
//     if (password.type == 'password') {
//             password.type = 'text';
//             this.src = "./inc/img/eye-1.svg";

//         }
//         else {
//             password.type = 'password';
//             this.src = "./inc/img/eye-2.svg";

//         }
// })

// //show/ hide password && changer icon eye-off / eye-on avec le clic sur icon eye password
// eyeConfirm.addEventListener('click', function () { 
//     let passwordConfirm = document.forms['inscription']['passwordConfirm'];
    
//     if (passwordConfirm.type == 'password') {
//             passwordConfirm.type = 'text';
//             this.src = "./inc/img/eye-1.svg";
//         }
//         else {
//             passwordConfirm.type = 'password';
//             this.src = "./inc/img/eye-2.svg";

//         }
// })
