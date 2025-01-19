<main>
    <?php

    use Core\View\View;

    if (isset($users)): ?>
        <section class="search-results">
            <h2>Resultados de búsqueda</h2>
            <?php if (empty($users)): ?>
                <span class="not-results">No se encontraron usuarios que coincidan con tu búsqueda.</span>
            <?php else: ?>
                <div class="users-grid">
                    <?php foreach ($users as $user): ?>
                        <a href="/user/<?= $user['id'] ?>" class="user-card">
                            <?php $user['photoProfile'] = 'https://picsum.photos/seed/profile' . $user['id'] . '/' . 100 ?>
                            <div class="photo-profile">
                                <img src="<?= $user['photoProfile'] ?>" alt="">
                            </div>
                            <span class="user-info">
                                <span><?= $user['user'] ?></span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <section id="posts">
            <?php foreach ($posts as $post): ?>
                <?php $post['text'] = substr($post['text'], 0, 100) . (strlen($post['text']) > 100 ? '...' : ''); ?>
                <?php $post['photo'] = 'https://picsum.photos/seed/post' . $post['id'] . '/' . 800 ?>
                <?php $post['photoProfile'] = 'https://picsum.photos/seed/profile' . $post['user_id'] . '/' . 100 ?>
                <?= View::component('post', ['post' => $post]) ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>