<h4>设置连续签到</h4>

<div id="promotion-rule">
    <div class="row rule_list" data-key="0">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[0][value]" value="1" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[0][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[0][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
    <div class="row rule_list" data-key="1">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[1][value]" value="2" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[1][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[1][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
    <div class="row rule_list" data-key="2">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[2][value]" value="3" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[2][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[2][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
    <div class="row rule_list" data-key="3">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[3][value]" value="4" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[3][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[3][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
    <div class="row rule_list" data-key="4">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[4][value]" value="5" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[4][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[4][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
    <div class="row rule_list" data-key="5">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[5][value]" value="6" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[5][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[5][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
    <div class="row rule_list" data-key="6">
        <div class="form-group">
            <label class="col-sm-2 control-label">连续签到：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[6][value]" value="7" readonly>
                    <span class="input-group-addon">天</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="action[6][point]" value="1">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="action[6][coupon]">
                    <option value="0">未选择</option>
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <hr>
    </div>
</div>
{{--<button type="button" class="btn btn-primary" onclick="actionChange()">增加一条</button>--}}

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
