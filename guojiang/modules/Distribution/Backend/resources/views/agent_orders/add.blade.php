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

<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        {!! Form::open( [ 'url' => [route('admin.distribution.agent.orders.postAgentOrder')], 'method' => 'POST', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}
        <input type="hidden" name="agent_id" value="{{$agent->id}}">

        <div class="form-group">
            {!! Form::label('type','清客信息：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <div class="input-group">
                    <div class="thumb"><img src="{{$agent->user->avatar}}" width="50" height="50"></div>
                    <p style="float: left">{{$agent->name}}</p>
                    <p style="float: left; margin-left: 20px">{{$agent->mobile}}</p>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','佣金金额：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <div class="input-group m-b">
                    <input class="form-control number_valid" value="" name="commission" type="text">
                    <span class="input-group-addon">元</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','账期：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <div class="input-group m-b">
                    <input class="form-control number_valid" value="" name="days" type="text">
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>

        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8 controls">
                <button type="submit" class="btn btn-primary">保存</button>

                <a href="javascript:history.go(-1)"
                   class="btn btn-danger">返回</a>
            </div>
        </div>

        {!! Form::close() !!}
                <!-- /.tab-content -->
    </div>
</div>

{!! Html::script('assets/backend/libs/jquery.form.min.js') !!}

<script>
    $('#base-form').ajaxForm({
        success: function (result) {
            if (!result.status) {
                swal("保存失败!", result.message, "error")
            } else {
                swal({
                    title: "保存成功！",
                    text: "",
                    type: "success"
                }, function () {
                    location = '{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDITED'])}}';
                });
            }

        }

    });
</script>