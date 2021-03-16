<script>
    var ue = UE.getEditor('container', {
	    autoHeightEnabled: false,
	    initialFrameHeight: 500
    });
    ue.ready(function () {
	    //此处为支持laravel5 csrf ,根据实际情况修改,目的就是设置 _token 值.
	    ue.execCommand('serverparam', '_token', '{{ csrf_token() }}');
    });

    $('#base-form').ajaxForm({
	    success: function (result) {
		    if (result.status) {
			    swal({
				    title: "保存成功！",
				    text: "",
				    type: "success"
			    }, function () {
				    location = '{{route('admin.store.article.index')}}';
			    });
		    } else {
			    swal("保存失败!", result.message, "error");
		    }
	    }
    });

    $(function () {
	    var uploader = WebUploader.create({
		    auto: true,
		    swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
		    server: '{{route('upload.image',['_token'=>csrf_token()])}}',
		    pick: '#filePicker',
		    fileVal: 'upload_image',
		    accept: {
			    title: 'Images',
			    extensions: 'gif,jpg,jpeg,bmp,png',
			    mimeTypes: 'image/*'
		    }
	    });

	    uploader.on('uploadSuccess', function (file, response) {
		    $('.article-img').attr('src', response.url).show();
		    $("input[name='img']").val(response.url)

	    });

	    var authorUploader = WebUploader.create({
		    auto: true,
		    swf: '{{url(env("APP_URL").'/assets/backend/libs/webuploader-0.1.5/Uploader.swf')}}',
		    server: '{{route('upload.image',['_token'=>csrf_token()])}}',
		    pick: '#AuthorAvatarPicker',
		    fileVal: 'upload_image',
		    accept: {
			    title: 'Images',
			    extensions: 'gif,jpg,jpeg,bmp,png',
			    mimeTypes: 'image/*'
		    }
	    });

	    authorUploader.on('uploadSuccess', function (file, response) {
		    $('.author_avatar').attr('src', response.url).show();
		    $("input[name='author_avatar']").val(response.url)

	    });
    });


    $(document).ready(function () {
	    $('#goods_selector').select2({
		    ajax: {
			    delay: 1000,
			    type: 'POST',
			    url: '{{ route('admin.store.article.getSpuData') }}',
			    dataType: 'json',
			    data: function (params) {
				    var query = {
					    page: params.page || 1,
					    value: params.term,
					    field: 'name'
				    };

				    return query;
			    },
			    processResults: function (res, params) {
				    params.page = params.page || 1;

				    return {
					    results: res.data.data,
					    pagination: {
						    more: params.page < res.data.total
					    }
				    };
			    },
			    cache: false
		    },
		    escapeMarkup: function (markup) {
			    return markup;
		    },
		    templateResult: function (repo) {
			    if (repo.loading) {
				    return repo.text;
			    }

			    var markup = "<div data-id='" + repo.id + "'><div class='select2-result-repository_img' style='display: inline'><img src='" + repo.img + "' width='50'/></div> <div class='select2-result-repository__title' style='display: inline'>" + repo.name + "</div></div>";
			    return markup;
		    },
		    templateSelection: function (repo) {
			    return repo.name;
		    }
	    });

	    $('#goods_selector').on('select2:select', function (e) {
		    var goods = e.params.data;
		    ue.execCommand('inserthtml', '<p>goods_' + goods.id + '</p>');

		    $('#goods_selector').val(null);
	    });
    });
</script>