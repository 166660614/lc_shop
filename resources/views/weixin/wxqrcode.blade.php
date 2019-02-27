<script src="js/qrcode.js"></script>
<div id="qrcode"></div>
<script>
    $(function () {
        //简单方式
        //new QRCode(document.getElementById('qrcode'), 'your content');
        // 设置参数方式
        var qrcode = new QRCode('qrcode', {
            text: 'your content',
            width: 256,
            height: 256,
            colorDark : '#000000',
            colorLight : '#ffffff',
            correctLevel : QRCode.CorrectLevel.H
        });
// 使用 API
        qrcode.clear();
        qrcode.makeCode($url);
    })
</script>