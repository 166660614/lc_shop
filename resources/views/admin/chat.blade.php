<meta name="csrf-token" content="{{ csrf_token() }}">
<form>
    <table>
        <tr>
            <td>聊天记录</td>
            <td><div style="width:400px;height:500px;border: solid black 1px" id="content"></div></td>
        </tr>
        <input type="hidden" class="openid" value="{{$userinfo['openid']}}">
        <input type="hidden" class="nickname" value="{{$userinfo['nickname']}}">
        <tr>
            <td>发送内容：</td>
            <td><input type="text" id="news"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="button" id="submit" value="发送"></td>
        </tr>
    </table>
</form>
<script>
    $(function () {
        $('#submit').click(function () {
            _this=$(this);
            var news=$('#news').val();
            var user_id=$('.openid').val();
            //$('#content').text(news);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    contentType : "application/x-www-form-urlencoded; charset=UTF-8",
                    url:'{{url("admin/weixin/userinfo")}}',
                    type:'post',
                    data:{openid:openid,news:news},
                    dataType:'json',
                    success:function (res) {
                        if(res.code==0){
                            alert(res.msg)
                           var _news="<h6>小智客服 ："+news+"</h6>"
                            $('#content').append(_news);
                            $('#news').val('');
                        }else{
                            alert(res);
                        }
                    }
                })
        })

        setInterval(function () {
            var user_id=$('.openid').val();
            console.log(user_id)
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType : "application/x-www-form-urlencoded; charset=UTF-8",
                url:'{{url("admin/weixin/getrecord")}}',
                type:'post',
                data:{openid:openid},
                dataType:'json',
                success:function (res) {
                    console.log(res.recorddata);
                    _newcontent=res.recorddata;
                    $('#content').html(_newcontent);
                }
            })
        },5000)
    })
</script>
