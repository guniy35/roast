{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}

<div class="tabs-container">
    <ul class="nav nav-tabs">
        <li class="{{ Active::query('status','') }}">
            <a no-pjax href="{{route('admin.promotion.seckill.index')}}">所有活动</a>
        </li>

        <li class="{{ Active::query('status','future') }}">
            <a no-pjax href="{{route('admin.promotion.seckill.index',['status'=>'future'])}}">未开始</a>
        </li>

        <li class="{{ Active::query('status','on') }}">
            <a no-pjax href="{{route('admin.promotion.seckill.index',['status'=>'on'])}}">进行中</a>
        </li>

        <li class="{{ Active::query('status','end') }}">
            <a no-pjax href="{{route('admin.promotion.seckill.index',['status'=>'end'])}}">已结束</a>
        </li>

    </ul>
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <div class="panel-body">
                {!! Form::open( [ 'route' => ['admin.promotion.seckill.index'], 'method' => 'get', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}
                <input type="hidden" name="status" value="{{request('status')}}">
                <div class="form-group">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="title" placeholder="输入秒杀活动标题搜索">
                    </div>

                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary">搜索</button>

                        <a no-pjax href="{{ route('admin.promotion.seckill.create')}}"
                           class="btn btn-primary">新建秒杀活动</a>
                    </div>

                    <div class="col-sm-6">

                        <label class="col-sm-5 control-label">秒杀活动列表页前端链接：</label>
                        <div class="col-sm-7" style="position: relative">
                            <input type="text"
                                   value="{{settings('mobile_domain_url')}}/#!/store/seckill">
                            <label class="label label-danger copyBtn">复制链接</label>
                        </div>

                    </div>

                </div>
                {!! Form::close() !!}

                <div class="table-responsive">
                    @include('store-backend::seckill.includes.list')

                </div><!-- /.box-body -->

            </div>
        </div>
    </div>
</div>

{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
<script>
    $('.close-seckill').on('click', function () {
        var postUrl = $(this).data('url');

        swal({
            title: "您真的要执行该操作吗?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            cancelButtonText: '取消',
            closeOnConfirm: false
        }, function () {
            $.post(postUrl, {_token: _token}, function (result) {
                if (!result.status) {
                    swal("操作失败!", result.message, "error")
                } else {
                    swal({
                        title: "操作成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location = '{{route('admin.promotion.seckill.index')}}';
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