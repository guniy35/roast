<style>

    .advert_li {
        display: flex;
        min-height: 120px;
        background-color: #ffffff;
        margin-left: 10px;
        margin-top: 15px;
        border: 1px #FFDBDBDB dashed;
        position: relative;
    }

    .box_img{
        position: relative;
        height: 90px;
        width: 90px;
        background-color: #FFF3F3F3;
        margin-left: 15px;
        margin-top: 13px;
        border: 1px #FFDBDBDB dashed;
    }

    .box_input{
        display: flex;
        margin-left: 20px;
        /*height: 90px;*/
        /*min-width: 260px;*/
        margin-top: 13px;
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

    .webuploader-pick{
        margin-left: -8px;
        background: transparent;
        color: #FFDBDBDB;
    }
    .img-upload {
        position: relative;
        margin-left: -2px;
        margin-top: -5px;
    }

    .img-upload-init{
        margin-left: -18px;
        margin-top: -19px;
    }

    .img-upload-end{
        margin-left: -2px;
        margin-top: -5px;
    }

    .ibox-content{

        border-color:#FFFFFF;
    }

</style>

{!! Html::style(env("APP_URL").'/assets/backend/libs/pager/css/kkpager_orange.css') !!}

<div class="ibox float-e-margins">

    <a style="display: none" class="btn btn-primary margin-bottom" id="promote-goods-btn" data-toggle="modal"
       data-target="#goods_modal" data-backdrop="static" data-keyboard="false"
       data-url="">
        选择
    </a>


    <div class="ibox-content">
    <div class="ibox-content">

            <div class="panel-body">

                <div class="form-group">
                    <label class="col-sm-2 control-label text-right">*标题:</label>

                    <div class="col-sm-6">

                        <input type="text" class="form-control taginput" name="name" id="advert-name" placeholder=""
                               value="{{$advert->name}}"/>

                    </div>

                </div>

            </div>

            <div class="panel-body">

                <div class="form-group advert">
                    <label class="col-sm-2 control-label text-right">*秒杀商品:</label>

                    <div class="col-sm-8 col-lg-5 advert-box">
                        <ul id="bar" style="margin-left: -50px;">

                            @if($advertItems->count())

                                @foreach($advertItems as $key=> $item)

                                    @if($item->associate AND $item->associate->status)

                                        <li class="advert_li clearfix advert_li_{{$key+1}}" index="{{$key+1}}" data-suit_id="{{$item->associate->id}}" >
                                <div class="del">
                                    <i class="fa fa-remove"></i>
                                </div>
                                 <div class="box_img upload-{{$key+1}}" style="display: none">
                                    <div class="img-upload img-upload-init">
                                        <div class="box_img">
                                            <img width="88" height="88" src="{{$item->image}}" alt="">
                                            <div class="replace_img">
                                            <span>更换图片</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="upload">
                                    </div>
                                </div>

                                <div class="box_input_group">
                                    <div class="box_input">
                                        <label class="text-right">套餐名称:</label>
                                        <div>{{$item->associate->title}}
                                            @if($item->associate->status!=1)
                                                (活动已失效)
                                            @else

                                                @if($item->associate->ends_at<=$server_time)
                                                    (活动已过期)
                                                @endif

                                            @endif
                                        </div>
                                    </div>
                                    <div class="box_input">
                                        <label class="text-right">套餐总价:</label>
                                        <div>¥{{$item->associate->total}}</div>
                                    </div>
                                    <div class="box_input">
                                        <label class="text-right">商品数量:</label>
                                        <div>

                                            @if($item->associate->items->count()<=0)

                                                0

                                            @else

                                                {{ $item->associate->items->filter(function ($v){

                                                    return $v->status>0;

                                                })->count()}}

                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </li>

                                    @endif

                              @endforeach
                            @endif

                        </ul>


                        <ul>
                            <li class="advert_b_li">
                                <a class="fa fa-plus">
                                    <span onclick="addSuits()">  添加一个{{$header}}商品</span>
                                </a>
                            </li>
                            <p style="margin-left: -35px;">*拖动可更改图片位置</p>
                        </ul>

                    </div>

                </div>

            </div>


            <input type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="panel-body">

                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="" onclick="save('edit')">保存</button>
                    </div>
                </div>

            </div>



    </div>
</div>

<div id="goods_modal" class="modal inmodal fade"></div>

<script src="https://cdn.bootcss.com/Sortable/1.6.0/Sortable.min.js"></script>

@include('store-backend::micro_page.compoent.micro_page_componet_suit.script')








