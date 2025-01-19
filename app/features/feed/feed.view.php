<?php

use Core\View\View;
?>

<main>
    <?php if (!empty($following)): ?>
        <section>
            <div class="following">
                <?php foreach ($following as $user): ?>
                    <a href="/user/<?= $user['id'] ?>">
                    <?php $user['photoProfile'] = 'https://picsum.photos/seed/profile' . $user['id'] . '/' . 100 ?>
                        <img src="<?= $user['photoProfile'] ?>" alt="">
                        <div><?= $user['user'] ?></div>
                    </a>
                <?php endforeach ?>
            </div>
        </section>

    <?php else: ?>
        <section class="not-following">
            <span>Comienza a seguir a otro usuario para poder visualizarlos</span>
        </section>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <section class="not-posts">
            <h2>No hay publicaciones para mostrar</h2>
            <span>Sigue a otros usuarios para ver sus publicaciones en tu feed</span>
        </section>
    <?php else: ?>
        <section id="posts">
            <?php foreach ($posts as $post): ?>
                <?php $post['photo'] = 'https://picsum.photos/seed/post' . $post['id'] . '/' . 800 ?>
                <?php $post['photoProfile'] = 'https://picsum.photos/seed/profile' . $post['user_id'] . '/' . 100 ?>
                <?= View::component('post', ['post' => $post]) ?>
            <?php endforeach ?>
        </section>
    <?php endif ?>
</main>