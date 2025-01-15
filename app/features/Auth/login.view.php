<div class="auth-container">
    <h1>Iniciar Sesión</h1>
    
    <?php if (isset($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST">
        <div class="form-group">
            <label for="username">Usuario:</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                   required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   required>
        </div>

        <button type="submit">Iniciar Sesión</button>
    </form>
</div>