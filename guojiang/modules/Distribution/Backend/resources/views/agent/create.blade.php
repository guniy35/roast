<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">

        {!! Form::open( [ 'url' => [route('admin.distribution.agent.store')], 'method' => 'POST', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}


        <div class="form-group">
            {!! Form::label('type','关联用户：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <div class="input-group">
                    <input type="text" id="search-mobile" placeholder="请输入手机号码搜索" value=""
                           class=" form-control"> <span class="input-group-btn">
                                            <button type="button" id="search" class="btn btn-primary">搜索
                                            </button> </span></div>

                <div>
                    <div class="ibox float-e-margins" style="display:none" id="user-list">
                        <div class="ibox-title">
                            <h5>用户列表(请点击选择)</h5>
                        </div>
                        <div class="ibox-content">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>手机</th>
                                    <th>昵称</th>
                                </tr>
                                </thead>
                                <tbody id="user-tr">

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','姓名：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <input type="text" class="form-control" name="name" placeholder="">
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('type','手机号码：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <input type="text" class="form-control" name="mobile" placeholder="">
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','角色：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="1" checked>
                        普通推客
                    </label>

                    <label>
                        <input type="radio" name="type" value="2">
                        机构推客
                    </label>

                    <label>
                        <input type="radio" name="type" value="3">
                        门店推客
                    </label>

                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('is_search','状态：', ['class' => 'col-lg-2 control-label']) !!}
            <div class="col-lg-9">
                <input value="1" type="radio" name="status">正常&nbsp;&nbsp;
                <input value="0" type="radio" name="status">待审核&nbsp;&nbsp;
                <input value="3" type="radio" name="status">清退
            </div>
        </div>

        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8 controls">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
{{--@endsection--}}

{{--@section('before-scripts-end')--}}
{!! Html::script('assets/backend/libs/jquery.form.min.js') !!}
<script type="text/x-template" id="user">
    <tr>
        <th><input type="radio" class="check_user" name="user_id" value="{VALUE}" data-mobile="{MOBILE}"
                   data-name="{NAME}"></th>
        <th>{MOBILE}</th>
        <th>{NAME}</th>
    </tr>
</script>

<script>
	$('#search').click(function () {
		var mobile = $('#search-mobile').val();
		if (!mobile) {
			swal('请输入手机号码搜索', '', 'warning');
			return;
		}

		$.get('{{route('admin.distribution.agent.searchUser')}}', {mobile: mobile}, function (result) {
			if (!result.status) {
				swal(result.message, '', 'warning');
			} else {
				$('#user-list').show();
				$('#user-tr').html('');
				result.data.user.forEach(function (item) {
					var action_html = $('#user').html();
					$('#user-tr').append(action_html.replace(/{VALUE}/g, item.id).replace(/{NAME}/g, item.nick_name).replace(/{MOBILE}/g, item.mobile));

				});
				$('#user-tr').find("input").iCheck({
					checkboxClass: 'icheckbox_square-green',
					radioClass: 'iradio_square-green',
					increaseArea: '20%'
				});
			}

			$('.check_user').on('ifChecked', function (event) {
				var that = $(this);
				var name = that.data('name');
				var mobile = that.data('mobile');
				$('input[name="name"]').val(name);
				$('input[name="mobile"]').val(mobile);
			});
		});

	});


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
{{--@stop--}}
