<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        {!! Form::open( [ 'url' => [route('admin.store.article.store')], 'method' => 'POST','id' => 'base-form','class'=>'form-horizontal'] ) !!}
        <input type="hidden" name="id" value="{{ $article->id }}">
        <div class="form-group">
            {!! Form::label('name','文章标题：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <input type="text" class="form-control" name="title" value="{{ $article->title }}">
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','文章副标题：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <input type="text" class="form-control" name="sub_title" value="{{ $article->sub_title }}">
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','文章发布人：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <input type="text" class="form-control" name="author" value="{{ $article->author }}">
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','发布人头衔：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <input type="text" class="form-control" name="author_title" value="{{ $article->author_title }}">
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','发布人头像：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <input type="hidden" name="author_avatar" value="{{ $article->author_avatar }}" />
                <img class="author_avatar" src="{{ $article->author_avatar }}">
                <div id="AuthorAvatarPicker">选择图片</div>

            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','文章分类：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="{{ \GuoJiangClub\EC\Open\Backend\Store\Model\Article::TYPE_STARS_RECOMMEND }}" {{ $article->type == \GuoJiangClub\EC\Open\Backend\Store\Model\Article::TYPE_STARS_RECOMMEND ? 'checked' : '' }}>
                        明星大咖推荐
                    </label>
                    <label>
                        <input type="radio" name="type" value="{{ \GuoJiangClub\EC\Open\Backend\Store\Model\Article::TYPE_EXCLUSIVE_CASES }}" {{ $article->type == \GuoJiangClub\EC\Open\Backend\Store\Model\Article::TYPE_EXCLUSIVE_CASES ? 'checked' : '' }}>
                        专属方案
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','展示图片：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <input type="hidden" name="img" value="{{ $article->img }}" />
                <img class="article-img" src="{{ $article->img }}">
                <div id="filePicker">选择图片</div>

            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">文章详情：</label>
            <div class="col-sm-9">
                <script id="container" name="article_detail" type="text/plain">
                    {!! $article->article_detail !!}
                </script>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">嵌入商品：</label>
            <div class="col-sm-9">
                <select class="form-control" id="goods_selector">
                </select>
            </div>
        </div>

        <input type="hidden" name="goods" value="0">

        <div class="form-group">
            {!! Form::label('name','是否推荐：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <div class="radio">
                    <label>
                        <input type="radio" name="is_recommend" value="1" {{ $article->is_recommend == 1 ? 'checked' : '' }}>
                        是
                    </label>
                    <label>
                        <input type="radio" name="is_recommend" value="0" {{ $article->is_recommend == 0 ? 'checked' : '' }}>
                        否
                    </label>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('name','状态：', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-9">
                <div class="radio">
                    <label>
                        <input type="radio" name="status" value="1" {{ $article->status == 1 ? 'checked' : '' }}>
                        发布
                    </label>
                    <label>
                        <input type="radio" name="status" value="0" {{ $article->status == 0 ? 'checked' : '' }}>
                        下架
                    </label>
                </div>
            </div>
        </div>

        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-md-offset-2 col-md-8 controls">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div id="spu_modal" class="modal inmodal fade"></div>
</div>
@include('UEditor::head')
{!! Html::script(env("APP_URL").'/vendor/libs/jquery.form.min.js') !!}
{!! Html::script(env("APP_URL").'/vendor/libs/webuploader-0.1.5/webuploader.js') !!}
@include('store-backend::article.script')