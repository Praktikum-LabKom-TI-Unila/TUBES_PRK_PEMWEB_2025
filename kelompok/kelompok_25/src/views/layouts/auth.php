<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Inventory Manager' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-gradient-to-br from-blue-600 via-purple-600 to-blue-700 min-h-screen">
    <?= $content ?? '' ?>
    
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
