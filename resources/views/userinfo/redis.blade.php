<table border="1">
    <tr>
        <td>oppenid</td>
        <td>用户昵称</td>
        <td>性别</td>
    </tr>
    <tr>
        <td><input type="checkbox"></td>
        <td>{{$userinfo['openid']}}</td>
        <td>{{$userinfo['nickname']}}</td>
        <td>{{$userinfo['sex']}}</td>
    </tr>
</table>
<script src="js/jquery-1.12.4.min.js"></script>
<script>
    $(function () {
        alert(1)
    })
</script>