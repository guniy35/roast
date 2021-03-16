{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status','STATUS_AUDIT') }}"><a
                    href="{{route('admin.balance.cash.index',['status'=>'STATUS_AUDIT'])}}">待审核
            </a>
        </li>

        <li class="{{ Active::query('status','STATUS_WAIT_PAY') }}"><a
                    href="{{route('admin.balance.cash.index',['status'=>'STATUS_WAIT_PAY'])}}">待打款提现
            </a>
        </li>
        <li class="{{ Active::query('status','STATUS_PAY') }}"><a
                    href="{{route('admin.balance.cash.index',['status'=>'STATUS_PAY'])}}">已打款提现
            </a>
        </li>
        <li class="{{ Active::query('status','STATUS_FAILED') }}"><a
                    href="{{route('admin.balance.cash.index',['status'=>'STATUS_FAILED'])}}">审核未通过
            </a>
        </li>


    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">

                @include('backend-distribution::cash.includes.search')

                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>申请编号</th>
                        <th>申请人</th>
                        <th>手机号</th>
                        <th>提现金额</th>
                        <th>申请时间</th>
                        @if(request('status')!='STATUS_AUDIT')
                            <th>处理时间</th>
                        @endif
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($cash as $item)
                        <tr>
                            <td>{{$item->cash_no}}</td>
                            <td>{{isset($item->agent->name)?$item->agent->name:''}}</td>
                            <td>{{isset($item->agent->mobile)?$item->agent->mobile:''}}</td>
                            <td>{{$item->amount}}</td>
                            <td>{{$item->created_at}}</td>
                            @if(request('status')!='STATUS_AUDIT')
                                <td>{{$item->updated_at}}</td>
                            @endif
                            <td>
                                @if(request('status')=='STATUS_WAIT_PAY')
                                    <a class="btn btn-xs btn-primary"
                                       href="{{route('admin.balance.cash.operatePay',['id'=>$item->id])}}">
                                        <i data-toggle="tooltip" data-placement="top"
                                           class="fa fa-pencil-square-o"
                                           title="操作"></i></a>
                                @else
                                    <a class="btn btn-xs btn-primary"
                                       href="{{route('admin.balance.cash.show',['id'=>$item->id])}}">
                                        <i data-toggle="tooltip" data-placement="top"
                                           class="fa fa-pencil-square-o"
                                           title="操作"></i></a>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="clearfix"></div>
                <div class="pull-right">
                    {!! $cash->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/el.common.js') !!}
@include('backend-distribution::cash.includes.script')