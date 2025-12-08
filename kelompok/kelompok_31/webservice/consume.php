<?php
/**
 * Contoh Konsumsi API
 * Dikerjakan oleh: Anggota 4
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Web Service API</h2>
        <button class="btn btn-primary" onclick="testAPI()">Test GET Request</button>
        <pre id="result" class="mt-3 p-3 bg-light"></pre>
    </div>

    <script>
        function testAPI() {
            // TODO Anggota 4: Implementasi consume API
            document.getElementById('result').textContent = 'Testing...';
        }
    </script>
</body>
</html>
