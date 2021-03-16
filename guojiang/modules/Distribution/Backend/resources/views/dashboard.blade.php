{{--@extends('backend-distribution::layouts.default')--}}
{{--@section ('title','分销管理')--}}

{{--@section('after-styles-end')--}}

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
    <div class="row">
        <div class="col-md-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">今天</span>
                    <h5>分销员</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins text-center">{{$agentCount}}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">今天</span>
                    <h5>分销订单</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins text-center">{{$orderCount}} 笔</h1>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">今天</span>
                    <h5>注册用户</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins text-center">{{$userCount}}</h1>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">今天</span>
                    <h5>产生佣金</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins text-center">¥ {{$cashCount}}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">

                    <div class="m-t-sm">

                        <div class="row">
                            <div class="col-md-8">
                                <div>
                                    <div id="main" style="width: 100%;height:500px;"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>总数据统计</h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
                                                <i class="fa fa-chevron-up"></i>
                                            </a>
                                            <a class="close-link">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ibox-content no-padding">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span class="badge badge-primary">{{$totalAgent}} 个</span> 分销员
                                            </li>
                                            <li class="list-group-item ">
                                                <span class="badge badge-info">{{$totalOrder}} 笔</span> 分销订单
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-danger">{{$totalUser}} 个</span> 注册用户
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge badge-success">{{$totalCash}} 元</span> 产生佣金
                                            </li>
                                            <li class="list-group-item" style="display: none">
                                                <button type="button" class="btn btn-primary export-cash"
                                                        data-toggle="modal"
                                                        data-target="#modal" data-backdrop="static"
                                                        data-keyboard="false"
                                                        data-link="{{route('admin.distribution.dataStatistics',['type'=>'xls'])}}"
                                                        id="xls"
                                                        data-url="{{route('admin.export.index',['toggle'=>'xls'])}}"
                                                        href="javascript:;">日报导出
                                                </button>

                                                <button type="button" class="btn btn-primary export-cash"
                                                        data-toggle="modal"
                                                        data-target="#modal" data-backdrop="static"
                                                        data-keyboard="false"
                                                        data-link="{{route('admin.distribution.monthDataStatistics',['type'=>'xls'])}}"
                                                        id="month-xls"
                                                        data-url="{{route('admin.export.index',['toggle'=>'month-xls'])}}"
                                                        href="javascript:;">月报导出
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="m-t-md">
                        <small class="pull-right">
                            <i class="fa fa-clock-o"> </i>
                            {{\Carbon\Carbon::now()->format('Y-m-d')}} 更新
                        </small>
                        <small>
                            {{--<strong>说明：</strong> 本期销售额比上期增长了23%。--}}
                        </small>
                    </div>

                </div>
            </div>
        </div>


    </div>

    <!--table-->
    <div class="row">

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>分销员排行TOP5</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>

                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>分销员</th>
                                <th>用户数</th>
                                @if(settings('distribution_level')>1)
                                    <th>下级分销员</th>
                                @endif
                                <th>订单数</th>
                                <th>累计佣金(元)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($agents as $item)
                                <tr>
                                    <td>
                                        <div class="thumb"><img src="{{$item->user->avatar}}" width="50" height="50">
                                        </div>
                                        <p>{{$item->name}}</p>
                                        <p>{{$item->mobile}}</p>
                                    </td>
                                    <td>
                                        <a href="{{route('admin.distribution.agent.agentUsers',['id'=>$item->id])}}">{{$item->manyUsers()->count()}}</a>
                                    </td>

                                    @if(settings('distribution_level')>1)
                                        <td>
                                            @for($i=2;$i<=settings('distribution_level');$i++)
                                                {{$i}}级分销员数：{{$item->subAgentsCount($i)}}<br>
                                            @endfor
                                        </td>
                                    @endif

                                    <td>
                                        <a href="{{route('admin.distribution.agent.orders.index',['id'=>$item->id,'status'=>'STATUS_ALL'])}}">
                                            {{$item->orders()->count()}}
                                        </a>
                                    </td>
                                    <td>{{$item->calculateCash()}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>
{{--@endsection--}}

{{--@section('after-scripts-end')--}}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/echart/echarts.common.min.js') !!}
    <script>
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: '最近7天分销数据图'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                    label: {
                        backgroundColor: '#6a7985'
                    }
                }
            },
            legend: {
//                data: ['分销员', '分销订单', '注册用户', '产生佣金']
                data: ['分销员', '分销订单', '注册用户']
            },
            toolbox: {
                feature: {
                    saveAsImage: {show: true}
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            yAxis: [
                {
                    type: 'value'
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。

        $.ajax({
            type: 'GET',
            url: '{{route('admin.distribution.getDashBoardData')}}',
            success: function (result) {
                option.xAxis = [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: result.data.date
                    }
                ];

                option.series = [
                    {
                        name: '分销员',
                        type: 'line',
                        smooth: true,
                        data: result.data.agentCount
                    },
                    {
                        name: '分销订单',
                        type: 'line',
                        smooth: true,
                        data: result.data.orderCount
                    },
                    {
                        name: '注册用户',
                        type: 'line',
                        smooth: true,
                        data: result.data.userCount
                    }
//                    {
//                        name: '产生佣金',
//                        type: 'line',
//                        stack: '总量',
//                        label: {
//                            normal: {
//                                show: true,
//                                position: 'top'
//                            }
//                        },
//                        areaStyle: {normal: {}},
//                        data: result.data.cashCount
//                    }
                ];

                myChart.setOption(option);
            }
        });
    </script>
{{--@stop--}}