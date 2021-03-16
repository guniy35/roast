<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no" />
    <meta name="format-detection" content="email=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title>首页</title>
    <style rel="stylesheet">
        @font-face {
            font-family: 'MILANTING--GBK1-Light';
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family:"MILANTING--GBK1-Light" !important;
        }

        body, html {
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;
            font-size: 16px;
        }

        #detailShare {
            width: 100%;
            background-color: #F3F5F7;
        }

        #detailShare .header {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: justify;
            -webkit-justify-content: space-between;
            -ms-flex-pack: justify;
            justify-content: space-between;

            padding: 10px 20px;
        }

        #detailShare .header .item {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
        }

        #detailShare .header .item .advtar {
            width: 25px;
            height: 25px;
            border-radius: 100%;
            overflow: hidden;
            margin-right: 10px;
        }

        #detailShare .header .item .advtar img {
            width: 100%;
        }

        #detailShare .header .item.item-right {
            font-size: 12px;
        }

        #detailShare .header .item.item-right img {
            padding-top: 5px;
            width: 15px;
            height: 15px;
            margin-right: 10px;
        }

        #detailShare .goods-img {
            padding: 0 20px;
        }

        #detailShare .goods-img img {
            width: 100%;
        }

        #detailShare .tips {
            padding: 15px 0;
            text-align: center;
            color: #FF2741;
            font-size: 12px;
        }

        #detailShare .code-box {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            padding: 10px 10px;
            background: #ffffff;
        }

        #detailShare .code-box .item {
            width: 100%;
        }

        #detailShare .code-box .item .goods-name {
            font-size: 15px;
            color: #2E2D2D;
            text-overflow: -o-ellipsis-lastline;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;

        }

        #detailShare .code-box .item .price-box {
            margin-top: 30px;
        }

        #detailShare .code-box .item .price-box .old-price {
            font-size: 10px;
            color: #9B9B9B;
            text-decoration: line-through;
        }

        #detailShare .code-box .item .price-box .new-price {
            font-size: 25px;
            color: #E73237;
        }

        #detailShare .code-box .item.right {
            text-align: center;
            font-size: 10px;
            color: #2E2D2D;
        }

        #detailShare .code-box .item.right img {
            width: 110px;
            height: 110px;
        }



    </style>
</head>
<body>
<div id="detailShare">
    <div class="header">
        <div class="item item-right">
            <div>
                <img src="https://cdn.ibrand.cc/Group%2031@2x.png" alt="">
            </div>
            <div class="text">
                {{ $icon_tip }}
            </div>
        </div>
    </div>
    <div class="goods-img">
        <img src="{{ $goods->img }}" alt="">
    </div>
    <div class="tips">
        {{ $tips }}
    </div>

    <div class="code-box">
        <div class="item left">
            <div class="goods-name">
                {{ $goods->name }}
            </div>
            <div class="price-box">
                <div class="old-price">
                    原价 ￥{{ $goods->market_price }}
                </div>
                <div class="new-price">
                    ￥{{ $price }}
                </div>
            </div>
        </div>
        <div class="item right">
            <div>
                <img src="{{ $mini_image }}" alt="">
            </div>
            <div>
                扫描或长按 识别二维码
            </div>
        </div>
    </div>
</div>
</body>
</html>