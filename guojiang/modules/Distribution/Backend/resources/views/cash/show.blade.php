<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        {!! Form::open( [ 'url' => [route('admin.balance.cash.review')], 'method' => 'POST','id' => 'base-form','class'=>'form-horizontal'] ) !!}
        <input type="hidden" name="id" value="{{$cash->id}}">
        <div class="form-group">
            <label class="control-label col-lg-2">申请编号：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->cash_no}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-2">申请人：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->agent->name}}</p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2">申请人手机：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->agent->mobile}}</p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2">申请金额：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->amount}} 元</p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2">申请时间：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->created_at}} </p>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2">状态：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->status_text}}</p>
            </div>
        </div>


        <div class="form-group">
            <label class="control-label col-lg-2">收款信息：</label>
            <div class="col-lg-9">
                <div class="alert alert-danger">
                    @if($cash->cash_type=='customer_account')
                        <p class="form-control-static">收款人：{{$cash->owner_name}}</p>
                        <p class="form-control-static">收款银行：{{$cash->bank_name}}</p>
                        <p class="form-control-static">收款账号：{{$cash->bank_number}}</p>
                    @else
                        <p>用户微信钱包</p>
                    @endif
                </div>
            </div>
        </div>


        @if($cash->status != 0)
            <div class="form-group">
                <label class="control-label col-lg-2">处理时间：</label>
                <div class="col-lg-9">
                    <p class="form-control-static">{{$cash->updated_at}}</p>
                </div>
            </div>
        @endif
        @if($cash->status==2)
            <div class="form-group">
                <label class="control-label col-lg-2">打款时间：</label>
                <div class="col-lg-9">
                    <p class="form-control-static">{{$cash->settle_time}}</p>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2">打款凭证：</label>
                <div class="col-lg-9">
                    @foreach($cash->cert as $key=>$item)
                        <img src="{{$item}}" width="100">
                    @endforeach
                </div>
            </div>
        @endif

        @if($cash->status == 0)
            <div class="form-group">
                <label class="control-label col-lg-2">处理：</label>
                <div class="col-lg-9">
                    <label>
                        <input type="radio" name="status" value="1" checked="">
                        通过
                    </label>
                    &nbsp;&nbsp;
                    <label>
                        <input type="radio" name="status" value="3">
                        不通过
                    </label>
                </div>
            </div>

            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-8 controls">
                    <button type="submit" class="btn btn-primary">提交</button>

                    <a href="{{route('admin.balance.cash.index',['status'=>'STATUS_AUDIT'])}}"
                       class="btn btn-danger">返回</a>
                </div>
            </div>
        @else
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-md-offset-2 col-md-8 controls">
                    <a href="{{route('admin.balance.cash.index',['status'=>'STATUS_AUDIT'])}}"
                       class="btn btn-danger">返回</a>
                </div>
            </div>
        @endif
        {!! Form::close() !!}
    </div>
</div>

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
<script>
	$('#base-form').ajaxForm({
		success: function (result) {
			if (!result.status) {
				swal("操作失败!", result.error, "error")
			} else {
				swal({
					title: "操作成功！",
					text: "",
					type: "success"
				}, function () {
					window.location.reload();
				});
			}

		}
	});
</script>