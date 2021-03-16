<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultiGrouponTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

        if (!Schema::hasTable($prefix . 'multi_groupon')) {

            Schema::create($prefix . 'multi_groupon', function (Blueprint $table) {

                $table->increments('id');
                $table->string('title');    //活动标题
                $table->integer('goods_id');//商品ID
                $table->integer('number');  //成团人数
                $table->decimal('price', 15, 2);  //团购价格
                $table->integer('rate')->default(0);    //佣金比例
                $table->integer('sort')->default(9);    //排序
                $table->tinyInteger('get_point')->default(0); //是否可获得积分：0 否；1是
                $table->tinyInteger('status')->default(1); //状态：0 无效；1有效
                $table->dateTime('starts_at');    //开始时间
                $table->dateTime('ends_at');    //结束时间
                $table->string('tags')->nullable(); //活动标签,预留
                $table->timestamps();

            });

         }

        if (!Schema::hasTable($prefix . 'multi_groupon_items')) {

            Schema::create($prefix . 'multi_groupon_items', function (Blueprint $table) {

                $table->increments('id');
                $table->integer('multi_groupon_id');
                $table->integer('multi_groupon_goods_id');
                $table->timestamp('starts_at')->nullable();  //开团时间
                $table->timestamp('ends_at')->nullable();  //结束时间
                $table->timestamp('complete_time')->nullable(); //成团时间
                $table->tinyInteger('status')->default(0);  //是否已成团:0 未成团，1 已成团,2 已失效（时间到期）
                $table->timestamps();

            });

        }

         if (!Schema::hasTable($prefix . 'multi_groupon_users')) {

            Schema::create($prefix . 'multi_groupon_users', function (Blueprint $table) {

                $table->increments('id');
                $table->integer('user_id');
                $table->integer('multi_groupon_id');
                $table->integer('multi_groupon_items_id')->nullable();
                $table->integer('order_id');
                $table->text('meta');  //用户数据，json格式
                $table->tinyInteger('is_leader')->default(0); //是否是团长
                $table->tinyInteger('status')->default(0); //参与状态，付款之后更改该状态
                $table->string('share_img')->nullable(); //分享海报
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
