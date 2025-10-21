document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("lydopers_pass");
    const confirmInput = document.getElementById("lydopers-pass-confirm");
    const passwordNote = document.getElementById("password-note");
    const confirmNote = document.getElementById("confirm-password-note");
    const createButton = document.querySelector(
        ".step-3 button[type='submit']"
    );

    function validatePassword() {
        const val = passwordInput.value;
        let message = "";

        if (val.length === 0) {
            // Kung walang laman, wag ipakita note
            passwordNote.classList.add("hidden");
            passwordInput.classList.remove("border-red-500");
            return;
        }

        if (val.length < 8) {
            message = "Password must be at least 8 characters.";
        } else if (!/[A-Z]/.test(val)) {
            message = "Password must contain at least 1 uppercase letter.";
        } else if (!/[a-z]/.test(val)) {
            message = "Password must contain at least 1 lowercase letter.";
        } else if (!/[0-9]/.test(val)) {
            message = "Password must contain at least 1 number.";
        } else if (!/[@$!%*?&]/.test(val)) {
            message =
                "Password must contain at least 1 special character (@$!%*?&).";
        }

        if (message) {
            passwordNote.textContent = message;
            passwordNote.classList.remove("hidden");
            passwordInput.classList.add("border-red-500");
        } else {
            passwordNote.classList.add("hidden");
            passwordInput.classList.remove("border-red-500");
        }

        validateConfirmPassword();
    }

    function validateConfirmPassword() {
        if (
            confirmInput.value.length > 0 &&
            confirmInput.value !== passwordInput.value
        ) {
            confirmNote.classList.remove("hidden");
            confirmInput.classList.add("border-red-500");
        } else {
            confirmNote.classList.add("hidden");
            confirmInput.classList.remove("border-red-500");
        }

        // Disable Create button kung may mali
        const hasError =
            !passwordNote.classList.contains("hidden") ||
            !confirmNote.classList.contains("hidden");
        createButton.disabled = hasError;
    }

    passwordInput.addEventListener("input", validatePassword);
    confirmInput.addEventListener("input", validateConfirmPassword);

    // Toggle password visibility
    document.getElementById("togglePassword").addEventListener("click", () => {
        passwordInput.type =
            passwordInput.type === "password" ? "text" : "password";
    });
    document
        .getElementById("toggleConfirmPassword")
        .addEventListener("click", () => {
            confirmInput.type =
                confirmInput.type === "password" ? "text" : "password";
        });
});
