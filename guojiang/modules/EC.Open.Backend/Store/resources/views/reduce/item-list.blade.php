<div class="hr-line-dashed"></div>
<div class="table-responsive">

    @if(count($Lists)>0)
        <table class="table table-hover table-striped">
            <tbody>
            <!--tr-th start-->
            <tr>
                <th>会员ID</th>
                <th>会员</th>
                <th>开始时间</th>
                <th>结束时间</th>
                <th>状态</th>
                <th>已砍人数</th>
                <th>砍价进度</th>
                <th>订单号</th>
                <th>操作</th>
            </tr>
            <!--tr-th end-->
            @foreach ($Lists as $item)
                <tr>
                    <td>
                         {{$item->user_id}}
                    </td>
                    <td>
                        <a href="{{route('admin.users.edit',$item->user_id)}}" target="_blank">
                            {{isset($item->user->meta['nick_name'])?$item->user->meta['nick_name']:''}}<br>
                            @if($item->userInfo->mobile)
                                {{substr_replace($item->userInfo->mobile,'***',3,5)}}
                            @endif
                        </a>
                    </td>

                    <td>{{$item->starts_at}}</td>
                    <td>{{$item->ends_at}}</td>
                    <td>
                        {{$item->status_text}}
                    </td>
                    <td>
                        {{$item->users->count()}}
                    </td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" style="width:{{$item->progress_par*100}}%">{{$item->progress_par*100}}%</div>
                        </div>
                    </td>
                    <td>
                        @if(isset($item->order->order_no))
                            <a href="{{route('admin.orders.show',$item->order->id)}}" target="_blank">
                                {{$item->order->order_no}}
                            </a>
                           <br>下单时间:{{$item->order_time}}
                        @else

                        @endif
                    </td>
                    {{--<td>--}}
                        {{--@if($item->complete_time)--}}
                            {{--<br>已支付--}}
                        {{--@else--}}
                            {{--<br>未支付--}}
                        {{--@endif--}}
                    {{--</td>--}}

                    <td>

                        <a no-pjax target="_blank"
                           href="{{route('admin.promotion.reduce.getUserLists',['reduce_items_id'=>$item->id])}}"
                           class="btn btn-xs btn-success">
                            <i class="fa fa-eye" data-toggle="tooltip" data-placement="top"
                               title="查看详情"></i></a>
                        </a>
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














