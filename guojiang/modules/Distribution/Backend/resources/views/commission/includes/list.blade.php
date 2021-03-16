<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>获取金额(元)</th>
        <th>关联订单编号</th>
        <th>备注</th>
        <th>获取时间</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($commission as $item)
        <tr>
            <td>
               {{$item->commission}}
            </td>

            <td>
                <p>分销单号：{{$item->agentOrder->agent_order_no}}</p>
                <p>订单编号：{{$item->agentOrder->order->order_no}}</p>
            </td>

            <td>
                {{$item->note}}
            </td>
            <td>{{$item->created_at}}</td>
        </tr>
    @endforeach
    </tbody>
</table>