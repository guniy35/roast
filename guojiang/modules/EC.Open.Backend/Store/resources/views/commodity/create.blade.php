{!! Html::style(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/webuploader.css') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/Tagator/fm.tagator.jquery.css') !!}
{!! Html::script(env("APP_URL").'/vendor/libs/md5.js') !!}
{!! Html::style(env("APP_URL").'/assets/backend/libs/jquery.el/spec/base.css') !!}
<style type="text/css">
    .wrapper-content, .tab-pane {
        animation-fill-mode: none
    }

    .filelist {
        clear: both;
        width: 100%;
        float: left;
        padding-bottom: 20px;
    }

    .filelist li {
        list-style: none;
        text-align: center;
        margin-right: 12px;
        float: left;
        width: 103px;
        height: 103px
    }

    .filelist li img {
        width: 100px;
        height: 100px
    }

    .filelist .current {
        border: 3px #f60 solid;
    }

    #sku-builder .color-block {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 1px solid #cccccc;
        border-radius: 2px;
        margin-right: 3px;
    }

    div#containerpc, div#container {
        overflow: hidden
    }

    #sku-table input[type=file] {
        display: inline-block;
        margin-top: 10px;
    }

    #sku-table th {
        min-width: 90px
    }

    #sku-table th.sku-th {
        min-width: 170px
    }

    #sku-table input {
        width: 100%
    }

    .spec_img_th {
        display: block !important;
    }

    .spec_img_th_active {
        display: none;
    }

    .app-actions {
        position: fixed;
        bottom: 0;
        right: 0;
        left: 200px;
        width: auto;
        padding: 20px 0;
        background: #ffc;

        z-index: 999;
        text-align: center;
    }

