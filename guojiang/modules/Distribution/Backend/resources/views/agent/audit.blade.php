<style type="text/css">
    .form-horizontal label {
        text-align: right;
    }
</style>
<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        {!! Form::open( [ 'url' => [route('admin.distribution.agent.saveAgent')], 'method' => 'POST','id' => 'base-form','class'=>'form-horizontal'] ) !!}
        <input type="hidden" name="id" value="{{$agent->id}}">
        <div class="form-group">
            {!! Form::label('name','姓名：', ['class' => 'col-lg-2']) !!}
            <div class="col-lg-9">
                {{$agent->name}}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','手机：', ['class' => 'col-lg-2']) !!}
            <div class="col-lg-9">
                {{$agent->mobile}}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','申请时间：', ['class' => 'col-lg-2']) !!}
            <div class="col-lg-9">
                {{$agent->created_at}}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','角色：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="1" {{$agent->type==1?'checked':''}} >
                        普通推客
                    </label>

                    <label>
                        <input type="radio" name="type" value="2" {{$agent->type==2?'checked':''}}>
                        机构推客
                    </label>

                    <label>
                        <input type="radio" name="type" value="3" {{$agent->type==3?'checked':''}}>
                        门店推客
                    </label>

                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('status','状态：', ['class' => 'col-lg-2']) !!}
            <div class="col-lg-9">
                @if($agent->status==0)
                    待审核
                @elseif($agent->status==2)
                    审核未通过
                @endif
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','备注：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <textarea class="form-control" name="note">{{$agent->note}}</textarea>
            </div>
        </div>


        <div class="form-group">
            {!! Form::label('name','审核意见：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <div class="radio">
                    <label>
                        <input type="radio" name="status" value="1" checked>
                        通过
                    </label>

                    <label>
                        <input type="radio" name="status" value="2">
                        不通过
                    </label>
                </div>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8 controls">
                <button type="submit" class="btn btn-primary">保存</button>

                <a href="{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDIT'])}}"
                   class="btn btn-danger">返回</a>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
<script>
	$('#base-form').ajaxForm({
		success: function (result) {
			if (!result.status) {
				swal("保存失败!", result.error, "error")
			} else {
				swal({
					title: "审核成功！",
					text: "",
					type: "success"
				}, function () {
					location = '{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDITED'])}}';
				});
			}

		}

	});
</script>