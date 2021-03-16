<table class="table table-hover table-striped" id="app" data-suit_id="{{request('suit_id')}}" data-totalPage="{{$suits->lastPage()}}">
    <tbody>
    <!--tr-th start-->
    <tr>
        <th>ID</th>
        <th>套餐名称</th>
        <th>套餐总价</th>
        <th>操作</th>
    </tr>
    <!--tr-th end-->
    @foreach($suits as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                {{$item->title}}
            </td>

            <td>
                {{$item->total}}
            </td>
            <td id="radio_suit_id_{{$item->id}}">

                <input id="radio_suit_id_{{$item->id}}" data-suit_id="{{$item->id}}"

                       type="radio" name="suits">

                <input id="suit_id_{{$item->id}}"  type="hidden"

                       value="{{$item->id}}" data-title="{{$item->title}}"

                       data-id="{{$item->id}}"

                       data-count="

                        @if($item->items->count()<=0)

                               0

                        @else

                             {{ $item->items->filter(function ($v){

                                 return $v->status>0;

                             })->count()}}

                        @endif

                        "

                       data-price="{{$item->total}}">

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
    function check(suit_id){
        $('#radio_suit_id_'+suit_id).iCheck('check');
    }
    $('input[name=suits]').on('ifChecked', function(e){
        var suit_id=$(this).data('suit_id');
        $('#app').attr('data-suit_id',suit_id);
    });

    function initCheck(){
        var suit_id=$('#app').data('suit_id');
        check("{{request('suit_id')}}");
    }
    ;

</script>


