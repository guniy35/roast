<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>订单编号</th>
        <th>订单状态</th>
        <th>来源分销商</th>
        <th>分销商手机</th>
        <th>佣金（元）</th>
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
            <td>
                {{$item->agent->name}}
            </td>
            <td>
                {{$item->agent->mobile}}
            </td>
            <td>{{$item->commission}}</td>
            <td>{{$item->created_at}}</td>
            <td>

                <a class="btn btn-xs btn-primary" href="{{route('admin.distribution.orders.show',['id'=>$item->id])}}">
                    <i data-toggle="tooltip" data-placement="top"
                       class="fa fa-eye"
                       title="查看"></i></a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>