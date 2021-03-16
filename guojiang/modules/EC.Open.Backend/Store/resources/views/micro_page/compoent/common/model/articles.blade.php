@extends('store-backend::micro_page.bootstrap_modal')


@section('modal_class')
     modal-lg
@stop

@section('title')
    选择文章
    {{--{{request('index')}},{{request('page_id')}}--}}
@stop

@section('after-styles-end')
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop

@section('body')

    <br>
    <br>
    <div class="row">


        <form class="form-horizontal"  action="{{route('admin.setting.micro.page.compoent.getArticlesData')}}" method="get" id="search_spu_from">

            <div class="col-md-12">
                <div class="form-group">
                    <label for="exampleInputEmail1" class="col-sm-3 control-label"> 文章标题:</label>
                    <div class="col-sm-7">
                        <input type="text" name="title" value="" class="form-control" placeholder="">
                    </div>
                    <div class="col-sm-2">
                        <button type="submit"  id="send" class="ladda-button btn btn-primary" data-style="slide-right"
                                data-toggle="form-submit" data-target="#search_spu_from">搜索
                        </button>
                    </div>
                </div>

                @if($articles_)
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="col-sm-3 control-label"></label>
                        <div class="col-sm-6">
                            <a target="_blank"  href="">{{$articles_->title}}</a>
                        </div>
                    </div>
                @else
                    @if(request('article_id'))
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <span class="label label-default">文章ID：{{request('article_id')}};不存在</span>
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
            <div class="table-responsive col-sm-9" id="pagesList" data-article_id="{{request('article_id')}}">

            </div>

            <div class="col-sm-1">

            </div><!-- /.box-body -->

            <div id="kkpager" style="margin-left: 180px;"></div>

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
    <button type="button" onclick="sendIds();"  class="ladda-button btn btn-primary" > 确定
        <script>

            var index="{{request('index')}}"
            function cancel() {
                var article_id=$('#app').attr('data-article_id');
                if(!article_id){
                    $('#goods_modal').data('index');
                }
            }
            function sendIds() {
                var article_id=$('#app').attr('data-article_id');
                if(!article_id){
                    swal({
                        title: '保存失败',
                        text: '请选择关联的文章',
                        type: "error"
                    });
                    return false;
                }
                console.log(article_id)


                var data = {
                    title : $('#article_id_'+article_id).data('title'),
                    img : $('#article_id_'+article_id).data('img'),
                    type:$('#article_id_'+article_id).data('type')
                };

                console.log(data);

                compoent_componet_html=compoent_html.replace('{#type#}',data.type);
                compoent_componet_html=compoent_componet_html.replace('{#title#}',data.title);
                compoent_componet_html=compoent_componet_html.replace('{#img#}',data.img);

                $('#bar').append(compoent_componet_html);

                $('#goods_modal').modal('hide');

                var length = $('.advert_li').length;

                var obj=$('.advert_li').eq(length-1);

                obj.attr('index',length);

                obj.attr('data-article_id',article_id);

                obj.addClass('advert_li_'+length);

                obj.find('.box_img').addClass('upload-'+length)

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
                    _token:_token
                };
                $.get('{{route('admin.setting.micro.page.compoent.getArticlesData',['article_id'=>request('article_id')])}}', data, function (ret) {

                    console.log(ret);
                    $('#pagesList').html(ret);
                });

                //搜索
                $('#search_spu_from').ajaxForm({
                    success: function (result) {
                        $('#pagesList').html(result);
                        var totalPage=$('#app').attr('data-totalPage');
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
                            click:function(n){
                                var title=$('input[name=title]').val();
                                var data={
                                    _token:"{{csrf_token()}}",
                                };
                                if(title){
                                    data.title=title;
                                }
                                $.get("{{route('admin.setting.micro.page.compoent.getArticlesData')}}?page="+n,data, function(data){
                                    $("#pagesList").html("");
                                    $("#pagesList").html(data);
                                    kkpager.selectPage(n,totalPage);
                                });

                            }
                        });
                    }
                });

                //分页
                var totalPage = "{{$articles->lastPage()}}";
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
                    click:function(n){
                        var title=$('input[name=title]').val();
                        var data={
                            _token:"{{csrf_token()}}",
                        };
                        if(title){
                            data.title=title;
                        }
                        $.get("{{route('admin.setting.micro.page.compoent.getArticlesData')}}?page="+n,data, function(data){
                            $("#pagesList").html("");
                            $("#pagesList").html(data);
                        });
                        this.selectPage(n); //手动条用selectPage进行页码选中切换
                    }
                });
            });



        </script>




@endsection











