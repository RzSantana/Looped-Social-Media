<main>
    <div class="auth-container">
        <div class="title-container">
            <h1>Iniciar Sesión</h1>
            <span>Ingresa para gestionar su cuenta</span>
        </div>


        <form action="/login" method="POST">
            <div class="inputs-container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="input">
                    <label for="username">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z" />
                        </svg>
                    </label>
                    <input type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required>
                </div>

                <div class="input">
                    <label for="password">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17l0 80c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24l0-40 40 0c13.3 0 24-10.7 24-24l0-40 40 0c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z" />
                        </svg>
                    </label>
                    <input type="password"
                        id="password"
                        name="password"
                        required>
                    <a href="#">Show</a>
                </div>

                <div class="remember-container">
                    <input type="checkbox" name="remember" id="">
                    <label for="remember">Recuerdame</label>
                </div>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>

    <div class="register-container">
        <span>¿No tiene cuenta?</span>
        <a href="/register">Registrate</a>
    </div>
</main>