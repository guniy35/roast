<div class="hr-line-dashed"></div>
<div class="table-responsive">
    @if(count($Lists)>0)
        <table class="table table-hover table-striped">
            <tbody>
            <!--tr-th start-->
            <tr>
                <th>标题</th>
                <th width="300">商品</th>

                <th>开始时间</th>
                <th>结束时间</th>
                <th>当前参与</th>
                <th>当前活动库存数</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            <!--tr-th end-->
            @foreach ($Lists as $item)
                <tr>
                    <td>{{$item->title}}</td>
                    <td>
                        <a href="{{route('admin.goods.edit',$item->goods->id)}}" target="_blank"> {{$item->goods->name}}</a>
                    </td>
                    <td>{{$item->starts_at}}</td>
                    <td>{{$item->ends_at}}</td>

                    <td>

                        <a href="{{route('admin.promotion.reduce.getItemLists',['reduce_id'=>$item->id,'title'=>$item->title])}}" target="_blank">{{$item->items->count()}}</a>


                    </td>

                    <td>
                        {{$item->store_nums}}
                    </td>

                    <td>{{$item->status_text}}</td>
                    <td style="position: relative;">
                        @if($item->edit_status==1)
                            <a no-pjax
                               href="{{route('admin.promotion.reduce.edit',['id'=>$item->id,'type'=>'edit'])}}"
                               class="btn btn-xs btn-success">
                                <i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top"
                                   title="编辑"></i></a>

                            <a no-pjax href="javascript:;"
                               data-url="{{route('admin.promotion.reduce.delete',['id'=>$item->id,'type'=>'close'])}}"
                               class="btn btn-xs btn-danger delete">
                                <i class="fa fa-close" data-toggle="tooltip" data-placement="top"
                                   title="使失效"></i></a>
                        @else
                            <a no-pjax
                               href="{{route('admin.promotion.reduce.edit',['id'=>$item->id,'type'=>'show'])}}"
                               class="btn btn-xs btn-success">
                                <i class="fa fa-eye" data-toggle="tooltip" data-placement="top"
                                   title="查看"></i></a>


                            <a no-pjax href="javascript:" class="btn btn-xs btn-danger delete"
                               data-url="{{route('admin.promotion.reduce.delete',['id'=>$item->id,'type'=>'delete'])}}"><i
                                        class="fa fa-trash" data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="删除活动"></i></a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="pull-left">
            &nbsp;&nbsp;共&nbsp;{!! $Lists->total() !!} 条记录
        </div>

        <div class="pull-right id='ajaxpag'">
            {!! $Lists->appends(request()->except('page'))->render() !!}
        </div>

        <!-- /.box-body -->

    @else
        <div>
            &nbsp;&nbsp;&nbsp;当前无数据
        </div>
    @endif
</div>














