<table>
    <tr>
        <td>oppenid</td>
        <td>用户昵称</td>
        <td>性别</td>
    </tr>
    @foreach($userinfo as $v)
    <tr>
        <td>{{$userinfo['openid']}}</td>
        <td>{{$userinfo['nickname']}}</td>
        <td>{{$userinfo['sex']}}</td>
    </tr>
    @endforeach
</table>