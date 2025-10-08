document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('exampleInputPassword');
    const passwordConfirmInput = document.getElementById('exampleRepeatPassword');
    const passwordError = document.getElementById('password-error');
    const passwordErrorInputs = document.getElementById('password-error-inputs');
    const showPasswordIcons = document.querySelectorAll('.c-password-show-1, .c-password-show-2');

    // Function to toggle password visibility
    function togglePasswordVisibility(input) {
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }

    // Add event listeners to show password icons
    showPasswordIcons.forEach(function(icon) {
        icon.addEventListener('click', function() {
            const input = icon.parentElement.querySelector('input');
            icon.querySelector('i').classList.toggle('fa-eye-slash');
            icon.querySelector('i').classList.toggle('fa-eye');
            togglePasswordVisibility(input);
        });
    });

    // Function to check if passwords match
    function passwordsMatch() {
        return passwordInput.value === passwordConfirmInput.value;
    }

   
        function validatePasswordFormat(password) {
        // Password must be at least 8 characters long and contain at least one alphabet, one number, and one or more special characters
        //const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&]+)[A-Za-z\d@$!%*?&]{8,}$/;
        const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&^#])[A-Za-z\d@$!%*?&^#]{8,}$/;
        return regex.test(password);
    }

    // Function to handle input change event on password input field
    function handlePasswordInputChange() {
        if (validatePasswordFormat(passwordInput.value)) {
            passwordErrorInputs.style.display = 'none';
             $('.reg-apply').prop('disabled', false);
        } else {
            passwordErrorInputs.style.display = 'block';
            passwordErrorInputs.textContent = "Password must be at least 8 characters long. Include a mix of  alphabets,  numbers, special characters (!, @, #, $, %, ^, &).";
             $('.reg-apply').prop('disabled', true);
        }
    }
    
    function handlePasswordConfirmChange() {
        if (passwordsMatch()) {
            passwordError.style.display = 'none';
            
        } else {
            passwordError.style.display = 'block';
             $('.reg-apply').prop('disabled', true);
        }
    }

    // Add event listener to password input field
    passwordInput.addEventListener('input', handlePasswordInputChange);
    
     // Add event listener to password confirm input
    passwordConfirmInput.addEventListener('input', handlePasswordConfirmChange);
});
