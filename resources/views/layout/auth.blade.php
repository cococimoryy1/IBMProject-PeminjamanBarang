<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Barang - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a1d37 0%, #1e3a8a 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 10;
        }
        h1 {
            color: #0a1d37;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .time-info {
            color: #1e3a8a;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        input, button {
            width: 100%;
            padding: 0.75rem;
            border-radius: 25px;
            margin: 0.5rem 0;
            font-size: 1rem;
        }
        input {
            border: 1px solid #1e3a8a;
            background: #f0f4f8;
            color: #0a1d37;
            transition: all 0.3s ease;
        }
        input:focus {
            background: #e0e7ff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.3);
            outline: none;
        }
        button {
            border: none;
            background: linear-gradient(90deg, #0a1d37 0%, #1e3a8a 100%);
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 29, 55, 0.5);
        }
        .switch {
            margin-top: 1rem;
            color: #1e3a8a;
        }
        .switch a {
            color: #0a1d37;
            text-decoration: none;
            font-weight: 600;
        }
        .switch a:hover {
            text-decoration: underline;
        }
        .bg-decoration {
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(30, 58, 138, 0.2);
            border-radius: 50%;
            filter: blur(60px);
        }
        .bg-decoration.top-right {
            top: -120px;
            right: -120px;
        }
        .bg-decoration.bottom-left {
            bottom: -120px;
            left: -120px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 2rem;
                margin: 10px;
            }
            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="bg-decoration top-right"></div>
    <div class="bg-decoration bottom-left"></div>
    <div class="container">
        <h1>@yield('title')</h1>
        <div class="time-info">
            Tanggal & Waktu: {{ date('d F Y, H:i A', strtotime('2025-07-24 10:33:00')) }} WIB
        </div>
        @yield('form')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
