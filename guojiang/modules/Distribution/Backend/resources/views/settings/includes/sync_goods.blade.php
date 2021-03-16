@extends('backend-distribution::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    同步商品
@stop

@section('body')
    <div class="row">
        <form class="form-horizontal">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-sm-2 control-label">推广：</label>
                    <div class="col-sm-10">
                        <label class="control-label">
                            <input type="radio" value="1" name="activity">
                            参与
                            &nbsp;&nbsp;
                            <input type="radio" value="0" name="activity" checked>
                            不参与
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">普通佣金比例：</label>
                    <div class="col-sm-10">
                        <div class="input-group m-b">
                            <input class="form-control" value="5" name="rate" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">推客佣金比例：</label>
                    <div class="col-sm-10">
                        <div class="input-group m-b">
                            <input class="form-control" value="5" name="rate_organ" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">门店佣金比例：</label>
                    <div class="col-sm-10">
                        <div class="input-group m-b">
                            <input class="form-control" value="5" name="rate_shop" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-danger">
                    以上设置只对第一次同步商品生效
                </div>
            </div>
        </form>
    </div>

    <div class="row" id="progress" style="display: none;">
        <div class="progress progress-striped active">
            <div style="width: 0%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="75" role="progressbar"
                 class="progress-bar progress-bar-danger">
                <span class="sr-only">40% Complete (success)</span>
            </div>
        </div>
        <div id="message"></div>
    </div>
@stop

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}

@section('footer')
    <button type="button" class="btn btn-primary" id="sync-goods">开始同步</button>

    <script>
        $(function () {
            $('.form-horizontal').find("input").iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '20%'
            });
        });

        var syncUrl = '{{route('admin.distribution.goods.postSyncGoods')}}';
        function _get(url) {
            $.post(url, {
                rate: $('input[name="rate"]').val(),
                rate_organ: $('input[name="rate_organ"]').val(),
                rate_shop: $('input[name="rate_shop"]').val(),
                activity: $('input[name="activity"]').val(),
                _token: $('meta[name="_token"]').attr('content')
            }, function (result) {
                if (result.data.status == 'goon') {
                    var current = result.data.current_page;
                    var total = result.data.total;
                    var process = (current / total).toFixed(2);
                    $('.progress-bar').css('width', (process * 100 - 2) + '%');
                    _get(result.data.url);

                } else {
                    $('.progress-bar').css('width', '100%');
                    swal({
                        title: "同步成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location = '{{route('admin.distribution.goods.setting',['status'=>'ACTIVITY'])}}';
                    });
                }
            });
        }


        $(function () {
            $('#sync-goods').on('click', function () {
                $('#progress').show();
                $('input[name="rate"]').attr("disabled", true);
                $('input[name="activity"]').attr("disabled", true);
                $(this).text('正在同步...');
                $(this).attr("disabled", true);
                _get(syncUrl);
            });
        });
    </script>
@stop






