<table class="table table-hover table-striped" id="app" data-groupon_id="{{request('groupon_id')}}" data-totalPage="{{$groupons->lastPage()}}">
    <tbody>
    <!--tr-th start-->
    <tr>
        <th>ID</th>
        <th>拼团商品</th>
        <th>拼团价</th>
        <th>操作</th>
    </tr>
    <!--tr-th end-->
    @foreach($groupons as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                <img src="{{$item->goods->img}}" width="50" height="50" alt="">
                {{$item->goods->name}}
            </td>
            <td>
                {{$item->price}}
            </td>
            <td id="radio_groupon_id_{{$item->id}}">

                <input id="radio_groupon_id_{{$item->id}}" data-groupon_id="{{$item->id}}"

                       type="radio" name="groupons">

                <input id="groupon_id_{{$item->id}}"  type="hidden"

                       value="{{$item->id}}" data-title="{{$item->goods->name}}" data-img="{{$item->goods->img}}" data-id="{{$item->id}}" data-number="{{$item->number}}"

                       data-price="{{$item->price}}">

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
    function check(groupon_id){
        $('#radio_groupon_id_'+groupon_id).iCheck('check');
    }
    $('input[name=groupons]').on('ifChecked', function(e){
        var groupon_id=$(this).data('groupon_id');
        $('#app').attr('data-groupon_id',groupon_id);
    });

    function initCheck(){
        var groupon_id=$('#app').data('groupon_id');
        check("{{request('groupon_id')}}");
    }
    ;

</script>


