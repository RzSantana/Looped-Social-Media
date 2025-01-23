<?php

use Core\Auth\Auth;
use Core\View\View;

$post['photo'] = 'https://picsum.photos/seed/post' . $post['id'] . '/' . 1920;
$post['photoProfile'] = 'https://picsum.photos/seed/profile' . $post['user_id'] . '/' . 100;
$post['photoCurrentProfile'] = 'https://picsum.photos/seed/profile' . Auth::id() . '/' . 100;

$date = new DateTime($post['date']);
$post['date'] = $date->format('d M Y, H:i');
?>

<main id="post-view">
    <article>
        <div class="image">
            <?php if (isset($post['photo'])): ?>
                <img src="<?= $post['photo'] ?>" alt="">
            <?php else: ?>
                <div class="not-image"></div>
            <?php endif ?>
        </div>

        <div class="content">
            <div class="user">
                <a href="/user/<?= $post['user_id'] ?>" class="photo-profile">
                    <?php if (isset($post['photoProfile'])): ?>
                        <img src="<?= $post['photoProfile'] ?>" alt="Foto de perfil del usuario">
                    <?php else: ?>
                        <div class="not-image"></div>
                    <?php endif ?>
                </a>
                <div class="data">
                    <div>
                        <a href="/user/<?= $post['user_id'] ?>" class="username">
                            <?= $post['username'] ?? 'Nombre de usuario' ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="content-post">
                <div class="actions">
                    <div class="likes">
                        <span><?= $post['likes_count'] ?? 0 ?></span>
                        <?= View::component('icons/like', ['id' => $post['id'], 'liked' => $post['user_liked']]) ?>
                    </div>
                    <div class="dislikes">
                        <span><?= $post['dislikes_count'] ?? 0 ?></span>
                        <?= View::component('icons/dislike', ['id' => $post['id'], 'disliked' => $post['user_disliked'] ?? false]) ?>
                    </div>
                </div>

                <div class="content-description">
                    <div class="description">
                        <?= $post['text'] ?? 'Descripción' ?>
                    </div>
                    <div class="date">
                        <?= $post['date'] ?>
                    </div>
                </div>

                <?php if (!empty($post['comments']) && is_array($post['comments'])): ?>
                    <div class="comments-list">
                        <?php foreach ($post['comments'] as $comment): ?>
                            <?= View::component('comment', [
                                'text' => $comment['text'],
                                'username' => $comment['username'],
                            ]) ?>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <div class="no-comments">
                        <span>No hay comentarios</span>
                    </div>
                <?php endif ?>
            </div>
            <div class="input-comment">
                <div class="content-photo-profile">
                    <a href="/user/<?= Auth::id() ?>" class="photo-profile">
                        <?php if (isset($post['photoCurrentProfile'])): ?>
                            <img src="<?= $post['photoCurrentProfile'] ?>" alt="Tu foto de perfil">
                        <?php else: ?>
                            <div class="not-image"></div>
                        <?php endif ?>
                    </a>
                </div>

                <form action="/comment" method="post">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <input id="comment" type="text" name="text" placeholder="Escribe un comentario..." autocomplete="off">
                    <button id="btn-comment" type="submit">Post</button>
                </form>
            </div>
        </div>
    </article>
</main>
