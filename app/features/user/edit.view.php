<?php

use Core\View\View;
?>

<main id="profile" class="profile-edit">
    <section class="header">
        <div class="profile-image">
            <div class="image">
                <?php $user['photoProfile'] = 'https://picsum.photos/seed/profile' . $user['id'] . '/' . 100 ?>
                <img src="<?= $user['photoProfile'] ?>" alt="Foto de perfil">
            </div>
        </div>

        <div>
            <form action="/profile/edit" method="post" id="form-edit" class="profile-info">
                <input type="text"
                    name="username"
                    value="<?= $user['user'] ?>"
                    class="input"
                    placeholder="Nombre de usuario">

                <input type="text"
                    name="email"
                    value="<?= $user['email'] ?>"
                    class="input"
                    placeholder="Correo electronico">

                <textarea
                    name="description"
                    class="textarea"
                    placeholder="No tienes ninguna descripcion, edita tu perfil para añadir una"><?= isset($user['description']) ? $user['description'] . '</textarea>' : '</textarea>' ?>
            </form>
        </div>
        <div class="continer-owner-actions">
            <div class="container-confirm">
                <button type="submit" class="button" form="form-edit">Confirmar</button>
            </div>
            <div class="container-cancel">
                <a href="/profile" class="button">Cancelar</a>
            </div>
        </div>
    </section>

    <section class="content-posts">
        <h2>Publicaciones</h2>

        <?php if (empty($posts)): ?>
            <div class="not-posts">
                <p>No hay publicaciones aún.</p>
            </div>
        <?php else: ?>
            <div id="posts">
                <?php foreach ($posts as $post): ?>
                    <?php $post['photo'] = 'https://picsum.photos/seed/post' . $post['id'] . '/' . 800 ?>
                    <?php $post['photoProfile'] = 'https://picsum.photos/seed/profile' . $post['user_id'] . '/' . 100 ?>
                    <div class="post">
                        <input type="checkbox"
                            value="<?= $post['id'] ?>"
                            name="selected_posts[]"
                            class="post-select input-checkbox"
                            form="form-edit">
                        <?= View::component('post', ['selected' => false, 'post' => $post]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
