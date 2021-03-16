<div class="tabs-container">
    <form method="post" action="{{route('admin.store.agreement.save')}}" class="form-horizontal" id="setting_site_form">
        {{csrf_field()}}
        <div class="tab-content">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户协议:</label>
                    <div class="col-sm-8">
                        <script id="container" name="user_agreement" type="text/plain">
                            {!! $user_agreement !!}
                        </script>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 15px">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存设置</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@include('UEditor::head')
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
<script>
    var ue = UE.getEditor('container', {
	    autoHeightEnabled: false,
	    initialFrameHeight: 500
    });
    ue.ready(function () {
	    ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');//此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.

    });

    $('#setting_site_form').ajaxForm({
	    success: function (result) {
		    swal({
			    title: "保存成功！",
			    text: "",
			    type: "success"
		    }, function () {
			    location.reload();
		    });

	    }
    })
</script>