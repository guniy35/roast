
    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">

            <a href="{{ route('admin.marketing.sign.create') }}" class="btn btn-primary margin-bottom">新建签到活动</a>

            <div class="hr-line-dashed"></div>

            <div class="box box-primary">

                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <!--tr-th start-->
                        <tr>
                            <th>活动名称</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        <!--tr-th end-->

                        @foreach ($markets as $item)
                            <tr>
                                <td>{{$item->title}}</td>
                                <td>{{$item->status?'有效':'禁用'}}</td>
                                <td>
                                    <a
                                            class="btn btn-xs btn-primary"
                                            href="{{route('admin.marketing.sign.edit',['id'=>$item->id])}}">
                                        <i data-toggle="tooltip" data-placement="top"
                                           class="fa fa-pencil-square-o"
                                           title="编辑"></i></a>

                                    <a class="btn btn-xs btn-danger del-market" href="javascript:;"
                                       data-href="{{route('admin.marketing.sign.delete',['id'=>$item->id])}}">
                                        <i data-toggle="tooltip" data-placement="top"
                                           class="fa fa-trash"
                                           title="删除"></i></a>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer clearfix">
                    {!!$markets->render() !!}
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('.del-market').on('click', function () {
                var that = $(this);
                var url = that.data('href') + "?_token=" + _token;

                swal({
                    title: "确定删除该活动吗?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText:"取消",
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "删除",
                    closeOnConfirm: false
                }, function () {
                    $.post(url, function (ret) {
                        if(ret.status){
                            swal({
                                title: "删除成功！",
                                text: "",
                                type: "success"
                            }, function() {
                                location.reload();
                            });
                        }else{
                            swal('删除失败','','error');
                        }
                    });
                });
            });
        });
    </script>