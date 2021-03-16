{{--@extends('backend-distribution::layouts.default')--}}

{{--@section ('title','分销商品管理')--}}

{{--@section ('breadcrumbs')--}}
    {{--<h2>分销商品管理</h2>--}}
    {{--<ol class="breadcrumb">--}}
        {{--<li><a href="{!!route('admin.distribution.index')!!}"><i class="fa fa-dashboard"></i> 首页</a></li>--}}
        {{--<li class="active">{!! link_to_route('admin.distribution.goods.setting', '分销商品管理') !!}</li>--}}
    {{--</ol>--}}
{{--@stop--}}

{{--@section('after-styles-end')--}}
    <style type="text/css">
        .thumb {
            float: left;
            margin-right: 15px;
            text-align: center;
            width: 50px;
        }
    </style>
{{--@stop--}}


{{--@section('content')--}}
    <div class="tabs-container">

        <ul class="nav nav-tabs">
            <li class="{{ Active::query('status','ACTIVITY') }}"><a
                        href="{{route('admin.distribution.goods.setting',['status'=>'ACTIVITY'])}}">已启用分销商品</a></li>
            <li class="{{ Active::query('status','UNACTIVITY') }}"><a
                        href="{{route('admin.distribution.goods.setting',['status'=>'UNACTIVITY'])}}">未启用分销商品</a></li>
            <li class="pull-right">
                <button class="btn btn-primary" data-toggle="modal"
                data-target="#modal" data-backdrop="static" data-keyboard="false"
                data-url="{{route('admin.distribution.goods.syncGoods')}}"
                        type="button">
                一键同步商品
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                <div class="panel-body">
                    @include('backend-distribution::settings.includes.goods_list')
                    </div>
                </div>


            <div class="pull-left">
                {!! $goods->total() !!} 件商品
            </div>

            <div class="pull-right">
                {!! $goods->render() !!}
            </div>
            <div class="box-footer clearfix">
            </div>
        </div>
    </div>
    <div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>
{{--@endsection--}}

{{--@section('before-scripts-end')--}}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/sortable/Sortable.min.js') !!}
    @include('backend-distribution::settings.includes.script')
{{--@endsection--}}