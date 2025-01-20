<?php

use Core\View\View;
?>

<main id="profile">
    <section class="header">
        <div class="profile-image padding-max">
            <div class="image">
                <?php $user['photoProfile'] = 'https://picsum.photos/seed/profile' . $user['id'] . '/' . 100 ?>
                <img src="<?= $user['photoProfile'] ?>" alt="Foto de perfil">
            </div>
        </div>

        <div class="profile-info">
            <h1><?= $user['user'] ?></h1>
            <p><?= $user['description'] ?? 'No tienes ninguna descripcion, edita tu perfil para añadir una.' ?></p>
        </div>
        <div class="continer-owner-actions">
            <div class="container-confirm">
                <a href="/profile/edit" class="button">Confirmar</a>
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
                    <?= View::component('post', ['post' => $post]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>