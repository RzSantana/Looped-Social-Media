<?php

$post['photo'] = 'https://picsum.photos/seed/post' . $post['id'] . '/' . 800;
$post['photoProfile'] = 'https://picsum.photos/seed/profile' . $post['user_id'] . '/' . 100;
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

        <div class="content-post">
            <div class="user">
                <a href="/user/<?= $post['user_id'] ?>" class="photo-profile">
                    <?php if (isset($post['photoProfile'])): ?>
                        <img src="<?= $post['photoProfile'] ?>" alt="">
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
        </div>
    </article>
</main>
