<script>
    var compoent_html = ["  <li class=\"advert_li clearfix\" >",
        "                                <div class=\"del\">",
        "                                    <i class=\"fa fa-remove\"></i>",
        "                                </div>",
        "                                 <div class=\"box_img\">",
        "                                    <div class=\"img-upload img-upload-init\">",
        "                                           <div class=\"box_img\">",
        "                                            <img width=\"88\" height=\"88\" src='{#img#}'>",
        "                                            <div class=\"replace_img\">",
        "                                            <span>更换图片</span>",
        "                                            </div>",
        "                                        </div>",
        "                                    </div>",
        "                                    <div class=\"upload\">",
        "                                    </div>",
        "                                </div>",
        "                                <div class=\'box_input_group\'>",
        "                                <div class=\'box_input\'>",
        "                                     <label class=\"text-right\">标题:</label>",
        "                                     <div>{#title#}</div>",
        "                                </div>",
        "                                <div class=\'box_input\'>",
        "                                     <label class=\"text-right\">分类:</label>",
        "                                     <div>{#type#}</div>",
        "                                </div>",
        "                               </div>",
        "                            </li>"].join("");

    function index_init(){
        $('.advert_li').each(function (v,obj) {
            var old_index=$(obj).attr('index');
            $(obj).removeClass('advert_li_'+old_index);
            $(obj).attr('index',v+1);
            $(obj).addClass('advert_li_'+(v+1));
        })
    }

    //删除
    $("body .advert").on("click", ".del", function () {
        var remove = $(this).parents('.advert_li');
        remove.remove();
        index_init();
    });

    function addArticles() {

        var url = "{{route('admin.setting.micro.page.compoent.model.articles')}}"

        var new_url = url ;

        $('#promote-goods-btn').data('url', new_url)

        $("#promote-goods-btn").trigger("click");
    }


</script>

