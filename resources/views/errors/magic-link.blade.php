<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Tidak Valid</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fc; color: #333;
        }
        .card {
            text-align: center; padding: 3rem 2rem;
            background: white; border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            max-width: 400px; width: 90%;
        }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
        h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        p { color: #64748b; font-size: 0.95rem; line-height: 1.6; }
        a { display: inline-block; margin-top: 1.5rem; padding: 0.75rem 2rem; background: #7c3aed; color: white; text-decoration: none; border-radius: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">🔒</div>
        <h1>Link Tidak Valid</h1>
        <p>Link akses yang Anda gunakan tidak valid atau sudah kadaluarsa. Hubungi admin untuk mendapatkan link baru.</p>
        <a href="{{ route('login') }}">Ke Halaman Login</a>
    </div>
</body>
</html>
