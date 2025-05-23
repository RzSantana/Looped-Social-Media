<main>
    <h1 class="logo">Looped</h1>

    <div class="auth-container">
        <div class="title-container">
            <h1>Iniciar Sesión</h1>
            <span>Ingresa para gestionar su cuenta</span>
        </div>

        <form action="/login" method="POST">
            <div class="inputs-container">
                <?php if (isset($error)): ?>
                    <div class="error-message">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="input-auth">
                    <label for="username">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z" />
                        </svg>
                    </label>
                    <input type="text"
                        class="<?= isset($errors['username']) ? 'input-error' : '' ?>"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($username) ?>"
                        placeholder="Nombre de usuario o email">
                    <div class="info-input">
                        <?php if (isset($errors['username'])): ?>
                            <div class="icon-error">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($errors['username'])): ?>
                        <div class="error-message">
                            <?= $errors['username'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-auth">
                    <label for="password">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17l0 80c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24l0-40 40 0c13.3 0 24-10.7 24-24l0-40 40 0c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z" />
                        </svg>
                    </label>
                    <input type="password"
                        class="<?= isset($errors['password']) ? 'input-error' : '' ?>"
                        id="password"
                        name="password"
                        placeholder="Contraseña"
                        value="<?= htmlspecialchars($password) ?>">
                    <div class="info-input">
                        <a class="visibility">Mostrar</a>
                        <?php if (isset($errors['password'])): ?>
                            <div class="icon-error">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-message">
                            <?= $errors['password'] ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="remember-container">
                    <input type="checkbox" name="remember" class="input-checkbox">
                    <label for="remember">Recuerdame</label>
                </div>
            </div>

            <button type="submit" name="action" value="login">Iniciar Sesión</button>
        </form>
    </div>

    <div class="register-container">
        <span>¿No tiene cuenta?</span>
        <a href="/register">Registrate</a>
    </div>
</main>
