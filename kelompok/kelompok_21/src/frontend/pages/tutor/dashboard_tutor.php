<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tutor - ScholarBridge</title>

    <!-- BOOTSTRAP 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            background-color: var(--light-bg);
            font-family: 'Inter', sans-serif;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .featured-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: var(--border-radius-lg);
            padding: 30px;
            color: white;
            box-shadow: var(--card-shadow);
        }

        .featured-tag {
            background-color: rgba(255,255,255,0.25);
            padding: 4px 10px;
            border-radius: 0.5rem;
            font-size: 12px;
        }

        .tutor-card {
            border-radius: var(--border-radius-md);
            box-shadow: var(--card-shadow);
            transition: 0.2s;
            background: white;
        }
        .tutor-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--hover-shadow);
        }

        .subject-tag {
            background-color: #e6f1f7;
            color: var(--secondary-color);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary-custom:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

    </style>
</head>
<body>

<!-- ================= HEADER ================= -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3 px-4">
    <a class="navbar-brand fw-bold" href="#">
        <img src="https://cdn-icons-png.flaticon.com/512/3917/3917754.png" width="32" class="me-2">
        ScholarBridge
    </a>

    <div class="ms-auto d-flex align-items-center gap-3">
        <i class="fa-regular fa-bell fa-lg"></i>

        <div class="d-flex align-items-center">
            <img src="https://i.pravatar.cc/150?img=12" width="40" class="rounded-circle me-2">
            <div>
                <div class="fw-semibold">Andi Pratama</div>
                <div class="text-muted" style="font-size: 13px;">Siswa SMA</div>
            </div>
        </div>
    </div>
</nav>


<!-- ================= CONTENT ================= -->
<div class="container py-5">

    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2 class="page-title">Daftar Tutor</h2>

        <!-- Search Bar -->
        <div class="input-group" style="width: 350px;">
            <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control" placeholder="Cari tutor, mata pelajaran...">
        </div>
    </div>

    <!-- FEATURED TUTOR -->
    <div class="featured-card mb-5 d-flex align-items-center">

        <img src="https://i.pravatar.cc/150?img=65" width="90" height="90" class="rounded-circle border border-3 border-white">

        <div class="ms-4">
            <div class="featured-tag">Top Rated Tutor</div>
            <h3 class="mt-2 fw-bold">Dr. Sarah Mitchell</h3>

            <div class="d-flex align-items-center mb-1">
                ⭐⭐⭐⭐⭐ <span class="ms-2">(284 ulasan)</span>
            </div>

            <div class="d-flex gap-2 mt-2">
                <div class="subject-tag">Matematika</div>
                <div class="subject-tag">Fisika</div>
                <div class="subject-tag">Persiapan SAT</div>
            </div>
        </div>

        <div class="ms-auto fs-4 fw-bold">
            Rp 180.000/jam
        </div>
    </div>


    <!-- FILTER -->
    <div class="d-flex gap-3 mb-4">
        <select class="form-select w-auto">
            <option>Semua Subjek</option>
            <option>Matematika</option>
            <option>Bahasa Inggris</option>
        </select>

        <select class="form-select w-auto">
            <option>Harga Berapa Saja</option>
            <option>Rp 50k - Rp 100k</option>
            <option>Rp 100k - Rp 200k</option>
        </select>

        <select class="form-select w-auto">
            <option>Tingkat Pengalaman</option>
            <option>Pemula</option>
            <option>Profesional</option>
        </select>

        <button class="btn btn-link text-decoration-none">Reset Filter</button>
    </div>


    <!-- LIST TUTOR -->
    <div class="row g-4">

        <!-- CARD EXAMPLE -->
        <?php 
        $tutors = [
            [
                "name" => "Emily Chen",
                "rating" => "4.8",
                "reviews" => "156",
                "city" => "Jakarta",
                "price" => "45000",
                "subjects" => ["English", "Writing", "Literature"],
                "img" => "https://i.pravatar.cc/150?img=47"
            ],
            [
                "name" => "Marcus Johnson",
                "rating" => "4.9",
                "reviews" => "203",
                "city" => "Bandung",
                "price" => "55000",
                "subjects" => ["Coding", "JavaScript", "Python"],
                "img" => "https://i.pravatar.cc/150?img=59"
            ]
        ];

        foreach ($tutors as $tutor): ?>
        
        <div class="col-md-6">
            <div class="tutor-card p-4 d-flex">

                <img src="<?= $tutor["img"] ?>" width="80" height="80" class="rounded-circle">

                <div class="ms-3 flex-grow-1">

                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold"><?= $tutor["name"] ?></h5>
                        <span class="fw-semibold">Rp <?= number_format($tutor["price"],0,",",".") ?>/jam</span>
                    </div>

                    <div class="text-warning small">
                        ⭐ <?= $tutor["rating"] ?> <span class="text-muted">(<?= $tutor["reviews"] ?> ulasan)</span>
                    </div>

                    <div class="text-muted small mb-2">📍 <?= $tutor["city"] ?></div>

                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <?php foreach ($tutor["subjects"] as $s): ?>
                            <span class="subject-tag"><?= $s ?></span>
                        <?php endforeach; ?>
                    </div>

                    <a href="#" class="btn btn-primary-custom mt-2 w-100">Lihat Profil</a>

                </div>
            </div>
        </div>

        <?php endforeach; ?>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
