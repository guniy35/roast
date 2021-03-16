{{--@extends('backend-distribution::layouts.default')--}}

{{--@section ('title','佣金获取记录列表')--}}

{{--@section('breadcrumbs')--}}

    {{--<h2>佣金获取记录列表</h2>--}}
    {{--<ol class="breadcrumb">--}}
        {{--<li><a href="{!!route('admin.distribution.index')!!}"><i class="fa fa-dashboard"></i> 首页</a></li>--}}
        {{--<li><a href="{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDITED'])}}">分销员列表</a> </li>--}}
        {{--<li class="active">佣金获取记录列表</li>--}}
    {{--</ol>--}}

{{--@endsection--}}

{{--@section('after-styles-end')--}}
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
{{--@stop--}}


{{--@section('content')--}}
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="active"><a
                        href="javascript:;">佣金列表
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">

                <div class="panel-body">

                    @include('backend-distribution::commission.includes.list')

                    <div class="clearfix"></div>
                    <div class="pull-right">
                        {!! $commission->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

{{--@endsection--}}



{{--@section('before-scripts-end')--}}

{{--@stop--}}


{{--@section('after-scripts-end')--}}

{{--@stop--}}


