<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>订单编号</th>
        <th>订单状态</th>
        @if(settings('distribution_level')>1)
            <th>来源分销商</th>
            <th>订单层级</th>
        @endif
        <th>佣金（元）</th>
        <th>佣金状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($orders as $item)
        <tr>
            <td>
                <p>分销单号：{{$item->agent_order_no}}</p>
                <p>商城单号：{{$item->order->order_no}}</p>
            </td>
            <td>{{$item->order_status}}</td>
            @if(settings('distribution_level')>1)
                <td>
                    {!! $item->fromAgent?$item->fromAgent->name:'<span style="color:#008cee">自己推广</span>' !!}
                </td>

                <td>
                    {{$item->level}}级订单
                </td>
            @endif
            <td>{{$item->commission}}</td>
            <td>{{$item->commission_status}}</td>
            <td>{{$item->created_at}}</td>
            <td>

                <a class="btn btn-xs btn-primary" href="{{route('admin.distribution.agent.orders.show',['id'=>$item->id])}}">
                    <i data-toggle="tooltip" data-placement="top"
                       class="fa fa-eye"
                       title="查看"></i></a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>