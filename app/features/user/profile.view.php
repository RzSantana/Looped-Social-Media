<?php

use Core\View\View;
?>

<main id="profile">
    <section class="header">
        <div class="profile-image">
            <div class="image">
                <?php $user['photoProfile'] = 'https://picsum.photos/seed/profile' . $user['id'] . '/' . 100 ?>
                <img src="<?= $user['photoProfile'] ?>" alt="Foto de perfil">
            </div>
        </div>

        <div class="profile-stats">
            <div class="stat">
                <?= $followersCount ?>
                <span>Post</span>
            </div>
            <div class="stat">
                <?= $followersCount ?>
                <span>Seguidores</span>
            </div>
            <div class="stat">
                <?= $followingCount ?>
                <span>Siguiendo</span>
            </div>
        </div>

        <div class="profile-info">
            <h1><?= $user['user'] ?></h1>
            <p><?= $user['description'] ?? 'No tienes ninguna descripcion, edita tu perfil para añadir una.' ?></p>

            <?php if (!$isOwnProfile): ?>
                <div class="profile-actions">
                    <?php if ($isFollowing): ?>
                        <form action="/unfollow/<?= $user['id'] ?>" method="POST" class="unfollow">
                            <button type="submit" class="btn btn-secondary">Dejar de seguir</button>
                        </form>
                    <?php else: ?>
                        <form action="/follow/<?= $user['id'] ?>" method="POST">
                            <button type="submit" class="btn btn-primary">Seguir</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
        <?php if ($isOwnProfile): ?>
            <div class="continer-owner-actions">
                <div class="container-edit">
                    <a href="/profile/edit" class="button">Editar Perfil</a>
                </div>
                <div class="container-logout">
                    <a href="/logout" class="button">Cerrar Sessión</a>
                </div>
            </div>
        <?php endif ?>
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