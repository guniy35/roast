<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReduceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

        if (!Schema::hasTable($prefix . 'reduce')) {

            Schema::create($prefix . 'reduce', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');    //活动标题
                $table->integer('goods_id');//商品ID
                $table->integer('number');  //帮砍人数
                $table->integer('reduce_store_nums');//砍价活动出始库存
                $table->integer('store_nums');//砍价活动时时库存
                $table->integer('hour')->default(24);//砍价有效期(小时)
                $table->decimal('price', 15, 2);  //帮砍最低价
                $table->integer('sort')->default(9);    //排序
                $table->tinyInteger('status')->default(1); //状态：0 无效；1有效
                $table->dateTime('starts_at');    //开始时间
                $table->dateTime('ends_at');    //结束时间
                $table->tinyInteger('get_point')->default(0); //是否可获得积分：0 否；1是
                $table->string('tags')->nullable(); //活动标签,预留
                $table->timestamps();

            });

         }

        if (!Schema::hasTable($prefix . 'reduce_items')) {

            Schema::create($prefix . 'reduce_items', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('reduce_id');
                $table->integer('user_id');
                $table->integer('reduce_goods_id');
                $table->integer('order_id')->nullable();
                $table->text('reduce_amount_arr');//砍价金额数组
                $table->timestamp('starts_at')->nullable();  //开始时间
                $table->timestamp('ends_at')->nullable();  //结束时间
                $table->timestamp('order_time')->nullable(); //下单时间(结算时间）
                $table->timestamp('complete_time')->nullable(); //完成时间(支付时间)
                $table->string('share_img')->nullable(); //分享海报
                $table->tinyInteger('status')->default(0);
                $table->timestamps();

            });

        }



        if (!Schema::hasTable($prefix . 'reduce_users')) {

            Schema::create($prefix . 'reduce_users', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->integer('reduce_id');
                $table->integer('reduce_items_id');
                $table->decimal('reduce_amount', 15, 2);  //已砍的价格
                $table->text('meta');  //用户数据，json格式
                $table->tinyInteger('is_leader')->default(0); //是否是队长
                $table->tinyInteger('status')->default(0); //参与状态，付款之后更改该状态
                $table->timestamps();

            });
        }




        if (!Schema::hasTable($prefix . 'promotion_goods_relation')) {

            Schema::create($prefix . 'promotion_goods_relation', function (Blueprint $table) {

                $table->increments('id');
                $table->integer('goods_id');
                $table->string('origin_type'); //活动类型，如groupon,seckill等
                $table->text('origin_id');  //活动对应的ID
                $table->timestamps();

            });

         }


       if (!Schema::hasTable($prefix . 'merchant_pay')) {

             Schema::create($prefix .'merchant_pay', function (Blueprint $table) {
                        $table->increments('id');
                        $table->string('origin_type');  //打款类型：REFUND 退款;COMMISSION 分销佣金
                        $table->integer('origin_id');
                        $table->string('channel')->default('wechat'); //打款渠道：wechat 微信； alipay 支付宝
                        $table->integer('channel_id')->default(0); //如果是REFUND，记录el_refund_amount 的ID
                        $table->string('partner_trade_no'); //打款编号
                        $table->string('payment_no')->nullable(); //交易流水号
                        $table->integer('amount');  //金额
                        $table->string('status');  //打款状态:SUCCESS FAIL
                        $table->string('error_code')->nullable();   //失败状态码：NAME_MISMATCH
                        $table->string('err_code_des')->nullable(); //失败描述：真实姓名不一致
                        $table->dateTime('payment_time')->nullable();   //成功打款时间
                        $table->integer('user_id'); //用户ID
                        $table->integer('admin_id');    //操作人ID
                        $table->timestamps();
             });

        }

        if (!Schema::hasTable($prefix . 'order_special_type')) {

                     Schema::create($prefix .'order_special_type', function (Blueprint $table) {
                       $table->increments('id');
                       $table->integer('order_id')->unsigned()->nullable();
                       $table->string('origin_type')->nullable();   //类型
                       $table->integer('origin_id')->default(0);
                       $table->timestamps();
                       $table->softDeletes();
                     });

         }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $prefix = config('ibrand.app.database.prefix', 'ibrand_');
        Schema::dropIfExists($prefix . 'multi_groupon');
        Schema::dropIfExists($prefix . 'multi_groupon_items');
        Schema::dropIfExists($prefix . 'multi_groupon_users');

    }
}
