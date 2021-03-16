<table class="table table-hover table-striped" id="app" data-seckill_id="{{request('seckill_id')}}" data-totalPage="{{$seckills->lastPage()}}">
    <tbody>
    <!--tr-th start-->
    <tr>
        <th>ID</th>
        <th>秒杀商品</th>
        <th>秒杀价</th>
        <th>操作</th>
    </tr>
    <!--tr-th end-->
    @foreach($seckills as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                <img src="{{$item->goods->img}}" width="50" height="50" alt="">
                {{$item->goods->name}}
            </td>
            <td>
                {{$item->seckill_price}}
            </td>
            <td id="radio_seckill_id_{{$item->id}}">

                <input id="radio_seckill_id_{{$item->id}}" data-seckill_id="{{$item->id}}"

                       type="radio" name="seckills">

                <input id="seckill_id_{{$item->id}}"  type="hidden"

                       value="{{$item->id}}" data-title="{{$item->goods->name}}" data-img="{{$item->goods->img}}" data-id="{{$item->id}}"

                       data-price="{{$item->seckill_price}}">

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>

    $('#app').find("input").iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
        increaseArea: '20%'
    });
    function check(seckill_id){
        $('#radio_seckill_id_'+seckill_id).iCheck('check');
    }
    $('input[name=seckills]').on('ifChecked', function(e){
        var seckill_id=$(this).data('seckill_id');
        $('#app').attr('data-seckill_id',seckill_id);
    });

    function initCheck(){
        var seckill_id=$('#app').data('seckill_id');
        check("{{request('seckill_id')}}");
    }
    ;

</script>


