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

        iframe {
            width: 100%;
            height: 100vh;
            border: none;
        }
    </style>
</head>
<body>

<iframe
    src="https://mozilla.github.io/pdf.js/web/viewer.html?file={{ urlencode($pdfUrl) }}">
</iframe>

</body>
</html>