<div class="panel blank-panel">
    <div class="panel-heading">
        <div class="panel-options">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-1" data-toggle="tab" aria-expanded="true">商品信息</a></li>

            </ul>
        </div>
    </div>

    <div class="panel-body">

        <div class="tab-content">
            <div class="tab-pane active" id="tab-1">

                <table class="table table-hover table-striped">
                    <tbody>

                    <tr>
                        <th>商品名称</th>
                        <th>数量</th>
                        <th>总价(元)</th>
                        <th>佣金比例</th>
                        <th>佣金(元)</th>
                    </tr>

                    @foreach($order->items as $key=>$val)
                        <tr>
                            <td><img width="50" height="50"
                                     src="{{$val->orderItem->item_meta['image']}}"
                                     alt="">&nbsp;&nbsp;&nbsp;&nbsp;
                                {{$val->orderItem->item_name}}
                            </td>
                            <td> {{$val->orderItem->quantity}}</td>
                            <td>{{$val->orderItem->total/100}}</td>
                            <td>{{$val->rate}} %</td>
                            <td>{{$val->total_commission}}</td>

                        </tr>
                    @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>