<script>
    /**
     * 连续签到设置
     */
    function actionChange() {
        var lng = $('.rule_list').length;
        if (lng > 6) {
            swal('提示', '最多设置7天连续签到', 'warning');
            return;
        }

        var num = 0;
        if (lng > 0) {
            num = $('.rule_list').last().data('key') + 1;
        }


        var action_html = $('#sign_rule_template').html();
        $('#promotion-rule').append(action_html.replace(/{VALUE}/g, num));
    }

    /**
     * 删除签到设置
     * @param _self
     */
    function deleteRule(_self) {
        var that = $(_self);
        that.parent('.rule_list').remove();
    }

    /**
     * 删除奖品设置
     * @param _self
     */
    function deleteReward(_self) {
        var that = $(_self);
        var deleteInput = $('#deleteReward').val();
        if (!deleteInput) {
            var ids = that.data('id');
        } else {
            ids = deleteInput + ',' + that.data('id');
        }
        $('#deleteReward').val(ids);
        that.parent().parent().parent('.reward_list').remove();
    }

    /**
     * 积分奖品
     */
    function rewardPointAdd() {
        var lng = $('.reward_list').length;
        var num = 0;
        if (lng > 0) {
            num = $('.reward_list').last().data('key') + 1;
        }

        var action_html = $('#sign_reward_point_template').html();
        $('#reward-box').append(action_html.replace(/{VALUE}/g, num));
    }

    /**
     * 优惠券奖品
     */
    function rewardCouponAdd() {
        var lng = $('.reward_list').length;
        var num = 0;
        if (lng > 0) {
            num = $('.reward_list').last().data('key') + 1;
        }
        var action_html = $('#sign_reward_coupon_template').html();
        $('#reward-box').append(action_html.replace(/{VALUE}/g, num));
    }

    /**
     * 未中奖
     */
    function rewardNoneAdd() {
        var lng = $('.reward_list').length;
        var num = 0;
        if (lng > 0) {
            num = $('.reward_list').last().data('key') + 1;
        }
        var action_html = $('#sign_reward_none_template').html();
        $('#reward-box').append(action_html.replace(/{VALUE}/g, num));
    }

</script>