@extends('backend-distribution::layouts.bootstrap_modal')

@section('modal_class')
    modal-md
@stop
@section('title')
    编辑分销商品
@stop

@section('body')
    <div class="row">
        <form class="form-horizontal" method="post" id="base-form"
              action="{{route('admin.distribution.goods.saveGoods')}}">
            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$goods->id}}">
            <div class="col-md-8">
                <div class="form-group">
                    <label class="col-sm-6 control-label">推广：</label>
                    <div class="col-sm-6">
                        <label class="control-label">
                            <input type="radio" value="1" name="activity" {{$goods->activity==1?'checked':''}}>
                            参与
                            &nbsp;&nbsp;
                            <input type="radio" value="0" name="activity" {{$goods->activity==0?'checked':''}}>
                            不参与
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label">普通佣金比例：</label>
                    <div class="col-sm-6">
                        <div class="input-group m-b">
                            <input class="form-control" value="{{$goods->rate}}" name="rate" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label">机构佣金比例：</label>
                    <div class="col-sm-6">
                        <div class="input-group m-b">
                            <input class="form-control" value="{{$goods->rate_organ}}" name="rate_organ" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-6 control-label">门店佣金比例：</label>
                    <div class="col-sm-6">
                        <div class="input-group m-b">
                            <input class="form-control" value="{{$goods->rate_shop}}" name="rate_shop" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}

@section('footer')
    <button type="button" class="btn btn-link" data-dismiss="modal">取消</button>
    <button type="submit" class="btn btn-primary" data-toggle="form-submit" data-target="#base-form">提交</button>

    <script>
        $(function () {
            $('#base-form').find("input").iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '20%'
            });
        });

        $('#base-form').ajaxForm({
            success: function (result) {
                if (result.status) {
                    swal({
                        title: "设置成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location.reload();
                    });
                }

            }
        });
    </script>
@stop






