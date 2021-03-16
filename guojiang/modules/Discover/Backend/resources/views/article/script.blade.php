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
				    location = '{{route('admin.bai.jia.article.index')}}';
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
    })
</script>