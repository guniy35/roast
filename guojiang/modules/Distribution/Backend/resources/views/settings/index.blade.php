{{--@extends('backend-distribution::layouts.default')--}}

{{--@section('breadcrumbs')--}}
    {{--<h2>分销设置</h2>--}}
    {{--<ol class="breadcrumb">--}}
        {{--<li><a href="{!!route('admin.distribution.index')!!}"><i class="fa fa-dashboard"></i>首页</a></li>--}}
        {{--<li class="active">分销设置</li>--}}
    {{--</ol>--}}
{{--@endsection--}}

{{--@section('content')--}}
    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <form method="post" action="{{route('admin.distribution.system.saveSetting')}}" class="form-horizontal"
                  id="setting_site_form">
                {{csrf_field()}}

                <div class="form-group">
                    <label class="col-sm-3 control-label">是否启用分销：</label>

                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_status" {{settings('distribution_status') ? 'checked': ''}}>
                            是
                            &nbsp;&nbsp;
                            <input type="radio" value="0"
                                   name="distribution_status" {{!settings('distribution_status') ? 'checked': ''}}>
                            否
                        </label>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label">商品默认推广状态：</label>
                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_goods_status" {{settings('distribution_goods_status') ? 'checked': ''}}>
                            参与
                            &nbsp;&nbsp;
                            <input type="radio" value="0"
                                   name="distribution_goods_status" {{!settings('distribution_goods_status') ? 'checked': ''}}>
                            不参与
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">商品默认佣金比例：</label>
                    <div class="col-sm-9">
                        <div class="input-group m-b">
                            <input class="form-control number_valid"
                                   value="{{settings('distribution_goods_rate') ? settings('distribution_goods_rate'): ''}}"
                                   name="distribution_goods_rate" type="text"> <span
                                    class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">佣金记录规则：</label>

                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_commission_for_link" {{settings('distribution_commission_for_link') ? 'checked': ''}}>
                            按分享链接
                            &nbsp;&nbsp;
                            <input type="radio" value="0"
                                   name="distribution_commission_for_link" {{!settings('distribution_commission_for_link') ? 'checked': ''}}>
                            按绑定关系
                        </label>
                    </div>
                </div>

                {{--<div class="form-group">--}}
                {{--<label class="col-sm-3 control-label">分享商品建立用户关系：</label>--}}

                {{--<div class="col-sm-9">--}}
                {{--<label class="control-label">--}}
                {{--<input type="radio" value="1"--}}
                {{--name="distribution_share_relation" {{settings('distribution_share_relation') ? 'checked': ''}}>--}}
                {{--是--}}
                {{--&nbsp;&nbsp;--}}
                {{--<input type="radio" value="0"--}}
                {{--name="distribution_share_relation" {{!settings('distribution_share_relation') ? 'checked': ''}}>--}}
                {{--否--}}
                {{--</label>--}}
                {{--</div>--}}
                {{--</div>--}}

                <div class="form-group">
                    <label class="col-sm-3 control-label">分销员购买权限<i class="fa fa-question-circle"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    data-original-title="开启分销员购买权限，分销员通过自己的推广链接购买商品可获得佣金。"></i>：</label>

                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_self_commission" {{settings('distribution_self_commission') ? 'checked': ''}}>
                            是
                            &nbsp;&nbsp;
                            <input type="radio" value="0"
                                   name="distribution_self_commission" {{!settings('distribution_self_commission') ? 'checked': ''}}>
                            否
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">分享链接有效期<i class="fa fa-question-circle"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    data-original-title="用户通过分享链接或者二维码进入商城，在有效期内都算推广效果"></i>：</label>

                    <div class="col-sm-9">
                        <div class="input-group m-b">
                            <input type="text" name="distribution_valid_time"
                                   value="{{settings('distribution_valid_time')?settings('distribution_valid_time'):10080}}"
                                   placeholder="Search"
                                   class="form-control number_valid">
                            <span class="input-group-addon">分钟</span>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3 control-label">是否开启分销招募：</label>

                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_recruit_status" {{settings('distribution_recruit_status') ? 'checked': ''}}>
                            是
                            &nbsp;&nbsp;
                            <input type="radio" value="0"
                                   name="distribution_recruit_status" {{!settings('distribution_recruit_status') ? 'checked': ''}}>
                            否
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">申请是否需要审核：</label>

                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_audit_status"
                                    {{(!settings('distribution_audit_status') OR settings('distribution_audit_status')==1) ? 'checked': ''}}>
                            是
                            &nbsp;&nbsp;
                            <input type="radio" value="2"
                                   name="distribution_audit_status" {{settings('distribution_audit_status')==2 ? 'checked': ''}}>
                            否
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">分销层级：</label>

                    <div class="col-sm-9">
                        <select name="distribution_level" class="form-control" id="distribution_level">
                            <option value="1" {{(!settings('distribution_level') OR settings('distribution_level'))==1?"selected":" "}}>
                                1级
                            </option>
                            <option value="2" {{settings('distribution_level')==2?"selected":""}}>2级</option>
                            <option value="3" {{settings('distribution_level')==3?"selected":""}}>3级</option>
                        </select>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">佣金分配比例：</label>

                    <div class="col-sm-9" id="rate_box">
                        @if($rate = settings('distribution_rate'))
                            @foreach($rate as $key=>$val)
                                <div class="input-group m-b rate_div"><span class="input-group-addon">{{$val['key']}}
                                        级佣金比例</span>
                                    <input type="hidden" name="distribution_rate[{{$key}}][key]"
                                           class="form-control" value="{{$val['key']}}">
                                    <input class="form-control" name="distribution_rate[{{$key}}][value]" type="text"
                                           value="{{$val['value']}}">
                                    <span class="input-group-addon">%</span>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group m-b rate_div"><span class="input-group-addon">1级佣金比例</span>
                                <input type="hidden" name="distribution_rate[0][key]"
                                       class="form-control" value="1">
                                <input class="form-control" name="distribution_rate[0][value]" type="text" value="100">
                                <span class="input-group-addon">%</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">佣金提现门槛：</label>

                    <div class="col-sm-9">
                        <div class="input-group m-b">
                            <input class="form-control number_valid" type="text"
                                   value="{{settings('distribution_limit')?settings('distribution_limit'):100}}"
                                   name="distribution_limit">
                            <span class="input-group-addon">元</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">是否开启提现至微信钱包：</label>

                    <div class="col-sm-9">
                        <label class="control-label">
                            <input type="radio" value="1"
                                   name="distribution_commission_wechat"
                                    {{settings('distribution_commission_wechat')? 'checked': ''}}>
                            是
                            &nbsp;&nbsp;
                            <input type="radio" value="0"
                                   name="distribution_commission_wechat" {{!settings('distribution_commission_wechat')? 'checked': ''}}>
                            否
                        </label>
                    </div>
                </div>


                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存设置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script id="rate-template" type="text/x-template">
        <div class="input-group m-b rate_div"><span class="input-group-addon">{LVL}级佣金比例</span>
            <input type="hidden" name="distribution_rate[{NUM}][key]"
                   class="form-control" value="{LVL}">
            <input class="form-control" name="distribution_rate[{NUM}][value]" type="text">
            <span class="input-group-addon">%</span>
        </div>
    </script>

{{--@endsection--}}

{{--@section('after-scripts-end')--}}

    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}

    <script>
        $(function () {
            var rate_html = $('#rate-template').html();
            $('#distribution_level').change(function () {
                var num = $(this).children('option:selected').val();
                $('#rate_box').html('');
                for (var i = 0; i < num; i++) {
                    $('#rate_box').append(rate_html.replace(/{NUM}/g, i).replace(/{LVL}/g, i + 1));
                }
            });

            $('.number_valid').bind('input propertychange', function (e) {
                var value = $(e.target).val()
                if (!/^[-]?[0-9]*\.?[0-9]+(eE?[0-9]+)?$/.test(value)) {
                    value = value.replace(/[^\d.].*$/, '');
                    $(e.target).val(value);
                } else if (value.indexOf('-') != -1) {
                    $(e.target).val('');
                }
            });


            $('#setting_site_form').ajaxForm({
                success: function (result) {
                    if (result.status) {
                        swal({
                            title: "保存成功！",
                            text: "",
                            type: "success"
                        }, function () {
                            location.reload();
                        });
                    } else {
                        swal('保存失败', result.message, 'warning');
                    }


                }
            });
        })
    </script>
{{--@stop--}}