//show/ hide password && changer icon eye-off / eye-on avec le clic sur icon eye password
let eyeConfirm = document.getElementById('eyeConfirm');
eyeConfirm.addEventListener('click', function () { 
    let passwordConfirm = document.getElementById('passwordConfirm');
    
    if (passwordConfirm.type == 'password') {
            passwordConfirm.type = 'text';
        }
        else {
            passwordConfirm.type = 'password';
        }
})

//show/ hide password && changer icon eye-off / eye-on avec le clic sur icon eye password
let eye = document.getElementById('eye');

eye.addEventListener('click', function () {
    let password = document.getElementById('password');
    if (password.type == 'password') {
            password.type = 'text';

        }
        else {
            password.type = 'password';
        }
})


