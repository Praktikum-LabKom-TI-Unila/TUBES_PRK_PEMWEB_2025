<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory Manager' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">
    <?php include ROOT_PATH . '/views/partials/navbar.php'; ?>

    <div class="flex min-h-screen pt-16">
        <?php include ROOT_PATH . '/views/partials/sidebar.php'; ?>

        <main class="flex-1 bg-white md:ml-64">
            <?= $content ?? '' ?>
        </main>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
