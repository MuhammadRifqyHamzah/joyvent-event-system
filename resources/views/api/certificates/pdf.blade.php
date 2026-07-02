<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
        }
        .cert-container {
            width: 100%;
            height: 100%;
            display: block;
            margin: 0;
            padding: 0;
        }
        .cert-img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <img src="{{ $imageSrc }}" class="cert-img" alt="Sertifikat">
    </div>
</body>
</html>
