{!! Html::style('assets/backend/libs/webuploader-0.1.5/webuploader.css') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        {!! Form::open( [ 'url' => [route('admin.balance.cash.applyPay')], 'method' => 'POST','id' => 'base-form','class'=>'form-horizontal'] ) !!}
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
            <label class="control-label col-lg-2">审核时间：</label>
            <div class="col-lg-9">
                <p class="form-control-static">{{$cash->updated_at}}</p>
            </div>
        </div>

        @if($cash->cash_type=='customer_account')
            <div class="form-group">
                <label class="control-label col-lg-2">收款信息：</label>
                <div class="col-lg-9">
                    <div class="alert alert-danger">
                        <p class="form-control-static">收款人：{{$cash->owner_name}}</p>
                        <p class="form-control-static">收款银行：{{$cash->bank_name}}</p>
                        <p class="form-control-static">收款账号：{{$cash->bank_number}}</p>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-lg-2">处理：</label>
                <div class="col-lg-9">
                    <input type="checkbox" name="status" value="2"> 已确认打款
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2">上传打款凭证：</label>
                <div class="col-lg-9">
                    <div id="imglist">
                        <input type="hidden" name="cert">
                    </div>
                    <div class="clearfix" style="padding-top: 22px;">
                        <div id="filePicker">添加图片</div>
                        {{--<p style="color: #b6b3b3">温馨提示：图片尺寸建议为：250*250, 图片小于4M</p>--}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2">打款时间：</label>
                <div class="col-lg-9">
                    <div class="input-group date form_datetime">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;打款时间</span>
                        <input type="text" class="form-control inline" name="settle_time"
                               placeholder="打款时间 " readonly>
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>
                </div>
            </div>
        @else
            <div class="form-group">
                <label class="control-label col-lg-2">收款信息：</label>
                <div class="col-lg-9">
                    <div class="alert alert-danger">
                        <p>用户微信钱包</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8 controls">
                <button type="submit" id="doPay" class="btn btn-primary">确认打款</button>

                <a href="{{route('admin.balance.cash.index',['status'=>'STATUS_AUDIT'])}}"
                   class="btn btn-danger">返回</a>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
{!! Html::script('assets/backend/libs/webuploader-0.1.5/webuploader.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
<script>
	$.getScript('{{env("APP_URL").'/assets/wechat-backend/libs/datepicker/bootstrap-datetimepicker.js'}}', function () {
		$.fn.datetimepicker.dates['zh-CN'] = {
			days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
			daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
			daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
			months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
			monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
			today: "今天",
			suffix: [],
			meridiem: ["上午", "下午"]
		};

		$('.form_datetime').datetimepicker({
			language: 'zh-CN',
			weekStart: 1,
			todayBtn: 1,
			autoclose: 1,
			todayHighlight: 1,
			startView: 2,
			forceParse: 0,
			showMeridian: 1,
			minuteStep: 1
		});
	});


	$(function () {
		var imglist = $('input[name="cert"]');
		// 初始化Web Uploader
		var postImgUrl = '{{route('upload.image',['_token'=>csrf_token()])}}';
		// 初始化Web Uploader
		var uploader = WebUploader.create({
			auto: true,
			swf: '{{url('assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
			server: '{{route('upload.image',['_token'=>csrf_token()])}}',
			pick: '#filePicker',
			fileVal: 'upload_image',
			accept: {
				title: 'Images',
				extensions: 'jpg,jpeg,png',
				mimeTypes: 'image/jpg,image/jpeg,image/png'
			}
		});
		// 文件上传成功，给item添加成功class, 用样式标记上传成功。
		uploader.on('uploadSuccess', function (file, response) {
			$("#imglist").append("<img width='100' src='" + response.url + "'/>");
			var imgs = imglist.val();
			if (!imgs) {
				imgs = response.url;
			} else {
				imgs = imgs + ';' + response.url;
			}
			imglist.val(imgs);
		});
	});


	$('#base-form').ajaxForm({
		beforeSubmit: function () {
			$('#doPay').attr('disabled', true);
		},
		success: function (result) {
			if (!result.status) {
				swal("操作失败!", result.message, "error")
			} else {
				swal({
					title: "操作成功！",
					text: "",
					type: "success"
				}, function () {
					location = '{{route('admin.balance.cash.index',['status'=>'STATUS_PAY'])}}'
				});
			}

		}
	})
	;
</script>