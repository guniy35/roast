@extends('cms::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    @if($action == 'view')
        查看已选择商品
    @elseif($action == 'view_exclude')
        查看已排除商品
    @else
        选择商品
    @endif
@stop

@section('after-styles-end')
    <style type="text/css">
        .modal-footer{
            background:#f8fafb;
        }
    </style>
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop


@section('body')
    <div>
    @if($action == 'add')
        <div class="row">
            <div class="col-sm-3">
                <select class="form-control" name="field">
                    <option value="name" {{!empty(request('field')=='name')?'selected ':''}}>商品名称</option>
                    <option value="goods_no" {{!empty(request('field')=='goods_no')?'selected ':''}}>商品编码</option>
                    <option value="sku" {{!empty(request('field')=='sku')?'selected ':''}}>SKU编码</option>
                </select>
            </div>
            <div class="col-sm-7">
                <input type="text" name="value" placeholder="Search" value="{{!empty(request('value'))?request('value'):''}}" class=" form-control">
            </div>
            <div class="col-sm-2">
                <button type="button" id="send" class="ladda-button btn btn-primary">搜索</button>
            </div>
        </div>
    @endif

    <div class="panel-body">
            @if($action == 'add')
            <h3 class="header">请选择商品：</h3>
        @endif
        <div class="table-responsive" id="goodsList">
                <table class="table table-hover table-striped">
                    <thead>
                    <tr>
                        <th>商品名称</th>
                        <th>销售价</th>
                        <th>库存</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody class="page-goods-list">

                    </tbody>
                </table>
            </div>
            <div class="pages">
            </div>
        </div>
        <div style="text-align: center">
            <input type="hidden" id="temp_selected_spu" value="{{ isset($goods) && !empty($goods) && !empty($goods->goods) ? $goods->goods : ''}}">
    <input type="hidden" id="temp_exclude_spu" value="">

    <button type="button" class="btn btn-primary" data-dismiss="modal">取消</button>

    <button type="button" onclick="sendIds('{{$action}}');" class="ladda-button btn btn-primary"> 确定
    </button>
        </div>
    </div>
    <script type="text/html" id="page-temp">
        <tr>
            <td>
                <img src="{#img#}" alt="" style="width: 30px; height: 30px"> &nbsp;
                {#name#}
            </td>
            <td>
                {#sell_price#}
            </td>
            <td>
                {#store_nums#}
            </td>
            <td>
                <button onclick="changeSelect(this, '{{$action}}')" class="btn btn-circle {#class#}"
                        type="button" data-id="{#id#}"><i class="fa fa-{#icon#}"></i>
                </button>
            </td>
        </tr>
    </script>
@stop
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/spin.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/ladda/ladda.jquery.min.js') !!}
{!! Html::script(env("APP_URL").'/assets/backend/libs/loader/jquery.loader.min.js') !!}


@section('footer')
    @include('store-backend::article.modal.script')
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/common.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/jquery.http.js') !!}
    {!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.el/page/jquery.pages.js') !!}
    <script>
        var action = '{{$action}}';
        var paraDiscount = {_token: $('meta[name="_token"]').attr('content')};

        function getList() {

	        var postUrl = '{{route('admin.store.article.getSpuData')}}';

	        if (action == 'exclude' || action == 'view_exclude') {
		        var selected_spu = $('#exclude_spu').val();
	        } else {
		        var selected_spu = $('#selected_spu').val();
	        }

	        $('.pages').pages({
		        page: 1,
		        url: postUrl,
		        get: $.http.post.bind($.http),
		        body: {
			        _token: $('meta[name="_token"]').attr('content'),
			        action: action,
			        ids: paraDiscount.ids,
			        field: $("select[name=field] option:selected").val(),
			        value: $("input[name=value]").val()
		        },
		        marks: {
			        total: 'data.last_page',
			        index: 'data.current_page',
			        data: 'data'
		        }
	        }, function (data) {
		        var html = '';
		        var ids = data.ids;

		        data.data.forEach(function (item) {
			        if (!~ids.indexOf(String(item.id))) {
				        item.class = 'btn-warning unselect';
				        item.icon = 'times';

			        } else {
				        item.class = 'btn-info select';
				        item.icon = 'check';
			        }

			        html += $.convertTemplate('#page-temp', item, '');
		        });
		        $('.page-goods-list').html(html);
	        });
        }

        $(document).ready(function () {

	        if (action == 'exclude' || action == 'view_exclude') {
		        $('#temp_exclude_spu').val($('#exclude_spu').val());
		        paraDiscount.ids = $('#temp_exclude_spu').val();
	        } else {
		        $('#temp_selected_spu').val($('#selected_spu').val());
		        paraDiscount.ids = $('#temp_selected_spu').val();

	        }

	        getList();
        });

        $('#send').on('click', function () {
	        getList();
        });
    </script>
@stop






