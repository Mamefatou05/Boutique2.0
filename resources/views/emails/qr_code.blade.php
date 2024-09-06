<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte De Fidélité</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative;
            margin: 0;
        }
        .card {
            top: 25%;
            left: 25%;
            background-color: white;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            width: 85.6mm; /* Standard credit card width */
            height: 53.98mm; /* Standard credit card height */
            position: relative;
            overflow: hidden;
        }
        .card-header {
            color: #9932CC;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .profile-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 5px;
        }
        .client-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .qr-code {
            width: 70px;
            height: 70px;
            margin: 0 auto;
        }
        .decoration {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            z-index: 0;
        }
        .decoration-1 {
            background-color: rgba(153, 50, 204, 0.2);
            top: 5px;
            left: 5px;
        }
        .decoration-2 {
            background-color: rgba(255, 165, 0, 0.2);
            bottom: 5px;
            right: 5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
        <h1 class="card-header">Carte De Fidélité</h1>
        <img src="{{ $user->photo }}" alt="Photo de profil" class="profile-image">
        <p class="client-name">{{ $user->prenom }} {{ $user->nom }}</p>
        <img src="data:image/png;base64,{{ $client->qr_code_base64}}" alt="QR Code" class="qr-code">
    </div>
</body>
</html>