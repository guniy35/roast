<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="email=no"/>
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

        #collageShare {
            width: 100%;
            /*height: 682px;*/

        }
        #collageShare .top {
            color: #ffffff;
            background: linear-gradient(291deg, #f77062 0%, #fe5196 100%);
        }
        #collageShare .top .tips {
            padding: 15px 0;
            text-align: center;
            font-size: 12px;
        }
        #collageShare .header {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-justify-content: space-between;
            justify-content: space-between;
            padding: 10px 20px;
        }
        #collageShare .header .item {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
        }
        #collageShare .header .item .advtar {
            width: 25px;
            height: 25px;
            border-radius: 100%;
            overflow: hidden;
            margin-right: 10px;
        }
        #collageShare .header .item .advtar img {
            width: 100%;
        }
        #collageShare .header .item.item-right {
            font-size: 12px;
        }
        #collageShare .header .item.item-right img {
            padding-top: 5px;
            width: 15px;
            height: 15px;
            margin-right: 10px;
        }
        #collageShare .goods {
            position: relative;
            padding: 0 20px;
        }
        #collageShare .goods img {
            display: block;
            width: 100%;
        }
        #collageShare .goods .num {
            padding: 5px;
            font-size: 12px;
            position: absolute;
            top: 0;
            left: 20px;
            background: #ff2741;
            border-radius: 0px 6px 6px 0px;
        }
        #collageShare .goods .text {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 16px;
            padding: 5px 15px;
            background: rgba(255, 255, 255, 0.7);
        }
        #collageShare .button-box {
            background: #ffffff;
            padding: 10px;
            text-align: center;
        }
        #collageShare .button-box .img-box {
            font-size: 12px;
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-justify-content: center;
            justify-content: center;
        }
        #collageShare .button-box .img-box .img {
            border-radius: 100%;
            width: 25px;
            height: 25px;
            overflow: hidden;
            background: #FBF6DC;
            margin-right: 5px;
        }
        #collageShare .button-box .img-box .img img {
            width: 100%;
            height: 100%;
        }
        #collageShare .button-box .code {
            font-size: 12px;
            color: #9B9B9B;
        }
        #collageShare .button-box .code img {
            width: 100px;
            height: 100px;
            margin: 10px 0;
        }
        #collageShare .group {
            text-align: center;
            padding: 10px 0;
            background: #ffffff;
        }
        #collageShare .group .code-box {
            display: block;
        }
        #collageShare .group .code-box .item {
            display: inline-block;
            width: 100%;
        }
        #collageShare .group .code-box .item.img img {
            width: 50px;
            height: 50px;
        }
        #collageShare .group .code-box .item.text {
            margin-left: 10px;
        }
        #collageShare .group .code-box .item.text .user {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -webkit-align-items: center;
            -ms-flex-align: center;
            align-items: center;
            margin-bottom: 10px;
        }
        #collageShare .group .code-box .item.text img {
            width: 25px;
            height: 25px;
            border-radius: 100%;
            margin-right: 5px;
        }
        #collageShare .code-box {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            padding: 20px 10px;
            background: #ffffff;
        }
        #collageShare .code-box .item {
            width: 100%;
        }
        #collageShare .code-box .item .goods-name {
            font-size: 15px;
            color: #2E2D2D;
        }
        #collageShare .code-box .item .price-box {
            margin-top: 30px;
        }
        #collageShare .code-box .item .price-box .old-price {
            font-size: 10px;
            color: #9B9B9B;
            text-decoration: line-through;
        }
        #collageShare .code-box .item .price-box .new-price {
            font-size: 25px;
            color: #E73237;
        }
        #collageShare .code-box .item.right {
            text-align: center;
            font-size: 10px;
            color: #2E2D2D;
        }
        #collageShare .code-box .item.right img {
            width: 110px;
            height: 110px;
        }
        #collageShare .Protection {
            border-top: 1px solid #D8D8D8;
        }
        #collageShare .Protection img {
            width: 100%;
        }
    </style>
</head>
<body>
<div id="collageShare">

    <div class="top">
        <div class="header">
            <div class="item item-left">
                <div class="advtar">
                    <img src="{{ $circularImg }}" alt="">
                </div>
                <div class="nick-name">{{ $user->nick_name }}</div>
            </div>
            <div class="item item-right">
                <div>
                    <img src="https://cdn.ibrand.cc/Group%2031@2x.png" alt="">
                </div>
                <div class="text">
                    力荐的时尚全能好货
                </div>
            </div>
        </div>
        <div class="goods">
            <img src="{{ $multiGroupon->goods->img }}" alt="">
            <div class="num">
                {{ $multiGroupon->number }}人拼团价
            </div>
        </div>
        <div class="tips">
            为您挑选全球好货
        </div>
    </div>
    <div class="code-box">
        <div class="item left">
            <div class="goods-name">
                {{ $multiGroupon->goods->name }}
            </div>
            <div class="price-box">
                <div class="old-price">
                    原价 ￥{{ $market_price }}
                </div>
                <div class="new-price">
                    ￥{{ $price }}
                </div>
            </div>
        </div>
        <div class="item right">
            <div>
                <img src="{{ $mini_qrcode }}" alt="">
            </div>
            <div>
                扫描或长按 识别二维码
            </div>
        </div>
    </div>

    <div class="Protection">
        <img src="https://cdn.ibrand.cc/100&gerb.png" alt="">
    </div>

</div>
</body>
</html>