<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Satu Kasir POS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            text-align: center;
        }
        .logo {
            width: 120px;
            height: 120px;
            background: #EA580C;
            border-radius: 30px;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: white;
            font-weight: bold;
        }
        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        h2 {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .version {
            color: #EA580C;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .download-btn {
            display: inline-block;
            background: #EA580C;
            color: white;
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            margin-bottom: 20px;
        }
        .download-btn:hover {
            background: #C2410C;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.3);
        }
        .features {
            margin-top: 30px;
            text-align: left;
        }
        .feature-item {
            padding: 10px 0;
            color: #666;
            display: flex;
            align-items: center;
        }
        .feature-item:before {
            content: "✓";
            color: #10B981;
            font-weight: bold;
            margin-right: 10px;
        }
        .help {
            margin-top: 30px;
            padding: 20px;
            background: #F3F4F6;
            border-radius: 10px;
        }
        .help-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .whatsapp {
            color: #EA580C;
            font-weight: bold;
        }
        .size {
            color: #999;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">S</div>
        <h1>Satu Kasir POS</h1>
        <h2>Point of Sale untuk UMKM</h2>
        <p class="version">Version 1.2</p>
        
        <a href="{{ route('download.apk') }}" class="download-btn">
            📥 Download APK
        </a>
        <p class="size">69 MB | Android Tablet</p>
        
        <div class="features">
            <div class="feature-item">Manajemen Produk & Kategori</div>
            <div class="feature-item">Transaksi & Checkout</div>
            <div class="feature-item">Laporan Penjualan</div>
            <div class="feature-item">Receipt Preview</div>
            <div class="feature-item">Multi User (Owner & Kasir)</div>
            <div class="feature-item">Tax & Discount Settings</div>
        </div>
        
        <div class="help">
            <p class="help-title">Butuh Bantuan?</p>
            <p>WhatsApp: <span class="whatsapp">6285156868501</span></p>
        </div>
    </div>
</body>
</html>