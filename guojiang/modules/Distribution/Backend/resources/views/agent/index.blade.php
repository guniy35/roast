{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
<style type="text/css">
    .thumb {
        float: left;
        margin-right: 15px;
        text-align: center;
        width: 50px;
    }

    .thumb > img {
        border-radius: 50%
    }
</style>
<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status','STATUS_AUDITED') }}"><a
                    href="{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDITED'])}}">已审核
            </a>
        </li>

        <li class="{{ Active::query('status','STATUS_AUDIT') }}"><a
                    href="{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDIT'])}}">待审核
            </a>
        </li>
        <li class="{{ Active::query('status','STATUS_FAILED') }}"><a
                    href="{{route('admin.distribution.agent.index',['status'=>'STATUS_FAILED'])}}">审核未通过
            </a>
        </li>
        <li class="{{ Active::query('status','STATUS_RETREAT') }}"><a
                    href="{{route('admin.distribution.agent.index',['status'=>'STATUS_RETREAT'])}}">已清退
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <div class="panel-body">
                <form action="" method="get" class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-6">
                            <div class="col-sm-6" style="padding-left: 0">
                                <div class="input-group date form_datetime">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;注册时间</span>
                                    <input type="text" class="form-control inline" name="stime"
                                           value="{{request('stime')}}" placeholder="开始" readonly>
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-5" style="padding-left: 0">
                                <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" name="etime" value="{{request('etime')}}"
                                           placeholder="截止" readonly>
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="hidden" value="{{request('status')}}" name="status">
                                <input type="text" name="value" placeholder="分销员姓名/手机"
                                       value="{{!empty(request('value'))?request('value'):''}}"
                                       class=" form-control"> <span
                                        class="input-group-btn">
                                            <button type="submit" class="btn btn-primary">查找</button> </span></div>
                        </div>

                        <div class="col-md-3">
                            <a class="btn btn-primary ladda-button dropdown-toggle batch" data-toggle="dropdown"
                               href="javascript:;" data-style="zoom-in">导出 <span
                                        class="caret"></span></a>
                            <ul class="dropdown-menu">

                                <li><a class="export-agents" data-toggle="modal"
                                       data-target="#modal" data-backdrop="static" data-keyboard="false"
                                       data-link="{{route('admin.distribution.agent.getExportData')}}" id="xls"
                                       data-url="{{route('admin.export.index',['toggle'=>'xls'])}}"
                                       data-type="xls"
                                       href="javascript:;">导出xls格式</a></li>

                                <li><a class="export-agents" data-toggle="modal"
                                       data-target="#modal" data-backdrop="static" data-keyboard="false"
                                       data-link="{{route('admin.distribution.agent.getExportData')}}" id="csv"
                                       data-url="{{route('admin.export.index',['toggle'=>'csv'])}}"
                                       data-type="csv"
                                       href="javascript:;">导出csv格式</a></li>

                            </ul>

                            <a class="btn btn-primary" href="{{route('admin.distribution.agent.create')}}">添加分销员</a>
                        </div>
                    </div>
                </form>
                <div class="hr-line-dashed clearfix"></div>

                @if(request('status')=='STATUS_AUDIT' OR request('status')=='STATUS_FAILED')
                    @include('backend-distribution::agent.includes.agent_audit_list')
                @else
                    @include('backend-distribution::agent.includes.agent_list')
                @endif

                <div class="clearfix"></div>
                <div class="pull-right">
                    {!! $agents->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/el.common.js') !!}
@include('backend-distribution::agent.includes.script')