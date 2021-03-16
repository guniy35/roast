<h4>设置连续签到</h4>

<div id="promotion-rule">

    @foreach($sign->rules as $key=>$rule)
        <div class="row rule_list" data-key="{{$key}}">
            <div class="form-group">
                <label class="col-sm-2 control-label">连续签到：</label>
                <div class="col-sm-4">
                    <div class="input-group m-b">
                        <input class="form-control" type="text" name="action[{{$key}}][value]" value="{{$rule}}" readonly>
                        <span class="input-group-addon">天</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">送积分：</label>
                <div class="col-sm-4">
                    <div class="input-group m-b">
                        <input class="form-control" type="text" name="action[{{$key}}][point]"
                               value="{{$action[$key]['point']}}">
                        <span class="input-group-addon">分</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">送优惠券：</label>
                <div class="col-sm-4">
                    <select class="form-control" name="action[{{$key}}][coupon]">
                        <option value="0">未选择</option>
                        @foreach($coupons as $item)
                            @if($item->id==$action[$key]['coupon'])
                                <option value="{{$item->id}}" selected>{{$item->title}}</option>
                            @else
                                <option value="{{$item->id}}">{{$item->title}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            {{--<a class="col-sm-offset-6" href="javascript:;" onclick="deleteRule(this)">删除</a>--}}
            <hr>
        </div>
    @endforeach
</div>
<button type="button" class="btn btn-primary" onclick="actionChange()">增加一条</button>

<hr>

<script type="text/x-template" id="sign_rule_template">
    <div class="row rule_list" data-key="{VALUE}">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[{VALUE}][value]" value="">
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[{VALUE}][point]" value="0">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[{VALUE}][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <a class="col-sm-offset-6" href="javascript:;" onclick="deleteRule(this)">删除</a>
        <hr>
    </div>

</script>
