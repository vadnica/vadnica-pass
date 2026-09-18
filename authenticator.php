<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOTP Avtentikator - Prototip</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=3">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png?v=3">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png?v=3">
    <link rel="manifest" href="site.webmanifest?v=3">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="authenticator">
    <div class="authenticator-container">
        <h3>1. Skrivni ključ (Secret)</h3>
        <p>Naključno generiran Base32 ključ. Ta ključ vneseš v aplikacijo na telefonu.</p>
        <div class="secret-display" id="secretDisplay">-</div>
        <button onclick="regenerateSecret()" class="authenticator-button">Generiraj nov klúč</button>
    </div>

    <div class="authenticator-container">
        <h3>3. Preverjanje kode</h3>
        <p>Vnesi 6-mestno kodo iz aplikacije na telefonu:</p>
        <label for="codeInput"></label>
        <input type="text" id="codeInput" maxlength="6" placeholder="000000" oninput="verifyCode()">
        <div class="result" id="verifyResult"></div>
    </div>
</div>

<script src="authenticator.js"></script>
</body>
</html>