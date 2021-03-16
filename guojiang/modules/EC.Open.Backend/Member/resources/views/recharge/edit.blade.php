{!! Html::style(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.min.css') !!}
<div class="tabs-container">
    @if (session()->has('flash_notification.message'))
        <div class="alert alert-{{ session('flash_notification.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {!! session('flash_notification.message') !!}
        </div>
    @endif

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> 编辑储值规则({{$recharge->name}})</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">
                <div class="row">
                    {!! Form::open( [ 'url' => [route('admin.users.recharge.update',['id'=>$recharge->id])], 'method' => 'POST', 'id' => 'store-recharge-form','class'=>'form-horizontal'] ) !!}
                    <div class="form-group">
                        <label class="col-sm-2 control-label">*储值规则名称:</label>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control" name="type" value="gift_recharge"/>
                            <input type="text" class="form-control" name="name" placeholder="" required="required" value="{{$recharge->name}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">副标题(前端显示):</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="title" placeholder="" value="{{$recharge->title}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">*实付金额(元):</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control num" name="payment_amount" placeholder="" required="required" value="{{$recharge->payment_amount/100}}"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">*到账金额(元):</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control num" name="amount" placeholder="" required="required" value="{{$recharge->amount/100}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">赠送优惠券：</label>
                        <div class="col-sm-10">
                            <label class="checkbox-inline i-checks"><input name="open_coupon" type="radio"
                                                                           value="1"
                                                                           @if($recharge->open_coupon==1)
                                                                           checked
                                        @endif
                                >
                                是</label>
                            <label class="checkbox-inline i-checks"><input name="open_coupon" type="radio"
                                                                           value="0"
                                                                       @if($recharge->open_coupon==0)
                                                                           checked
                                                                        @endif
                                >否</label>
                        </div>
                    </div>


                    <div class="form-group" style="display: none">
                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="coupon_title" name="coupon_title" placeholder="请输入进行中的优惠券名称搜索" />
                        </div>
                        <div class="col-sm-2">
                            <a href="javascript:;" class="btn btn-w-m btn-info" id="search" >搜索</a>
                        </div>
                    </div>
                    <div class="form-group">

                        <label class="col-sm-2 control-label"></label>
                        <div class="col-sm-4">
                            <select class="form-control select_coupon" name="coupon" >
                                <option id="option_coupon" value="">请选择优惠券</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="display: none">
                        <label class="col-sm-2 control-label" >赠送积分：</label>
                        <div class="col-sm-10">
                            <label class="checkbox-inline i-checks"><input name="open_point" type="radio"
                                                                           value="1"
                                                                           @if($recharge->open_point==1)
                                                                           checked
                                        @endif>
                                是</label>
                            <label class="checkbox-inline i-checks"><input name="open_point" type="radio"
                                                                           value="0"
                                                                           @if($recharge->open_point==0)
                                                                           checked
                                                                           @endif

                                > 否</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">赠送积分：</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control num" name="point" placeholder="输入赠送积分数目" value="{{$recharge->point}}" />
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-sm-2 control-label">*开启状态：</label>
                        <div class="col-sm-10">
                            <label class="checkbox-inline i-checks"><input name="status" type="radio"
                                                                           value="1"
                                                                           @if($recharge->status==1)
                                                                           checked
                                                                            @endif

                                >
                                是</label>
                            <label class="checkbox-inline i-checks"><input name="status" type="radio"
                                                                           value="0"

                                                                           @if($recharge->status==0)
                                                                           checked
                                                                           @endif


                                > 否</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">*排序：</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control num" name="sort" placeholder="" value="{{$recharge->sort}}"  required="required"  />
                        </div>
                    </div>


                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.zclip/jquery.zclip.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.zh-CN.js') !!}
<script>
    var coupons = JSON.parse("{{$coupon}}");
    var coupon_api="{{route('admin.users.recharge.api.coupon',['status'=>'ing'])}}"


    $('.num').bind('input propertychange', function (e) {
        var value = $(e.target).val()
        if (!/^[-]?[0-9]*\.?[0-9]+(eE?[0-9]+)?$/.test(value)) {
            value = value.replace(/[^\d.].*$/, '');
            $(e.target).val(value);
        }
    });

    $('#search').click(function () {
        var url=coupon_api;
        var coupon_title=$('#coupon_title').val();
        if(coupon_title!=='') url=url+"&"+'title='+coupon_title;
        $('.select_coupon').html('');
        $.get(url,function (res) {
            if(res.status){
                var data=res.data;
                var html="<option value=''>"+"请选择优惠券"+"</option>";
                if(data.length>0){
                    $.each(data,function (k,v) {
                        html+="<option value="+v.id+"  >"+v.title+"</option>";
                    })
                }else{
                    var html="<option value=''>"+"无进行中的优惠券"+"</option>";
                }

                $('.select_coupon').html(html);

                $(function () {
                    console.log(coupons);
                    $('.select_coupon').each(function (i,e) {
                        $(e).val(coupons[i]);
                    })
                })
            }
        })
    });

    $('#search').trigger("click");

    $('input[name=point]').change(function () {
        var num=$('input[name=point]').val();
        var reg = /^\d+(?=\.{0,1}\d+$|$)/;
        if(!reg.test(num)) {
            $('input[name=point]').val(0);
        }
    });

    $('input[name=payment_amount]').change(function () {
        var num=$('input[name=payment_amount]').val();
        var reg = /^\d+(?=\.{0,1}\d+$|$)/;
        if(!reg.test(num)) {
            $('input[name=payment_amount]').val('');
        }
    });

    $('input[name=amount]').change(function () {
        var num=$('input[name=amount]').val();
        var reg = /^\d+(?=\.{0,1}\d+$|$)/;
        if(!reg.test(num)) {
            $('input[name=amount]').val('');
        }
    });

    $('input[name=sort]').change(function () {
        var num=$('input[name=sort]').val();
        var reg = /^\d+(?=\.{0,1}\d+$|$)/;
        if(!reg.test(num)) {
            $('input[name=sort]').val('');
        }
    });

    $('#store-recharge-form').ajaxForm({
        beforeSubmit:function (data) {
            var input = [];
            $.each(data, function (k, v) {
                if (v.name !== "lenght") {
                    input[v.name] = v.value;
                }
            });

            if (input['payment_amount'] <= 0) {
                swal({
                    title: "保存失败",
                    text: "实付金额必须大于0",
                    type: "error"
                });
                return false;
            }

            if (input['amount'] <= 0) {
                swal({
                    title: "保存失败",
                    text: "到账金额必须大于0",
                    type: "error"
                });
                return false;
            }

            if (input['payment_amount'] - input['amount']>0) {
                swal({
                    title: "保存失败",
                    text: "到账金额不能小于实付金额",
                    type: "error"
                });
                return false;
            }

            if (input['payment_amount'] < input['amount'] || input['open_coupon']==1|| input['open_point']==1) {
                if (input['title'] == '') {
                    console.log( input['open_point']);
                    swal({
                        title: "保存失败",
                        text: "请输入副标题(前端显示):",
                        type: "error"
                    });
                    return false;
                }
            }

            if(input['point'] <0 ){
                swal({ title: "保存失败", text: "积分的数目非负数",type: "error"  });
                return false;
            }
            if(input['sort'] <=0){
                swal({ title: "保存失败", text: "排序非负数非0",type: "error"  });
                return false;
            }

            if (input['open_coupon']==1) {
                console.log(input['coupon']);
                if(input['coupon'] == "" || typeof(input['coupon'])=="undefined"){
                    swal({
                        title: "保存失败",
                        text: "请选择优惠券",
                        type: "error"
                    });
                    return false;
                }
            }

        },

        success: function (result) {
            if(!result.status)
            {
                swal("保存失败!", result.error, "error")
            }else{
                swal({
                    title: "保存成功！",
                    text: "",
                    type: "success"
                }, function() {
                    location = '{{route('admin.users.recharge.index')}}';
                });
            }

        }
    });
</script>