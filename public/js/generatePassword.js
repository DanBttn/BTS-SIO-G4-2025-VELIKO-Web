function togglePasswordVisibility(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
}

function generatePassword(fieldId, confirmFieldId) {
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    const specialChars = "&#!@^$¨,;/§-_<>:?%*";
    let password = "";

    // Generate a base password with 10 random alphanumeric characters
    for (let i = 0; i < 10; i++) {
        const randomIndex = Math.floor(Math.random() * charset.length);
        password += charset[randomIndex];
    }

    // Add 2 random special characters to ensure they are included
    for (let i = 0; i < 2; i++) {
        const randomIndex = Math.floor(Math.random() * specialChars.length);
        password += specialChars[randomIndex];
    }

    // Shuffle the password to distribute special characters randomly
    password = password.split('').sort(() => 0.5 - Math.random()).join('');

    // Set the generated password in both fields
    document.getElementById(fieldId).value = password;
    document.getElementById(confirmFieldId).value = password;
}