</style>

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="tabs-container" id="wizard">
    <ul class="nav nav-tabs">
        <li class="active"><a data-id="1" class="app-action" aria-expanded="true">???????????????SPU???<i class="glyphicon"></i></a>
        </li>
        <li class=""><a data-id="2" class="app-action" aria-expanded="false">???????????????SKU???<i class="glyphicon"></i></a></li>
        <li class="editor_li"><a data-id="3" class="app-action" data-type="ue" aria-expanded="false">????????????(Mobile)</a>
        </li>
        <li class="editor_li"><a data-id="4" class="app-action" data-type="uepc" aria-expanded="false">????????????(PC)</a></li>
        <li class=""><a data-id="5" class="app-action" aria-expanded="false">?????????</a></li>
        <li class=""><a data-id="6" class="app-action" aria-expanded="false">SEO??????</a></li>
    </ul>

    {!! Form::open( [ 'url' => [route('admin.goods.store')], 'method' => 'POST', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}
    <div class="tab-content items">

        <div class="tab-pane active" id="tab_1">

            <div class="panel-body">
                <input type="hidden" name="id" value="0">
                <input type="hidden" name="img" value="">
                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="brand_id" name="brand_id">
                            <option value="">?????????</option>
                            @foreach($brands as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10" id="category-box">

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="store_nums" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="goods_no" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">????????????</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="market_price" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">????????????</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="sell_price" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <input name="is_del" type="radio" value="0" checked=checked/> ???
                        <input name="is_del" type="radio" value="2"/> ???
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <input name="is_commend" type="radio" value="1"/> ???
                        <input name="is_commend" type="radio" value="0" checked/> ???
                    </div>
                </div>

                <div class="form-group hide">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <input name="is_largess" type="radio" value="1"/> ???
                        <input name="is_largess" type="radio" value="0" checked/> ???
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="model_id" name="model_id" onchange="create_attr(this.value)">
                            <option value="">?????????????????????</option>
                            @foreach($models as $item)
                                <option value="{{$item['id']}}">{{$item['name']}}</option>
                            @endforeach
                        </select>
                        <label>????????????????????????????????????????????????????????????...????????????????????????????????????</label>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10" id="propert_table">

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">???????????????</label>
                    <div class="col-sm-10">
                        {!! Form::text('tags',  '' , ['class' => 'form-control form-inputTagator col-sm-10','id'=>'inputGoodsTags', 'placeholder' => '']) !!}
                        <label>??????????????????????????????????????????</label>
                    </div>
                </div>

            </div>

            <div class="app-actions">
                <a data-id="2" data-action="next" class="btn btn-success app-action">???????????</a>
                <input type="submit" class="btn btn-success app-action-save" data-toggle="form-submit"
                       data-target="#base-form"
                       value="??????">
            </div>

        </div><!-- /.tab-pane -->

        <div class="tab-pane" id="tab_2">
            <div class="panel-body">
                @include('store-backend::commodity.includes.spec')
            </div>
            <div class="app-actions">
                <a data-id="1" data-action="next" class="btn btn-success app-action-prev">???????????</a>
                <a data-id="3" data-action="next" class="btn btn-success app-action">???????????</a>
                <input type="submit" class="btn btn-success app-action-save" data-toggle="form-submit"
                       data-target="#base-form"
                       value="??????">
            </div>
        </div>

        <div class="tab-pane" id="tab_3">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-1 control-label">?????????????????????</label>
                    <div class="col-sm-10">
                        <input name="sync" type="radio" value="0" checked/> ?????????
                        <input name="sync" type="radio" value="1"/> ?????????PC???
                    </div>
                </div>
                <div id="upload_container" class="btn btn-primary" style="margin-bottom: 10px;">??????????????????</div>
                <script id="container" name="content"
                        type="text/plain"></script>
            </div>
            <div class="app-actions">
                <a data-id="2" data-action="next" class="btn btn-success app-action-prev">???????????</a>
                <a data-id="4" data-action="next" class="btn btn-success app-action">???????????</a>
                <input type="submit" class="btn btn-success app-action-save" data-toggle="form-submit"
                       data-target="#base-form"
                       value="??????">
            </div>
        </div><!-- /.tab-pane -->

        <div class="tab-pane" id="tab_4">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-1 control-label">?????????????????????</label>
                    <div class="col-sm-10">
                        <input name="sync" type="radio" value="2"/> ??????????????????
                    </div>
                </div>
                <div id="upload_containerpc" class="btn btn-primary" style="margin-bottom: 10px;">??????????????????</div>
                <script id="containerpc" name="contentpc"
                        type="text/plain"></script>
            </div>
            <div class="app-actions">
                <a data-id="3" data-action="next" class="btn btn-success app-action-prev">???????????</a>
                <a data-id="5" data-action="next" class="btn btn-success app-action">???????????</a>
                <input type="submit" class="btn btn-success app-action-save" data-toggle="form-submit"
                       data-target="#base-form"
                       value="??????">
            </div>
        </div><!-- /.tab-pane -->


        @include('store-backend::commodity.includes.album')

        <div class="tab-pane" id="tab_6">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">SEO????????????</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="keywords" placeholder="">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">SEO?????????</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="description" placeholder=""></textarea>

                    </div>
                </div>
            </div>
            <div class="app-actions">
                <a data-id="5" data-action="next" class="btn btn-success app-action-prev">???????????</a>
                <input type="submit" class="btn btn-success app-action-save" data-toggle="form-submit"
                       data-target="#base-form" value="??????">
            </div>
        </div><!-- /.tab-pane -->


    </div><!-- /.tab-content -->
    {!! Form::close() !!}
</div>


<div class="popup">
    <div class="sortContainer">
        <h2 class="sortTitle">??????????????????</h2>
        <p>??????????????????????????????????????????????????????????????????</p>
        <div class="sortMain">
            <ul id="sort">

            </ul>
        </div>

        <div class="btn-box">
            <button class="btn btn-cancle sortCancle pull-right">??????</button>
            <button class="btn btn-success sortOK pull-right">??????</button>
        </div>
    </div>
</div>

@include('UEditor::head')

{!! Html::script(env("APP_URL").'/vendor/libs/jquery.form.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/webuploader.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/Tagator/fm.tagator.jquery.js') !!}

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/common.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/spec/Sortable.js?v=201806041167') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/spec/spec.js?v=201806041167') !!}


<script>
	var postImgUrl = '{{route('upload.image',['_token'=>csrf_token()])}}';
	// ?????????Web Uploader
	$(document).ready(function () {

		var uploader2 = WebUploader.create({
			auto: true,
			swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
			server: '{{route('file.upload',['strategy'=>'video','_token'=>csrf_token()])}}',
			pick: '#videoPicker',
			fileVal: 'file',
			accept: {
				title: 'Video',
				extensions: 'mp4,wmv,avi,mpg,rmvb,m4v'
			}
		});

		// ????????????????????????item????????????class, ??????????????????????????????
		uploader2.on('uploadSuccess', function (file, response) {
			console.log(response);
			$('input[name="extra[video][url]"]').val(response.url);
			$('#videoOriginName').text(response.original_name);
		});

	});

	$(function () {
		$('#inputGoodsTags').tagator({
			autocomplete: ['????????????1', '????????????2', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth']
		});

		$('#filePicker').children().eq(0).css({'width': '90px', 'height': '30px'});

	});

</script>
@include('store-backend::commodity.script')