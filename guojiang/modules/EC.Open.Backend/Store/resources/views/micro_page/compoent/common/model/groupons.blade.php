@extends('store-backend::micro_page.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    选择拼团商品
    {{--{{request('index')}},{{request('page_id')}}--}}
@stop

@section('after-styles-end')
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop

@section('body')

    <br>
    <br>
    <div class="row">


        <form class="form-horizontal" action="{{route('admin.setting.micro.page.compoent.getGrouponsData')}}"
              method="get" id="search_spu_from">

            <div class="col-md-12">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="col-sm-3 control-label"> 商品名称:</label>
                    <div class="col-sm-7">
                        <input type="text" name="title" value="" class="form-control" placeholder="">
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" id="send" class="ladda-button btn btn-primary" data-style="slide-right"
                                data-toggle="form-submit" data-target="#search_spu_from">搜索
                        </button>
                    </div>
                </div>

                @if($groupons_)
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-6">
                            <a target="_blank" href=""></a>
                        </div>
                    </div>
                @else
                    @if(request('groupon_id'))
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <span class="label label-default">groupon_id：{{request('groupon_id')}};不存在</span>
                            </div>
                        </div>
                    @endif

                @endif
            </div>

        </form>

        <div class="clearfix"></div>
        <div class="hr-line-dashed "></div>

        <div class="panel-body">
            <div class="col-sm-2">

            </div>
            <div class="table-responsive col-sm-9" id="pagesList" data-groupon_id="{{request('groupon_id')}}">

            </div>

            <div class="col-sm-1">

            </div><!-- /.box-body -->
            <div id="kkpager" style="margin-left: 160px;"></div>
        </div>
    </div>
@stop




{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/spin.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.jquery.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/alpaca-spa-2.1.js') !!}


@section('footer')
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.form.min.js') !!}

    @include('store-backend::micro_page.compoent.common.kkpager')

    <button type="button" class="btn btn-link" data-dismiss="modal" onclick="cancel();">取消</button>
    <button type="button" onclick="sendIds();" class="ladda-button btn btn-primary"> 确定
        <script>

            var index = "{{request('index')}}"

            function cancel() {
                var groupon_id = $('#app').attr('data-groupon_id');
                if (!groupon_id) {
                    $('#goods_modal').data('index');
                }
            }

            function sendIds() {
                var groupon_id = $('#app').attr('data-groupon_id');
                console.log(groupon_id);
                if (!groupon_id) {
                    swal({
                        title: '保存失败',
                        text: '请选择关联的拼团商品',
                        type: "error"
                    });
                    return false;
                }
                console.log(groupon_id)

                var data = {
                    price: $('#groupon_id_' + groupon_id).data('price'),
                    title: $('#groupon_id_' + groupon_id).data('title'),
                    img: $('#groupon_id_' + groupon_id).data('img'),
                    number: $('#groupon_id_' + groupon_id).data('number')
                };

                compoent_componet_html = compoent_html.replace('{#price#}', data.price);
                compoent_componet_html = compoent_componet_html.replace('{#title#}', data.title);
                compoent_componet_html = compoent_componet_html.replace('{#img#}', data.img);
                compoent_componet_html = compoent_componet_html.replace('{#number#}', data.number);

                $('#bar').append(compoent_componet_html);

                $('#goods_modal').modal('hide');

                var length = $('.advert_li').length;

                var obj = $('.advert_li').eq(length - 1);

                obj.attr('index', length);

                obj.attr('data-groupon_id', groupon_id);

                obj.addClass('advert_li_' + length);

                obj.find('.box_img').addClass('upload-' + length)

                uploadImg('.upload-' + length, length, 'edit');


            }

            function getParameter(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]);
                return null;
            }

            $(document).ready(function () {
                var data = {
                    ids: $('#selected_spu').val(),
                    _token: _token
                };
                $.get('{{route('admin.setting.micro.page.compoent.getGrouponsData',['groupon_id'=>request('groupon_id')])}}', data, function (ret) {

                    console.log(ret);
                    $('#pagesList').html(ret);
                });

                //搜索
                $('#search_spu_from').ajaxForm({
                    success: function (result) {
                        $('#pagesList').html(result);
                        var totalPage = $('#app').attr('data-totalPage');
                        var pageNo = getParameter('page');
                        if (!pageNo) {
                            pageNo = 1;
                        }
                        kkpager.generPageHtml({
                            pno: pageNo,
                            //总页码
                            total: totalPage,
                            //总数据条数1
                            totalRecords: 1,
                            mode: 'click',
                            //点击页码的函数，这里发送ajax请求后台
                            click: function (n) {
                                var title = $('input[name=title]').val();
                                var data = {
                                    _token: "{{csrf_token()}}",
                                };
                                if (title) {
                                    data.title = title;
                                }
                                $.get("{{route('admin.setting.micro.page.compoent.getGrouponsData')}}?page=" + n, data, function (data) {
                                    $("#pagesList").html("");
                                    $("#pagesList").html(data);
                                    kkpager.selectPage(n, totalPage);
                                });

                            }
                        });

                    }
                });

                //分页
                var totalPage = "{{$groupons->lastPage()}}";
                var totalRecords = "1";
                var pageNo = getParameter('page');
                if (!pageNo) {
                    pageNo = 1;
                }

                kkpager.generPageHtml({
                    pno: pageNo,
                    //总页码
                    total: totalPage,
                    //总数据条数
                    totalRecords: totalRecords,
                    mode: 'click',
                    //点击页码的函数，这里发送ajax请求后台
                    click: function (n) {
                        var title = $('input[name=title]').val();
                        var data = {
                            _token: "{{csrf_token()}}",
                        };
                        if (title) {
                            data.title = title;
                        }
                        $.get("{{route('admin.setting.micro.page.compoent.getGrouponsData')}}?page=" + n, data, function (data) {
                            $("#pagesList").html("");
                            $("#pagesList").html(data);
                        });
                        this.selectPage(n); //手动条用selectPage进行页码选中切换
                    }
                });
            });


        </script>




@endsection