<script>

    window.box_index = null;

    $(function () {
        var length = $('.advert_li').length;
        $('.advert_li').each(function (val, index) {
            var num = $(this).attr('index');
            uploadImg('.upload-' + num, num, 'edit');
        })

    })

    function uploadImg(pick, index, action = 'create') {
        console.log(1);
        $(function () {
            var uploader = WebUploader.create({
                // 选完文件后，是否自动上传。
                auto: true,
                swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
                server: '{{route('upload.image',['_token'=>csrf_token()])}}',
                pick: pick,
                fileVal: 'upload_image',
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/*'
                }
            });
            // 文件上传成功，给item添加成功class, 用样式标记上传成功。
            uploader.on('uploadSuccess', function (file, response) {
                console.log(pick);
                addImg(pick, response.url);
                $(pick).find('.img-upload').removeClass('img-upload-init')
            });
        });
    }

    function addImg(pick, url,action = 'create') {

        console.log(pick);

        var img = [" <img width=\"88\" height=\"88\" src=\"" + url +
        "\" alt=\"\">",
            "                                     <div class=\"replace_img\">",
            "                                         <span >更换图片</span>",
            "                                     </div>"].join("");
        $(pick).find('.img-upload').html(img);

        if (action == 'edit') {
            $(pick).find('.img-upload').addClass('img-upload-end');
        }

        $(pick).find('i').remove();
    }


    function upload(pick) {
        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: true,
            swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
            server: '{{route('upload.image',['_token'=>csrf_token()])}}',
            pick: pick,
            fileVal: 'upload_image',
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });
    }

    //检查数据
    function testData() {
        var num = $('.advert_li').length;

    }

    //添加
    function add(id, str) {
        var div = $('#' + id);
        var li = div.append(str);
        var num = $('.advert_li').length;
        var ad = $('.advert_li').eq(num - 1).attr('index', num);
        $('.advert_li').eq(num - 1).addClass('advert_li_' + num,);
        $('.advert_li').eq(num - 1).find('.box_img').addClass('upload-' + num);
        $('.advert_li').eq(num - 1).find('.inputLink').addClass('inputLink-' + num);
        $('.advert_li').eq(num - 1).find('.inputLink').data('index', num);
        uploadImg('.upload-' + num, num);
    }

    $('input[name=is_show_title]').on('ifChecked', function(event){
        var is_show_title=$(this).val();
        if(is_show_title==0){
            $('#save').attr('is_show_title',0)
        }else{
            $('#save').attr('is_show_title',1)
        }
    });

    //保存
    function save(action){

        if(status==1){

            console.log(1);return;
        }

        var data={};

        var name=$('#advert-name').val();

        if(name==''){
            swal("保存失败!", '请输入标题', "error");return;
        }

        $('.advert_li').each(function (v, obj) {

            data[v]={'image':null,'type':null,'link':null, 'sort':null}

            var obj=$(obj);

            var img=obj.find('img').attr('src');
            if(typeof(img)=='undefined' || img==''){
                swal("保存失败!", '请上传图片', "error");return;
            }
            data[v]['image']=img;

            data[v]['type']='article';

            var id=obj.attr('data-article_id');

            data[v]['link']='/pages/article/detail/detail?id='+id;

            data[v]['associate_id']=id;

            data[v]['associate_type']='article';

            data[v]['sort']=v+1;

        })


        var input={};

        input.input=data;

        input.advert_id="{{$advert_id}}";

        input.advert_name=name;

        input.type=$('#style').val();

        input.advert_title=$('#advert-title').val();

        input.is_show_title=$('#save').attr('is_show_title');

        if(JSON.stringify(data) == "{}"){

            swal("保存失败!", '请添加文章', "error");return;
        }

        if(!check_input(input.input)) return;


        if(action=='edit'){

            status=1;

            var href="{{route('admin.setting.micro.page.compoent.update',['_token'=>csrf_token()])}}";

            $.post(href,input,function (result) {

                if (!result.status) {
                    swal("保存失败!", result.message, "error")
                } else {
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location = "{{route('admin.setting.micro.page.compoent.index',$type)}}";
                    });
                }

                status=0;

            })

        }

        if(action=='create'){

            status=1;

            {{--input.type="{{$type}}";--}}

            var href="{{route('admin.setting.micro.page.compoent.store',['_token'=>csrf_token()])}}";

            $.post(href,input,function (result) {

                if (!result.status) {
                    swal("保存失败!", result.message, "error")
                } else {
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location = "{{route('admin.setting.micro.page.compoent.index',$type)}}";
                    });
                }

                status=0;

            })

        }
    }

    function check_input(input) {
        for(k in input){
            if(!input[k]['image']){
                return false;
            }
        }
        return true;
    }

</script>

<script>
    (function () {
        'use strict';

        var byId = function (id) {
                return document.getElementById(id);
            },

            loadScripts = function (desc, callback) {
                var deps = [], key, idx = 0;

                for (key in desc) {
                    deps.push(key);
                }

                (function _next() {
                    var pid,
                        name = deps[idx],
                        script = document.createElement('script');

                    script.type = 'text/javascript';
                    script.src = desc[deps[idx]];

                    pid = setInterval(function () {
                        if (window[name]) {
                            clearTimeout(pid);

                            deps[idx++] = window[name];

                            if (deps[idx]) {
                                _next();
                            } else {
                                callback.apply(null, deps);
                            }
                        }
                    }, 30);

                    document.getElementsByTagName('head')[0].appendChild(script);
                })()
            },

            console = window.console;

        if (!console.log) {
            console.log = function () {
                alert([].join.apply(arguments, ' '));
            };
        }

        Sortable.create(byId('bar'), {
            group: "words",
            animation: 150,
            onAdd: function (evt) {
                console.log('onAdd.bar:', evt.item);
            },
            onUpdate: function (evt) {
                console.log('onUpdate.bar:', evt.item);
            },
            onRemove: function (evt) {
                console.log('onRemove.bar:', evt.item);
            },
            onStart: function (evt) {
                $('body .module-box-son').addClass('box-select-hide');
                console.log('onStart.foo:', evt.item);
            },
            onEnd: function (evt) {
                $('body .module-box-son').addClass('box-select-hide');
                console.log('onEnd.foo:', evt.item);
                index_init();
            }
        });

    })();


</script>