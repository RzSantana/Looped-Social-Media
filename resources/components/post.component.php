<?php

use Core\View\View;

$selected = isset($selected) ? $selected : false;
?>

<div class="post">
    <div class="image">
        <?php if ($selected): ?>
            <a href="/post/<?= $post['id'] ?>">
            <?php endif; ?>
            <?php if (isset($post['photo'])): ?>
                <img src="<?= $post['photo'] ?>" alt="">
            <?php else: ?>
                <div class="not-image"></div>
            <?php endif; ?>
            <?php if ($selected): ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="info-post">
        <div class="user">
            <div class="photo-profile">
                <?php if (isset($post['photoProfile'])): ?>
                    <img src="<?= $post['photoProfile'] ?>" alt="">
                <?php else: ?>
                    <div class="not-image"></div>
                <?php endif; ?>
            </div>
            <div class="data">
                <div>
                    <a href="/user/<?= $post['user_id'] ?>" class="username">
                        <?= $post['username'] ?? 'Nombre de usuario' ?>
                    </a>
                </div>
                <div class="description">
                    <?= $post['text'] ?? 'Descripción' ?>
                    <div class="date">
                        <?php
                        $date = new DateTime($post['date']);
                        echo $date->format('d M Y, H:i');
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <div class="action">
                <div><?= $post['likes_count'] ?? '0' ?></div>
                <?= View::component('icons/like', ['id' => $post['id'], 'liked' => $post['user_liked']]) ?>
            </div>
            <div class="action">
                <div><?= $post['dislikes_count'] ?? '0' ?></div>
                <?= View::component('icons/dislike', ['id' => $post['id'], 'disliked' => $post['user_disliked']]) ?>
            </div>
            <div class="action">
                <div><?= $post['comments_count'] ?? '0' ?></div>
                <?= View::component('icons/comment', ['id' => $post['id']]) ?>
            </div>
        </div>
    </div>
</div>
