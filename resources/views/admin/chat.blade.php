<script src="js/jquery-1.12.4.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<form>
    <table>
        <input type="hidden" class="user_id" value="{{$user_id}}">
        <tr>
            <td>文本消息：</td>
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
            var user_id=$('.user_id').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType : "application/x-www-form-urlencoded; charset=UTF-8",
                url:'{{url("admin/weixin/userinfo")}}',
                type:'post',
                data:{user_id:user_id,news:news},
                dataType:'json',
                success:function (res) {
                    alert(res);
                }
            })
        })
    })
</script>