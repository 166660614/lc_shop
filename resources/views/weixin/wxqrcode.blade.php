<div id="qrcode"></div>
    <script src="{{ URL::asset('js/qrcode/qrcode.min.js') }}"></script>
<script type="text/javascript">
    var qrcode = new QRCode(document.getElementById('qrcode'), {
        text: "{{$url}}",
        width: 256,
        height: 256,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
</script>