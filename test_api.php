<?php
// Buat file ini di root folder bengkel_api untuk test API
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Bengkel API</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .endpoint { background: #f4f4f4; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .endpoint h3 { margin-top: 0; color: #333; }
        .method { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .post { background: #4CAF50; color: white; }
        .get { background: #2196F3; color: white; }
        code { background: #e0e0e0; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Test Bengkel API</h1>
    
    <h2>Authentication Endpoints</h2>
    <div class="endpoint">
        <h3><span class="method post">POST</span> /auth/register.php</h3>
        <p><strong>Params:</strong> kode_registrasi, username, password, nama_lengkap</p>
    </div>
    
    <div class="endpoint">
        <h3><span class="method post">POST</span> /auth/login.php</h3>
        <p><strong>Params:</strong> username, password</p>
    </div>
    
    <h2>Pelanggan Endpoints</h2>
    <div class="endpoint">
        <h3><span class="method post">POST</span> /pelanggan/create.php</h3>
        <p><strong>Params:</strong> nama_pelanggan, no_hp, alamat, id_admin</p>
    </div>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span> /pelanggan/read.php</h3>
    </div>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span> /pelanggan/read_single.php?id={id}</h3>
    </div>
    
    <div class="endpoint">
        <h3><span class="method post">POST</span> /pelanggan/update.php</h3>
        <p><strong>Params:</strong> id_pelanggan, nama_pelanggan, no_hp, alamat</p>
    </div>
    
    <div class="endpoint">
        <h3><span class="method post">POST</span> /pelanggan/delete.php</h3>
        <p><strong>Params:</strong> id_pelanggan</p>
    </div>
    
    <div class="endpoint">
        <h3><span class="method get">GET</span> /pelanggan/search.php?keyword={keyword}</h3>
    </div>
    
    <h2>Mobil Endpoints</h2>
    <p><em>Similar structure: create, read, read_single, update, delete, search</em></p>
    
    <h2>Servis Endpoints</h2>
    <p><em>Similar structure: create, read, read_single, update, delete, search</em></p>
    
    <hr>
    <p><strong>Base URL:</strong> <code>http://localhost:8080/bengkel_api/</code></p>
    <p><strong>For emulator:</strong> <code>http://10.0.2.2:8080/bengkel_api/</code></p>
</body>
</html>