<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>peminjaman barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #1e3a8a 0%, #4b6cb7 100%);
      color: #333;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .header {
      background: #ffffff;
      padding: 2rem 0;
      text-align: center;
      border-radius: 0 0 15px 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .header h1 {
      color: #1e3a8a;
      font-size: 2.5rem;
      font-weight: 700;
    }
    .header p {
      color: #4b6cb7;
      font-size: 1.2rem;
    }
    .btn-custom {
      background: linear-gradient(90deg, #1e3a8a 0%, #4b6cb7 100%);
      color: #ffffff;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 25px;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(30, 58, 138, 0.4);
    }
    .feature-section {
      background: #f8f9fa;
      padding: 3rem 0;
      text-align: center;
    }
    .feature-card {
      background: #ffffff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }
    .feature-card img {
      max-width: 100px;
      margin-bottom: 1rem;
    }
    .feature-card h3 {
      color: #1e3a8a;
      font-size: 1.5rem;
    }
    .feature-card p {
      color: #4b6cb7;
    }
    .testimonial-section {
      padding: 3rem 0;
      text-align: center;
      background: #ffffff;
    }
    .testimonial-quote {
      font-size: 1.2rem;
      color: #333;
      font-style: italic;
      margin-bottom: 1rem;
    }
    .testimonial-author {
      color: #4b6cb7;
      font-weight: 500;
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="container">
      <h1>peminjaman barang</h1>
      <p class="lead">Solusi terbaik untuk meminjam dan memantau barang dengan mudah</p>
      <a href="{{ route('login') }}" class="btn btn-custom">Pinjam Sekarang</a>
    </div>
  </header>

  <section class="feature-section">
    <div class="container">
      <div class="row">
<div class="col-md-4">
  <div class="feature-card">
    <i class="bi bi-book" style="font-size: 3rem; color: #1e3a8a;"></i>
    <h3>Katalog Barang</h3>
    <p>Jelajahi berbagai barang yang tersedia untuk dipinjam.</p>
  </div>
</div>
<div class="col-md-4">
  <div class="feature-card">
    <i class="bi bi-globe" style="font-size: 3rem; color: #1e3a8a;"></i>
    <h3>Akses Kapan Saja</h3>
    <p>Pinjam barang dari mana saja melalui platform ini.</p>
  </div>
</div>
<div class="col-md-4">
  <div class="feature-card">
    <i class="bi bi-headset" style="font-size: 3rem; color: #1e3a8a;"></i>
    <h3>Dukungan Peminjaman</h3>
    <p>Dapatkan bantuan untuk proses peminjaman Anda.</p>
  </div>
</div>
      </div>
    </div>
  </section>

  <section class="testimonial-section">
    <div class="container">
      <blockquote class="testimonial-quote">
        "Platform ini sangat membantu saya meminjam barang dengan cepat dan aman!"
      </blockquote>
      <p class="testimonial-author">- Budi Santoso</p>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
