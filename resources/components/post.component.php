<div class="post">
    <div class="image">
        <?php if (isset($post['photo'])): ?>
            <img src="<?= $post['photo'] ?>" alt="">
        <?php else: ?>
            <div class="not-image"></div>
        <?php endif ?>
    </div>
    <div class="info-post">
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
        <div class="actions">
            <div class="action">
                <div><?= $post['likes_count'] ?? '0' ?></div>

                <?php if (isset($post['user_liked'])) ?>
                <a class="like <?= $post['user_liked'] ? 'active' : '' ?>" href="/post/like/<?= $post['id'] ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z" />
                    </svg>
                </a>
            </div>

            <div class="action">
                <div><?= $post['dislikes_count'] ?? '0' ?></div>

                <a class="dislike <?= $post['user_disliked'] ? 'active' : '' ?>" href="/post/dislike/<?= $post['id'] ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M119.4 44.1c23.3-3.9 46.8-1.9 68.6 5.3l49.8 77.5-75.4 75.4c-1.5 1.5-2.4 3.6-2.3 5.8s1 4.2 2.6 5.7l112 104c2.9 2.7 7.4 2.9 10.5 .3s3.8-7 1.7-10.4l-60.4-98.1 90.7-75.6c2.6-2.1 3.5-5.7 2.4-8.8L296.8 61.8c28.5-16.7 62.4-23.2 95.7-17.6C461.5 55.6 512 115.2 512 185.1l0 5.8c0 41.5-17.2 81.2-47.6 109.5L283.7 469.1c-7.5 7-17.4 10.9-27.7 10.9s-20.2-3.9-27.7-10.9L47.6 300.4C17.2 272.1 0 232.4 0 190.9l0-5.8c0-69.9 50.5-129.5 119.4-141z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>