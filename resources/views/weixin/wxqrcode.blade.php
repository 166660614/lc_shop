<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div id="qrcode"></div>
</body>
</html>

    <script src="{{ URL::asset('js/qrcode/qrcode.min.js') }}"></script>
<script type="text/javascript">
    var qrcode = new QRCode(document.getElementById('qrcode'), {
        text: "{{$url}}",
        width: 256,
        height: 256,
        colorDark: '#ffffff',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
</script>