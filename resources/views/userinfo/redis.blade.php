<meta name="csrf-token" content="{{csrf_token()}}">
<table border="1">
    <tr>
        <td>多选</td>
        <td>oppenid</td>
        <td>用户昵称</td>
        <td>性别</td>
        <td>操作</td>
    </tr>
    <tr openid="$userinfo['openid']">
        <td><input type="checkbox"></td>
        <td>{{$userinfo['openid']}}</td>
        <td>{{$userinfo['nickname']}}</td>
        <td>{{$userinfo['sex']}}</td>
        <td>
            <input type="button" value="加入黑名单" id="blacklist">
            <input type="text" placeholder="加标签" id="tag_name">
            <input type="button" value="添加标签" id="tag">
        </td>
    </tr>
</table>
<script src="/js/jquery-1.12.4.min.js"></script>
<script>
    $(function () {
        $("#tag").click(function(e){
            e.preventDefault();
            var tag_name = $('#tag_name').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/tag',
                type    :   'post',
                data    :   {tag_name:tag_name},
                dataType:   'json',
                success :   function(res){
                    alert(res.msg)
                    window.location.reload();
                }
            });
        });
        $("#blacklist").click(function(e){
            e.preventDefault();
            _this=$(this);
            var openid = _this.parents('tr').attr('openid');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/blacklist',
                type    :   'post',
                data    :   {openid:openid},
                dataType:   'json',
                success :   function(res){
                    alert(res.msg)
                    window.location.reload();
                }
            });
        });
    })
</script>