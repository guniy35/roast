<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status','STATUS_ALL') }}"><a
                    href="{{route('admin.distribution.agent.orders.index',['id'=>$id,'status'=>'STATUS_ALL'])}}">所有订单
            </a>
        </li>

        <li class="{{ Active::query('status','STATUS_UNSETTLED') }}"><a
                    href="{{route('admin.distribution.agent.orders.index',['id'=>$id,'status'=>'STATUS_UNSETTLED'])}}">未结算订单
            </a>
        </li>

        <li class="{{ Active::query('status','STATUS_STATE') }}"><a
                    href="{{route('admin.distribution.agent.orders.index',['id'=>$id,'status'=>'STATUS_STATE'])}}">已结算订单
            </a>
        </li>
        <li class="{{ Active::query('status','STATUS_INVALID') }}"><a
                    href="{{route('admin.distribution.agent.orders.index',['id'=>$id,'status'=>'STATUS_INVALID'])}}">已失效订单
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="hidden" value="{{request('status')}}" name="status">
                                <input type="hidden" value="{{$id}}" name="id">
                                <div class="input-group-btn">
                                    <select class="form-control" name="field" style="width: 150px">
                                        <option value="">请选择条件搜索</option>
                                        <option value="agent_order_no" {{request('field')=='agent_order_no'?'selected':''}} >
                                            分销单号
                                        </option>
                                        @if(settings('distribution_level')>1)
                                            <option value="name" {{request('field')=='name'?'selected':''}} >
                                                来源分销员姓名
                                            </option>
                                        @endif
                                        <option value="order_no" {{request('field')=='order_no'?'selected':''}} >
                                            订单编号
                                        </option>
                                    </select>
                                </div>


                                <input type="text" name="value" value="{{request('value')}}" placeholder="Search"
                                       class="form-control">
                                <span class="input-group-btn"> <button type="submit" class="btn btn-primary">
                                                   搜索
                                               </button> </span>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="hr-line-dashed clearfix"></div>

                @include('backend-distribution::agent_orders.includes.order_list')

                <div class="clearfix"></div>

                <div class="pull-left">
                    总共 {{$orders->count()}} 条数据
                </div>

                <div class="pull-right">
                    {!! $orders->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>