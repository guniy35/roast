
    <div class="tabs-container">
        <ul class="nav nav-tabs">
            <li class="{{ Active::query('status','') }}">
                <a href="{{route('admin.promotion.reduce.index')}}">所有活动</a>
            </li>

            <li class="{{ Active::query('status','future') }}">
                <a href="{{route('admin.promotion.reduce.index',['status'=>'future'])}}">未开始</a>
            </li>

            <li class="{{ Active::query('status','on') }}">
                <a href="{{route('admin.promotion.reduce.index',['status'=>'on'])}}">进行中</a>
            </li>

            <li class="{{ Active::query('status','end') }}">
                <a href="{{route('admin.promotion.reduce.index',['status'=>'end'])}}">已结束</a>
            </li>

            <li class="{{ Active::query('status','invalid') }}">
                <a href="{{route('admin.promotion.reduce.index',['status'=>'invalid'])}}">已失效</a>
            </li>
        </ul>
        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">

                <div class="panel-body">
                    {!! Form::open( [ 'route' => ['admin.promotion.reduce.index'], 'method' => 'get', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}
                    <input type="hidden" name="status" value="{{request('status')}}">
                    <div class="form-group">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="title" value="{{request('title')}}" placeholder="输入砍价活动标题搜索">
                        </div>

                        <div class="col-sm-3">
                            <button type="submit" class="btn btn-primary">搜索</button>

                            <a href="{{ route('admin.promotion.reduce.create')}}"
                               class="btn btn-primary">新建活动</a>

                        </div>

                        <div class="col-sm-3 pull-right">

                            <a class="btn btn-success pull-right"  id="getHelpTextModal" data-toggle="modal"
                               data-target="#modal" data-backdrop="static" data-keyboard="false"
                               data-url="{{route('admin.promotion.reduce.getHelpTextModal')}}">
                                活动规则
                            </a>
                        </div>


                    </div>
                    {!! Form::close() !!}

                    <div class="table-responsive">
                        @include('store-backend::reduce.includes.list')

                    </div><!-- /.box-body -->

                </div>
            </div>
        </div>
    </div>


    <div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>
    <div id="download_modal" class="modal inmodal fade"></div>

    {!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
    <script>

        $('.delete').on('click',function () {
            var postUrl=$(this).data('url');

            swal({
                title: "确认执行操作?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                cancelButtonText: '取消',
                closeOnConfirm: false
            }, function () {
                $.post(postUrl,{_token:_token},function (result) {
                    if (!result.status) {
                        swal("操作失败!", result.message, "error")
                    } else {
                        swal({
                            title: "操作成功！",
                            text: "",
                            type: "success"
                        }, function () {
                            location = '{{route('admin.promotion.reduce.index')}}';
                        });
                    }
                })
            });
        });
    </script>
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.zclip/jquery.zclip.js') !!}
    <script>
        $('.copyBtn').zclip({
            path: "{{url('assets/backend/libs/jquery.zclip/ZeroClipboard.swf')}}",
            copy: function () {
                return $(this).prev().val();
            }
        });
    </script>



