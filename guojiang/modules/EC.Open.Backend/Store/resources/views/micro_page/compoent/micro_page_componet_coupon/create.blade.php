<style>

    .advert_li {
        display: flex;
        min-height: 120px;
        background-color: #ffffff;
        margin-left: 10px;
        margin-top: 15px;
        position: relative;
        background:linear-gradient(141deg,rgba(246,121,126,1) 0%,rgba(234,68,72,1) 100%);
        border-radius:6px;
    }

    .box_input label{
        width:100px;
        text-align: center;
    }

    .advert_b_li {
        min-height: 120px;
        background-color: #ffffff;
        margin-left: -40px;
        margin-top: 20px;
        margin-bottom: 15px;
        border: 1px #FFDBDBDB dashed;
        text-align: center;
        line-height: 120px;
    }
    .advert-box {
        background-color: #FFF3F3F3;
        min-height: 120px;
        margin-left: 12px;
        border: 1px #FFDBDBDB solid;
    }
    .add-img{
        font-size: 25px;
        line-height: 90px;
        margin-left: 30px;
        verflow: hidden;

    }
    .replace_img{
        position: absolute;
        bottom: 0;
        width:100%;
        background-color: black;
        background:rgba(46,45,45,.8);
        color: #FFFFFF;
        text-align: center;
    }
    .del{
        width: 30px;
        height: 30px;
        border-radius: 100%;
        background:rgba(46,45,45,.5);
        position: absolute;
        right:-8px;
        top:-8px;
        cursor: pointer;
    }
    .del i{
        color: #ffffff;
        line-height: 30px;
        margin-left: 10px;
        z-index: -100;
    }

    .upload label{
        width: 100px;
        height: 100px;
    }

    .ibox-content{

        border-color:#FFFFFF;
    }
    .coupon{
        display: flex;
        flex: 1;
        padding: 20px;
        color: #fff;
        align-items: center;
    }
    .left-item{
        flex: 1;
    }

    .money-value{
        font-size: 45px;
    }

    .coupon .title{
        font-size: 18px;
    }

    .coupon .time{
        font-size: 12px;
    }


</style>

{!! Html::style(env("APP_URL").'/assets/backend/libs/pager/css/kkpager_orange.css') !!}

<div class="ibox float-e-margins">

    <a style="display: none"  class="btn btn-primary margin-bottom" id="promote-goods-btn" data-toggle="modal"
       data-target="#goods_modal" data-backdrop="static" data-keyboard="false"
       data-url="">
        ??????
    </a>


    <div class="ibox-content">
        <div class="ibox-content">

            <div class="panel-body">

                <div class="form-group">
                    <label class="col-sm-2 control-label text-right">*??????:</label>

                    <div class="col-sm-6">

                        <input type="text" class="form-control taginput" name="name" id="advert-name" placeholder=""
                               value=""/>

                    </div>

                </div>

            </div>

            <div class="panel-body">

                <div class="form-group advert">
                    <label class="col-sm-2 control-label text-right">*?????????:</label>

                    <div class="col-sm-8 col-lg-4 advert-box">
                        <ul id="bar" style="margin-left: -50px;">

                            {{--<li class="advert_li clearfix" draggable="false" style="">--}}
                                {{--<div class="coupon">--}}
                                    {{--<div class="del"><i class="fa fa-remove"></i></div>--}}
                                    {{--<div class="left-item">--}}

                                        {{--<div class="money-1">--}}
                                            {{--<span style="font-size: 20px;">??</span>--}}
                                            {{--<span class="money-value">99</span>--}}
                                        {{--</div>--}}
                                        {{--<div class="money-2" style="display: none">--}}
                                            {{--<span class="money-value">9</span>--}}
                                            {{--<span style="font-size: 20px;">???</span>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    {{--<div class="right-item">--}}
                                        {{--<div class="title">--}}
                                                {{--???????????????????????????--}}
                                        {{--</div>--}}
                                        {{--<br>--}}
                                        {{--<div class="time pull-right">2008.05.05-2019.05.05</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</li>--}}

                        </ul>


                        <ul>
                            <li class="advert_b_li">
                                <a class="fa fa-plus">
                                    <span onclick="addCoupon()">  ????????????{{$header}}</span>
                                </a>
                            </li>
                            <p style="margin-left: -35px;">*???????????????????????????</p>
                            <p style="margin-left: -35px;"></p>
                        </ul>

                    </div>

                </div>

            </div>


            <input type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="panel-body">

                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="" onclick="save('create')">??????</button>
                    </div>
                </div>

            </div>



        </div>
    </div>

    <div id="goods_modal" class="modal inmodal fade"></div>

    <div id='box' style="display: none"></div>

    <script src="https://cdn.bootcss.com/Sortable/1.6.0/Sortable.min.js"></script>

@include('store-backend::micro_page.compoent.micro_page_componet_coupon.script')








