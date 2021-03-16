@extends('backend-distribution::layouts.bootstrap_modal')

@section('modal_class')
    modal-md
@stop
@section('title')
    批量编辑分销商品{{$type=='status'?'参与状态':'佣金'}}
@stop

@section('body')
    <div class="row">
        <form class="form-horizontal" method="post" id="base-form"
              action="{{route('admin.distribution.goods.saveBatchGoods')}}">
            {{csrf_field()}}
            <input type="hidden" name="ids" value="{{$ids}}">
            <input type="hidden" name="type" value="{{$type}}">
            <input type="hidden" name="value" value="{{$value}}">
            <input type="hidden" name="status" value="{{$status}}">
            <div class="col-md-8">
                @if($type=='status')
                    <div class="form-group">
                        <label class="col-sm-6 control-label">参与分销：</label>
                        <div class="col-sm-6">
                            <label class="control-label">
                                <input type="radio" value="1" name="activity" checked>
                                参与
                                &nbsp;&nbsp;
                                <input type="radio" value="0" name="activity">
                                不参与
                            </label>
                        </div>
                    </div>
                @else
                    @if($rate_type=='default')
                        <div class="form-group">
                            <label class="col-sm-6 control-label">普通佣金比例：</label>
                            <div class="col-sm-6">
                                <div class="input-group m-b">
                                    <input class="form-control" value="5" name="rate" type="text"> <span
                                            class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    @elseif($rate_type=='organ')

                        <div class="form-group">
                            <label class="col-sm-6 control-label">机构佣金比例：</label>
                            <div class="col-sm-6">
                                <div class="input-group m-b">
                                    <input class="form-control" value="5" name="rate_organ" type="text"> <span
                                            class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="col-sm-6 control-label">门店佣金比例：</label>
                            <div class="col-sm-6">
                                <div class="input-group m-b">
                                    <input class="form-control" value="5" name="rate_shop" type="text"> <span
                                            class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
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






