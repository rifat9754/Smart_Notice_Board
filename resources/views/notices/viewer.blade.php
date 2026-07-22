<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDF Viewer</title>

    <style>
        html, body {
            margin: 0;
            height: 100%;
        }

        embed {
            width: 100%;
            height: 100vh;
            border: none;
        }
    </style>
</head>
<body>

<embed
    src="{{ $pdfUrl }}"
    type="application/pdf">

</body>
</html>