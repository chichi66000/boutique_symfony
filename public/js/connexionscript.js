window.addEventListener('load', connexionJS);

function connexionJS() {
    const connexionFormButton = document.getElementById('connexionButton');
    const connexionEmail = document.getElementById('email');
    const connexionPass = document.getElementById('pass');
    const connexionEmailError = document.getElementById('errorMail');
    const connexionPassError = document.getElementById('errorPass');
    const connexionForm = document.getElementById('connexionForm');

    connexionFormButton.addEventListener('click', function (e) {
        e.preventDefault();
        let textEmail = connexionEmail.value;
        let textPass = connexionPass.value;
        const emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        const regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,20}$/; // 8 characteres min 20 max, 1 MAJ, 1 min, 1 chiffre
        let emailOk = true;
        let passOk = true;
        if(!textEmail.match(emailRegex)) {
            connexionEmailError.textContent = 'Votre email est invalide';
            emailOk = false;
        } else {
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    if (this.responseText == 'notok') {
                        connexionEmailError.textContent = "Votre email n'est pas reconnu";
                        emailOk = false;
                    } else {
                        emailOk = true;
                    }
                }
            }
            xhttp.open("POST", "../../src/controllers/connexion.php", false);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send('verifmail=' + textEmail);
        }

        if (emailOk) {
            connexionEmailError.textContent = '\xa0';
            if(!textPass.match(regexPassword)) {
                connexionPassError.textContent = 'Votre mot de passe est invalide';
                passOk = false;
            } else {
                let xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        if (this.responseText == 'notok') {
                            connexionPassError.textContent = 'Votre mot de passe est erroné';
                            passOk = false;
                        } else {
                            passOk = true;
                        }
                    } 
                }
                xhttp.open("POST", "../../src/controllers/connexion.php", false);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send('verifpass=' + textPass + '&email=' + textEmail);
            }
        }

        if (passOk && emailOk) {
            connexionPassError.textContent = '\xa0';
            connexionForm.submit();
        }
    });
}