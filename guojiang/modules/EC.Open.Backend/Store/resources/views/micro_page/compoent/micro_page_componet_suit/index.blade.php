
<div class="tabs-container">

    @include('store-backend::micro_page.compoent.common.nav')

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">

                <div class="row" style="margin-bottom:20px;">

                    <div class="col-sm-4">
                        <a target="_blank" href="{{route('admin.setting.micro.page.compoent.create',['type'=>$type,'header'=>'套餐'])}}" class="btn btn-w-m btn-info">创建套餐模块</a>
                    </div>

                    <div class="col-sm-4"></div>

                    <div class="col-sm-4">

                        <form action="{{route('admin.setting.micro.page.compoent.index',$type)}}" method="get">

                            <div class="col-sm-10">
                                <div class="input-group search_text col-sm-12">
                                    <input type="text" name="name" placeholder="标题"
                                           value="{{!empty(request('name'))?request('name'):''}}" class="form-control">

                                </div>
                            </div>
                            <div class="col-sm-2">
                                <input class="btn btn-info" type="submit" value="搜索"/>
                            </div>

                        </form>

                    </div>



                </div>

                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="col-sm-2">标题</th>
                        <th class="col-sm-3">套餐名称</th>
                        <th class="col-sm-3">套餐总价</th>
                        <th class="col-sm-2">创建时间</th>
                        <th class="col-sm-1">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($lists->count())
                        @foreach($lists as $item)
                            <tr>

                                <td>
                                    {{$item->name}}
                                </td>

                                <td>
                                    @foreach($item->item as $key=> $citem)

                                        @if($citem->associate)

                                            <div>
                                                套餐{{$key+1}}:
                                                <a href="{{route('Suit',$citem->associate->id)}}" target="_blank">
                                                 {{$citem->associate->title}}
                                                </a>
                                                    @if($citem->associate->status!=1)
                                                        (活动已失效)
                                                    @else

                                                    @if($citem->associate->ends_at<=$server_time)
                                                        (活动已过期)
                                                    @endif

                                                @endif
                                            </div>



                                        @endif

                                    @endforeach
                                </td>

                                <td>
                                    @foreach($item->item as $citem)

                                        @if($citem->associate)

                                            <div>
                                                ¥ {{$citem->associate->total}}
                                            </div>

                                        @endif

                                    @endforeach
                                </td>

                                <td>{{$item->created_at}}</td>
                                <td>
                                    <a
                                            class="btn btn-xs btn-primary"
                                            href="{{route('admin.setting.micro.page.compoent.edit',['type'=>$type,'code'=>$item->code,'header'=>'套餐'])}}">
                                        <i data-toggle="tooltip" data-placement="top"
                                           class="fa fa-pencil-square-o"
                                           title="编辑"></i></a>

                                        <span  class="btn btn-xs btn-danger delete"  data-href="{{route('admin.setting.micro.page.compoent.delete',['id'=>$item->id])}}">
                                                <i class="fa fa-trash" data-toggle="tooltip" data-placement="top"

                                                   title="删除"></i>
                                            </span>

                                </td>

                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

                @if($lists->count())
                    <div class="pull-lift">
                        {!! $lists->appends(request()->except('page'))->render() !!}
                    </div>
                @endif

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>


<script>

    $('.delete').on('click', function () {
        var that = $(this);
        var postUrl = that.data('href');
        var body = {
            _token: _token
        };
        swal({
            title: "确定要删除吗？",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            cancelButtonText: '取消',
            closeOnConfirm: false
        }, function () {
            $.post(postUrl, body, function (result) {
                if (result.status) {
                    swal({
                        title: "删除成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location = '{{route('admin.setting.micro.page.compoent.index',$type)}}';
                    });
                } else {
                    swal({
                        title: '删除失败',
                        text: result.message,
                        type: "error"
                    });
                }
            });
        });
    });

</script>

