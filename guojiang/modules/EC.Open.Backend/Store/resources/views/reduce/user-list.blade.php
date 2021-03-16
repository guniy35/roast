
<span style="margin-right: 50px;">当前进度：{{$reduceItem->progress_par*100}}%</span>
<span style="margin-right: 50px;">已砍金额： {{$reduceItem->reduce_amount}}</span>
@if($reduceItem->status_text=='进行中')
    <span style="margin-right: 50px;">剩余可砍金额： {{$reduceItem->reduce_surplus_amount}}</span>
@endif

<span style="margin-right: 50px;">状态:{{$reduceItem->status_text}}</span>

@if(isset($reduceItem->order->order_no))
    <span style="margin-right: 50px;">订单号:
        <a href="{{route('admin.orders.show',$reduceItem->order->id)}}" target="_blank">
        {{$reduceItem->order->order_no}}
     </a>
    </span>

    @if($reduceItem->complete_time)
        <span style="margin-right: 50px;">支付状态：已支付</span>
    @else
        <span style="margin-right: 50px;">支付状态：未支付</span>
    @endif

@endif

<div class="progress" style="margin-top:15px;">
    <div class="progress-bar progress-bar-info" role="progressbar"
         aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
         style="width: {{$reduceItem->progress_par*100}}%;">
        <span class="sr-only"></span>
    </div>
</div>

<div class="hr-line-dashed"></div>

<div class="table-responsive">

    @if(count($Lists)>0)
        <table class="table table-hover table-striped">
            <tbody>
            <!--tr-th start-->
            <tr>
                <th>会员ID</th>
                <th>会员</th>
                <th>头像</th>
                <th>帮砍金额</th>
                <th>时间</th>
                <th></th>
            </tr>
            <!--tr-th end-->
            @foreach ($Lists as $item)
                <tr>
                    <td>
                        {{$item->user_id}}
                    </td>
                    <td>
                        <a href="{{route('admin.users.edit',$item->user_id)}}" target="_blank">
                            {{isset($item->meta['nick_name'])?$item->meta['nick_name']:''}}<br>
                            @if($item->userInfo->mobile)
                                {{substr_replace($item->userInfo->mobile,'***',3,5)}}
                            @endif
                        </a>
                    </td>

                    <td>
                            @if(isset($item->meta['avatar']))
                            <img src="{{$item->meta['avatar']}}" width="50" height="50" alt="">
                            @endif
                        </a>
                    </td>

                    <td>
                        {{$item->reduce_amount}}
                    </td>

                    <td>
                        {{$item->created_at}}
                    </td>
                    <td>@if($item->is_leader)

                            <p><span class="label label-info"> 砍价发起人</span></p>
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














