<style>

    .select2-container{
        z-index: 2055 !important;
    }
    .advert_li {
        display: flex;
        min-height: 150px;
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

    .box_input_name{
        display: flex;
        margin-left: 20px;
        /*height: 90px;*/
        /*min-width: 260px;*/
        margin-top: 13px;
    }

    .box_input_name label{
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
        ??????
    </a>

    <a style="display: none"  class="btn btn-primary margin-bottom" id="img-btn" data-toggle="modal"
       data-target="#img_modal" data-backdrop="static" data-keyboard="false"
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
                               value="{{$advert->name}}"/>

                    </div>

                </div>

            </div>

        <div class="panel-body">

            <div class="form-group">
                <label class="col-sm-2 control-label text-right">??????????????????:</label>

                <div class="col-sm-6">

                    <input type="text" class="form-control taginput" name="advert-title" id="advert-title" placeholder=""
                           value="{{$advert->title}}"/>

                </div>

            </div>

        </div>

        <div class="panel-body">

            <div class="form-group">
                <label class="col-sm-2 control-label text-right">????????????????????????:</label>

                <div class="col-sm-6" >

                    ???<input type="radio" name="is_show_title" id="" value="1"

                            @if($advert->is_show_title==1) checked @endif
                    >

                    ???<input type="radio" name="is_show_title" id="" value="0"

                            @if($advert->is_show_title==0) checked @endif
                    >

                </div>

            </div>

        </div>


        <div class="panel-body">

            <div class="form-group">
                <label class="col-sm-2 control-label text-right">*??????</label>

                <div class="col-sm-6" >
                    <select id="model"  class="form-control" name="model">

                        <option value ="micro_page_componet_goods_group"

                                @if($advert->type=='micro_page_componet_goods_group') selected @endif

                        >??????</option>
                        <option value ="micro_page_componet_goods_group_change"

                                     @if($advert->type=='micro_page_componet_goods_group_change') selected @endif
                             ">?????????</option>
                    </select>
                </div>

            </div>

        </div>

            <div class="panel-body">

                <div class="form-group advert">
                    <label class="col-sm-2 control-label text-right">*????????????:</label>

                    <div class="col-sm-8 col-lg-5 advert-box">
                        <ul id="bar" style="margin-left: -50px;">

                            @if($advertItems->count())

                                @foreach($advertItems as $key=> $item)
                                <li class="advert_li

                                @if($item->type=='goods_group_img')

                                        advert_li_img
                                @endif

                                clearfix advert_li_{{$key+1}}" index="{{$key+1}}" >
                                <div class="del">
                                    <i class="fa fa-remove"></i>
                                </div>
                                 <div class="box_img upload-{{$key+1}}" style="display: none">
                                    <div class="img-upload img-upload-init">
                                        <div class="box_img">
                                            <img width="88" height="88" src="{{$item->image}}" alt="">
                                            <div class="replace_img">
                                            <span>????????????</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="upload">
                                    </div>
                                </div>

                                <div class="box_input_group">
                                    <div class="box_input">
                                        <label class="text-right">????????????:</label>
                                        <div>{{$item->name}}</div>
                                    </div>

                                    @if($item->type=='micro_page_componet_goods_group')
                                        <div class="box_input">
                                            <label class="text-right">????????????:</label>
                                            <div>{{$item->children->count()}}</div>
                                        </div>
                                        <div class="box_input">
                                            <label class="text-right"></label>
                                            <div style="position: absolute;left: 58px;">
                                                <a class='edit' style='cursor: pointer' href='javascript:;'
                                                   data-type='micro_page_componet_goods_group'
                                                   data-ids='{{$item->meta['goods_ids']}}' data-name='{{$item->name}}'
                                                   data-subtitle='{{isset($item->meta['subtitle'])?$item->meta['subtitle']:''}}'
                                                   data-index='{{$key+1}}' >??????</a>
                                                <input class='form-control' name='goods_ids'  type='hidden' value='{{$item->meta['goods_ids']}}'>
                                                <input class='form-control' name='group_subtitle'  type='hidden' value='{{isset($item->meta['subtitle'])?$item->meta['subtitle']:''}}'>
                                            </div>
                                        </div>
                                   @endif


                                    @if($item->type=='micro_page_componet_goods_group_img')
                                        <div class="box_input">
                                            <label class="text-right">????????????:</label>
                                            <div>{{$item->children->count()}}</div>
                                        </div>
                                        <div class="box_input">
                                            <label class="text-right"></label>
                                            <div style="position: absolute;left: 58px;">
                                                <a class='edit edit-img' style='cursor: pointer' href='javascript:;'
                                                   data-type='micro_page_componet_goods_group_img'
                                                   data-name='{{$item->name}}'
                                                   data-len='{{$item->meta['count']}}'
                                                   data-subtitle='{{isset($item->meta['subtitle'])?$item->meta['subtitle']:''}}'
                                                   data-input='{{$item->meta['input']}}'
                                                   data-index='{{$key+1}}' >??????</a>
                                                <input class='form-control' name='input' type='hidden' value='{{$item->meta['input']}}'>
                                                <input class='form-control' name='group_subtitle'  type='hidden' value='{{isset($item->meta['subtitle'])?$item->meta['subtitle']:''}}'>
                                            </div>
                                        </div>
                                    @endif

                                </div>

                            </li>
                            @endforeach



                            @endif

                        </ul>


                        <ul>
                            <li class="advert_b_li">
                                <a class="fa fa-plus">
                                    <span onclick="addGoodsGroup()">  ????????????{{$header}}</span>
                                </a>
                            </li>
                            <li class="advert_b_li">
                                <a class="fa fa-plus">
                                    <span onclick="addGoodsGroupImage()">  ???????????????????????????</span>
                                </a>
                            </li>
                            <p style="margin-left: -35px;">*???????????????????????????</p>
                        </ul>

                    </div>

                </div>

            </div>


            <input type="hidden" name="_token" value="{{csrf_token()}}">

            <div class="panel-body">

                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button id="save" class="btn btn-primary" is_show_title="{{$advert->is_show_title}}" type="" onclick="save('edit')">??????</button>
                    </div>
                </div>

            </div>



    </div>
</div>

<div id="goods_modal"  style="z-index: 2055 !important;" class="modal inmodal fade"></div>

    <div id="img_modal" class="modal inmodal fade"></div>

<script src="https://cdn.bootcss.com/Sortable/1.6.0/Sortable.min.js"></script>

@include('store-backend::micro_page.compoent.micro_page_componet_goods_group.script')

@include('store-backend::micro_page.compoent.micro_page_componet_goods_group.script_image')

    <script>
        $('input[name=is_show_title]').on('ifChecked', function(event){
            var is_show_title=$(this).val();
            if(is_show_title==0){
                $('#save').attr('is_show_title',0)
            }else{
                $('#save').attr('is_show_title',1)
            }
        });
    </script>







