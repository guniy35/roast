<div class="col-md-6">
    <div class="ibox">
        <div class="ibox-title">
            <h5>订单信息</h5>
        </div>
        <div class="ibox-content">
            <dl class="dl-horizontal">

                <dt>订单编号:</dt>
                <dd>{{$order->order->order_no}}</dd>
                <dt>下单会员:</dt>
                <dd>{{$order->order->user->nick_name?$order->order->user->nick_name:$order->order->user->mobile}}</dd>
                <dt>下单时间:</dt>
                <dd>{{$order->order->created_at}}</dd>
                <dt>订单总金额:</dt>
                <dd>{{$order->order->total/100}}元</dd>
                <dt>订单状态:</dt>
                <dd>{{$order->order_status}}</dd>
            </dl>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="ibox">
        <div class="ibox-title">
            <h5>分销信息</h5>
        </div>
        <div class="ibox-content">
            <dl class="dl-horizontal">

                <dt>分销编号:</dt>
                <dd>{{$order->agent_order_no}}</dd>
                <dt>来源分销商:</dt>
                <dd>{{$order->agent->name}}</dd>
                <dt>总佣金:</dt>
                <dd>{{$order->commission}}元</dd>
                <dt>佣金状态:</dt>
                <dd>{{$order->commission_status}}</dd>
            </dl>
        </div>
    </div>
</div>
