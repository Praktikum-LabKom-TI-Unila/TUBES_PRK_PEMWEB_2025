<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Iklan Tutor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- BOOTSTRAP -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #FF6B35;
        --primary-dark: #E55A2B;
        --secondary-color: #1B4965;
        --accent-color: #9FD3C7;
        --success-color: #48bb78;
        --warning-color: #FF6B35;
        --danger-color: #f56565;
        --dark-color: #0A1628;
        --light-bg: #f5f5f5;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        --hover-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        --border-radius-lg: 1rem;
        --border-radius-md: 0.75rem;
    }

    body {
        background: var(--light-bg);
        font-family: 'Poppins', sans-serif;
    }

    .card-custom {
        background: white;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--card-shadow);
        padding: 30px;
        transition: 0.3s ease;
    }

    .card-custom:hover {
        box-shadow: var(--hover-shadow);
    }

    .btn-primary {
        background: var(--primary-color) !important;
        border: none;
        border-radius: var(--border-radius-md);
    }

    .btn-primary:hover {
        background: var(--primary-dark) !important;
    }

    label {
        font-weight: 600;
        color: var(--secondary-color);
    }

    .form-control,
    .form-select {
        border-radius: var(--border-radius-md);
        padding: 10px 14px;
    }

    .page-title {
        font-size: 26px;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 20px;
    }

</style>

</head>

<body>

<div class="container py-5">
    
    <h2 class="page-title mb-4">Buat Iklan Tutor</h2>

    <div class="card-custom">

        <form action="proses_iklan.php" method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Judul Iklan</label>
                <input type="text" name="judul" class="form-control" placeholder="Contoh: Tutor Matematika Berpengalaman" required>
            </div>

            <div class="mb-3">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="5" placeholder="Tuliskan deskripsi lengkap tentang diri Anda..." required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Mata Pelajaran</label>
                    <select name="subject" class="form-select" required>
                        <option value="">-- pilih --</option>
                        <option>Matematika</option>
                        <option>Bahasa Inggris</option>
                        <option>IPA</option>
                        <option>IPS</option>
                        <option>Programming</option>
                        <option>Fisika</option>
                        <option>Kimia</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Harga Per Jam (Rp)</label>
                    <input type="number" name="harga" class="form-control" placeholder="50000" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Lokasi / Kota</label>
                <input type="text" name="kota" class="form-control" placeholder="Contoh: Jakarta Selatan" required>
            </div>

            <div class="mb-3">
                <label>Pengalaman (Tahun)</label>
                <input type="number" name="pengalaman" class="form-control" placeholder="contoh: 2" required>
            </div>

            <div class="mb-3">
                <label>Foto Profil Tutor</label>
                <input type="file" name="foto" class="form-control" accept="image/*" required>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    Simpan Iklan
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
