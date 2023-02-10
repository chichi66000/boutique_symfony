// valider les données entrants
window.addEventListener('load', validate )

function validate (e) {
    let nomValid = prenomValid = pseudoValid = telValid = adresseValid = villeValid = codePostalValid = passwordValid = false;
console.log(pseudoValid);
    

    let pseudo = document.forms['modifProfil']['pseudo'];
    if(pseudo.value.length > 50) {
        pseudo.nextElementSibling.textContent = "Pas plus de 50 characters";
    }
    else {
        pseudo.nextElementSibling.textContent ="";
        pseudoValid = true;
    }
    console.log("g ", pseudoValid);
    console.log(pseudo.value);
    if ( pseudoValid ) {
        console.log("bien");
        return true;
    }
    else {
        console.log("pas ");
        e.preventDefault();
    }
} 
let modifInfo = document.getElementById('modifInfo');
modifInfo.addEventListener('click', validate)
