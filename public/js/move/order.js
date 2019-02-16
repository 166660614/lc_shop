$(".bttt").click(function(e){
    e.preventDefault();
    _this=$(this);
    var position = _this.attr('position');
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/move/order',
        type    :   'post',
        data    :   {position:position},
        dataType:   'json',
        success :   function(res){
            alert(res.msg)
            window.location.reload();
        }
    });
});