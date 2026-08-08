<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, sans-serif; background: linear-gradient(160deg, #0d1b2e, #1e3a5f); min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 20px; }
        .wrap { max-width: 440px; }
        .code { font-size: 96px; font-weight: 800; color: rgba(255,255,255,.1); line-height: 1; margin-bottom: 0; }
        .icon { font-size: 56px; color: #c9963a; margin-bottom: 16px; }
        h1 { font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 10px; }
        p { color: rgba(255,255,255,.6); font-size: 15px; line-height: 1.7; margin-bottom: 28px; }
        .btn-home { display: inline-flex; align-items: center; gap: 8px; background: #c9963a; color: #fff; padding: 12px 28px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700; transition: .2s; }
        .btn-home:hover { background: #b8832a; transform: translateY(-2px); color: #fff; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="code">404</div>
        <div class="icon"><i class="bi bi-compass"></i></div>
        <h1>Page Not Found</h1>
        <p>The page you're looking for doesn't exist or may have been moved. Let's get you back on track.</p>
        <a href="<?= Helpers\Helper::base() ?>" class="btn-home">
            <i class="bi bi-arrow-left"></i> Back to Events
        </a>
    </div>
</body>
</html>
