<table>
    <tr>
        <td>oppenid</td>
        <td>用户昵称</td>
        <td>性别</td>
    </tr>
    @foreach($userinfo as $k=>$v)
    <tr>
        <td>{{$v['openid']}}</td>
        <td>{{$v['nickname']}}</td>
        <td>{{$v['sex']}}</td>
    </tr>
    @endforeach
</table>