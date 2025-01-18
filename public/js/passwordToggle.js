document.addEventListener('DOMContentLoaded', function () {
    const toggleLinks = document.querySelectorAll('.visibility')

    toggleLinks.forEach((link) => {
        link.addEventListener('click', function (e) {
            e.preventDefault() // Prevenir la navegación del enlace

            // Encontrar el input de contraseña relacionado (el elemento previo al enlace)
            const parent = this.parentElement.parentElement
            const passwordInput = parent.querySelector('input')

            // Cambiar el tipo de input
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text'
                this.textContent = 'Ocultar'
            } else {
                passwordInput.type = 'password'
                this.textContent = 'Mostrar'
            }
        })
    })
})
