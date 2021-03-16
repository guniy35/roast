<table class="table table-hover table-striped" id="app" data-article_id="{{request('article_id')}}" data-totalPage="{{$articles->lastPage()}}">
    <tbody>
    <!--tr-th start-->
    <tr>
        <th>ID</th>
        <th>文章标题</th>
        <th>分类</th>
        <th>封面</th>
        <th>操作</th>
    </tr>
    <!--tr-th end-->
    @foreach($articles as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                {{$item->title}}
            </td>
            <td>{{$item->type_text}}</td>
            <td>
                <img src="{{$item->img}}" width="50" height="50" alt="">
            </td>

            <td id="radio_article_id_{{$item->id}}">

                <input id="radio_article_id_{{$item->id}}" data-article_id="{{$item->id}}"

                       type="radio" name="articles">

                <input id="article_id_{{$item->id}}"  type="hidden"

                       value="{{$item->id}}" data-title="{{$item->title}}" data-type="{{$item->type_text}}"  data-img="{{$item->img}}" data-id="{{$item->id}}">

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
    function check(article_id){
        $('#radio_article_id_'+article_id).iCheck('check');
    }
    $('input[name=articles]').on('ifChecked', function(e){
        var article_id=$(this).data('article_id');
        $('#app').attr('data-article_id',article_id);
    });

    function initCheck(){
        var article_id=$('#app').data('article_id');
        check("{{request('article_id')}}");
    }
    ;

</script>


