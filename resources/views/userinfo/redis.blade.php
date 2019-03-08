<table>
    <tr>
        <td>用户id</td>
        <td>用户昵称</td>
        <td>关注时间</td>
    </tr>
    @foreach($userinfo as $v)
    <tr>
        <td>{{$userinfo['nickname']}}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @endforeach
</table>