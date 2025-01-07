<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'App' ?></title>
    <link rel="stylesheet" href="/styles/global.css">
</head>
<body>
    <header>
        Encabezado Comun
</header>

    <main>
        <?= $slot ?>
    </main>

    <footer>
        Footer Comun
    </footer>
</body>
</html>