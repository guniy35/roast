{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status','ALL') }}"><a
                    href="{{route('admin.distribution.orders.index',['status'=>'ALL'])}}">所有订单
            </a>
        </li>

        <li class="{{ Active::query('status','STATUS_UNSETTLED') }}"><a
                    href="{{route('admin.distribution.orders.index',['status'=>'STATUS_UNSETTLED'])}}">未结算订单
            </a>
        </li>

        <li class="{{ Active::query('status','STATUS_STATE') }}"><a
                    href="{{route('admin.distribution.orders.index',['status'=>'STATUS_STATE'])}}">已结算订单
            </a>
        </li>
        <li class="{{ Active::query('status','STATUS_INVALID') }}"><a
                    href="{{route('admin.distribution.orders.index',['status'=>'STATUS_INVALID'])}}">已失效订单
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <div class="panel-body">
                @include('backend-distribution::orders.includes.search')

                <div class="hr-line-dashed clearfix"></div>

                @include('backend-distribution::orders.includes.order_list')

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
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/el.common.js') !!}
@include('backend-distribution::orders.includes.script')