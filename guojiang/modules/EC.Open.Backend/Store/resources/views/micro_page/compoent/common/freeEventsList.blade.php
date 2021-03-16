<table class="table table-hover table-striped" id="app" data-free_event_id="{{request('free_event_id')}}" data-totalPage="{{$freeEvents->lastPage()}}">
    <tbody>
    <!--tr-th start-->
    <tr>
        <th>ID</th>
        <th>集call活动</th>
        <th>集call数量</th>
        <th>操作</th>
    </tr>
    <!--tr-th end-->
    @foreach($freeEvents as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                <img src="{{$item->img}}" width="50" height="50" alt="">
                {{$item->title}}
            </td>
            <td>
                {{$item->per_count}}
            </td>
            <td id="radio_seckill_id_{{$item->id}}">

                <input id="radio_free_event_id_{{$item->id}}" data-free_event_id="{{$item->id}}"

                       type="radio" name="freeEvents">

                <input id="free_event_id_{{$item->id}}"  type="hidden"

                       value="{{$item->id}}" data-title="{{$item->title}}" data-img="{{$item->img}}" data-id="{{$item->id}}"

                       data-per_count="{{$item->per_count}}">

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
    function check(free_event_id){
        $('#radio_free_event_id_'+free_event_id).iCheck('check');
    }
    $('input[name=freeEvents]').on('ifChecked', function(e){
        var free_event_id=$(this).data('free_event_id');
        $('#app').attr('data-free_event_id',free_event_id);
    });

    function initCheck(){
        var free_event_id=$('#app').data('free_event_id');
        check("{{request('free_event_id')}}");
    }
    ;

</script>


