<div class="ibox float-e-margins">
    <div class="ibox-content">
        <a href="{{ route('admin.bai.jia.article.create') }}" class="btn btn-primary margin-bottom" no-pjax>添加文章</a>
            <div>
                <div class="box-body table-responsive">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr>
                            <th>标题</th>
                            <th>副标题</th>
                            <th>文章分类</th>
                            <th>是否推荐</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        @foreach($articles as $article)
                            <tr>
                            <td><img src="{{ $article->img }}" width="100"> {{$article->title}}</td>
                            <td>{{$article->sub_title}}</td>
                            <td>{{$article->type_text}}</td>
                            <td>@if($article->is_recommend==1) 是 @else 否 @endif</td>
                            <td>@if($article->status==1) 发布 @else 下架 @endif</td>
                            <td>
                                <a class="btn btn-xs btn-primary" href="{{route('admin.bai.jia.article.edit',['id'=>$article->id])}}">
                                    <i data-toggle="tooltip" data-placement="top" class="fa fa-pencil-square-o" title="" data-original-title="编辑"></i></a>
                                <a class="btn btn-xs btn-danger article-delete" data-url="{{ route('admin.bai.jia.article.delete', ['id'=>$article->id]) }}">
                                    <i data-toggle="tooltip" data-placement="top" class="fa fa-trash" title="删除"></i></a>
                                <a>
                                    <i class="fa switch @if($article->status) fa-toggle-on @else fa-toggle-off @endif" title="切换状态" value= {{$article->status}} ><input type="hidden" value={{$article->id}}></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div id="comment_modal" class="modal inmodal fade"></div>
                </div>
                <div class="clearfix"></div>
                <div class="box-footer clearfix">
                    {!! $articles->render() !!}
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.article-delete').on('click', function () {
	        var thisPoint = $(this);
	        var url = thisPoint.data('url');
	        swal({
			        title: "确认删除此项？",
			        imageUrl: "/assets/backend/activity/backgroundImage/delete-xxl.png",
			        showCancelButton: true,
			        confirmButtonColor: "#DD6B55",
			        confirmButtonText: "确认",
			        cancelButtonText: "取消",
			        closeOnConfirm: false,
			        closeOnCancel: true
		        },
		        function (isConfirm) {
			        if (isConfirm) {
				        $.ajax({
					        type: "GET",
					        url: url,
					        success: function (data) {
						        if (data.status) {
							        swal({
									        title: "删除成功",
									        text: "",
									        type: "success"
								        },
								        function () {
									        location.reload();
								        });
						        }
					        }
				        });
			        } else {
			        }
		        });
        });

        $('.switch').on('click', function () {
	        var value = $(this).attr('value');
	        var modelId = $(this).children('input').attr('value');


	        value = parseInt(value);
	        modelId = parseInt(modelId);

	        if (value == 1) {
		        value = 0;
	        } else {
		        value = 1;
	        }

	        var that = $(this);
	        $.post("{{route('admin.bai.jia.article.status')}}",
		        {
			        status: value,
			        modelId: modelId
		        },
		        function (data, status) {
			        if (status) {
				        if (1 == value) {
					        that.removeClass('fa-toggle-off');
					        that.addClass('fa-toggle-on');
					        that.parent('a').parent('td').prev('td').html('发布');
				        } else {
					        that.removeClass('fa-toggle-on');
					        that.addClass('fa-toggle-off');
					        that.parent('a').parent('td').prev('td').html('下架');
				        }

				        that.attr('value', value);
			        }
		        });

        })
    </script>