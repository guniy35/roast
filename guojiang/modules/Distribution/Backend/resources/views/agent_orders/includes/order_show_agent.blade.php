<div class="panel blank-panel">
    <div class="panel-heading">
        <div class="panel-options">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab" aria-expanded="true">佣金分配信息</a>
                </li>

            </ul>
        </div>
    </div>

    <div class="panel-body">

        <div class="tab-content">
            <div class="tab-pane active" id="tab-1">
                <table class="table table-hover table-striped">
                    <tbody>
                    <tr>
                        <th>分销商名称</th>
                        <th>获取佣金（元）</th>
                        <th>佣金状态</th>
                    </tr>

                    @foreach($order->getAgentOrders($order->order_id) as $key=>$item)
                    <tr>
                        <td>{{$item->agent->name}}</td>
                        <td>{{$item->commission}}</td>
                        <td>{{$item->commission_status}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>