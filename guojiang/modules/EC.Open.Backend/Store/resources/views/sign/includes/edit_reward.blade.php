<h4>设置签到抽奖奖品</h4>
<div id="reward-box">
    <input type="hidden" name="deleteReward" id="deleteReward">
    @foreach($sign->rewards as $key=>$item)
        @if($item->type=='point')
            <div class="row reward_list" data-key="{{$key}}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">送积分：</label>
                    <div class="col-sm-4">
                        <div class="input-group m-b">
                            <input class="form-control" type="text" name="reward[{{$key}}][value]"
                                   value="{{$item->type_value}}">
                            <span class="input-group-addon">分</span>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <a class="col-sm-offset-6" href="javascript:;" data-id="{{$item->id}}" onclick="deleteReward(this)">删除</a>
                        <input type="hidden" name="reward[{{$key}}][type]" value="point">
                        <input type="hidden" name="reward[{{$key}}][id]" value="{{$item->id}}">
                    </div>
                </div>
                <hr>
            </div>
        @elseif($item->type=='coupon')
            <div class="row reward_list" data-key="{{$key}}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">送优惠券：</label>
                    <div class="col-sm-4">
                        <select class="form-control" name="reward[{{$key}}][value]">
                            @foreach($coupons as $coupon)
                                @if($coupon->id==$item->type_value)
                                    <option value="{{$coupon->id}}" selected>{{$coupon->title}}</option>
                                @else
                                    <option value="{{$coupon->id}}">{{$coupon->title}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="{{$item->label}}" name="reward[{{$key}}][label]"
                               placeholder="提示语">
                        <input type="hidden" name="reward[{{$key}}][type]" value="coupon">
                    </div>
                    <div class="col-sm-2">
                        <a class="col-sm-offset-6" href="javascript:;" data-id="{{$item->id}}" onclick="deleteReward(this)">删除</a>
                        <input type="hidden" name="reward[{{$key}}][id]" value="{{$item->id}}">
                    </div>
                </div>
                <hr>
            </div>
        @else
            <div class="row reward_list" data-key="{{$key}}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">谢谢参与：</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" value="谢谢参与" name="reward[{{$key}}][label]">
                        <input type="hidden" name="reward[{{$key}}][type]" value="luck">
                    </div>
                    <div class="col-sm-2">
                        <a class="col-sm-offset-6" href="javascript:;" data-id="{{$item->id}}" onclick="deleteReward(this)">删除</a>
                        <input type="hidden" name="reward[{{$key}}][id]" value="{{$item->id}}">
                    </div>
                </div>
                <hr>
            </div>
        @endif
    @endforeach
</div>
<button type="button" class="btn btn-primary" onclick="rewardPointAdd()">送积分</button>
<button type="button" class="btn btn-primary" onclick="rewardCouponAdd()">送优惠券</button>
<button type="button" class="btn btn-primary" onclick="rewardNoneAdd()">未中奖</button>
<hr>

<script type="text/x-template" id="sign_reward_point_template">
    <div class="row reward_list" data-key="{VALUE}">
        <div class="form-group">
            <label class="col-sm-2 control-label">送积分：</label>
            <div class="col-sm-4">
                <div class="input-group m-b">
                    <input class="form-control" type="text" name="reward[{VALUE}][value]" value="">
                    <span class="input-group-addon">分</span>
                </div>
            </div>
            <div class="col-sm-2">
                <a class="col-sm-offset-6" href="javascript:;" onclick="deleteReward(this)">删除</a>
                <input type="hidden" name="reward[{VALUE}][type]" value="point">
            </div>
        </div>
        <hr>
    </div>
</script>


<script type="text/x-template" id="sign_reward_coupon_template">
    <div class="row reward_list" data-key="{VALUE}">
        <div class="form-group">
            <label class="col-sm-2 control-label">送优惠券：</label>
            <div class="col-sm-4">
                <select class="form-control" name="reward[{VALUE}][value]">
                    @foreach($coupons as $item)
                        <option value="{{$item->id}}">{{$item->title}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="reward[{VALUE}][label]" placeholder="提示语">
                <input type="hidden" name="reward[{VALUE}][type]" value="coupon">
            </div>
            <div class="col-sm-2">
                <a class="col-sm-offset-6" href="javascript:;" onclick="deleteReward(this)">删除</a>
            </div>
        </div>
        <hr>
    </div>

</script>

<script type="text/x-template" id="sign_reward_none_template">
    <div class="row reward_list" data-key="{VALUE}">
        <div class="form-group">
            <label class="col-sm-2 control-label">谢谢参与：</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" value="谢谢参与" name="reward[{VALUE}][label]">
                <input type="hidden" name="reward[{VALUE}][type]" value="luck">

            </div>
            <div class="col-sm-2">
                <a class="col-sm-offset-6" href="javascript:;" onclick="deleteReward(this)">删除</a>
            </div>
        </div>
        <hr>
    </div>
</script>