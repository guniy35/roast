<table class="table table-hover table-striped" id="app" data-brand_id="{{request('brand_id')}}" data-totalPage="{{$brands->lastPage()}}">
    <tbody>
    <!--tr-th start-->
    <tr>
        <th>ID</th>
        <th>品牌名称</th>
        <th>操作</th>
    </tr>
    <!--tr-th end-->
    @foreach($brands as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                <img src="{{$item->logo}}" width="50" height="50" alt="">
                {{$item->name}}
            </td>

            <td id="radio_brand_id_{{$item->id}}">

                <input id="radio_brand_id_{{$item->id}}" data-brand_id="{{$item->id}}"

                       type="radio" name="brands">

                <input id="brand_id_{{$item->id}}"  type="hidden"

                       value="{{$item->id}}" data-title="{{$item->name}}" data-img="{{isset($item->brand_img)?$item->brand_img:$item->logo}}" data-id="{{$item->id}}">

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
    function check(brand_id){
        $('#radio_brand_id_'+brand_id).iCheck('check');
    }
    $('input[name=brands]').on('ifChecked', function(e){
        var brand_id=$(this).data('brand_id');
        $('#app').attr('data-brand_id',brand_id);
    });

    function initCheck(){
        var brand_id=$('#app').data('brand_id');
        check("{{request('brand_id')}}");
    }
    ;

</script>


