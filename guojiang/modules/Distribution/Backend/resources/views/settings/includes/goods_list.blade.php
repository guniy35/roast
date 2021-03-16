<div>
    <div class="col-md-8">
        <input type="checkbox" class="check-full"> 全选 &nbsp;&nbsp;&nbsp;&nbsp;

        <a class="btn btn-primary ladda-button dropdown-toggle batch" data-toggle="dropdown"
           href="javascript:;" data-style="zoom-in">批量设置佣金 <span
                    class="caret"></span></a>
        <ul class="dropdown-menu">

            <li><a class="export-goods" data-toggle="modal-filter"
                   data-target="#modal" data-backdrop="static" data-keyboard="false"
                   data-url="{{route('admin.distribution.goods.editBatchGoods',['type'=>'rate','rate_type'=>'default','status'=>request('status'),'value'=>request('value')])}}"

                   href="javascript:;">批量设置普通推客佣金</a></li>

            <li><a class="export-goods" data-toggle="modal-filter"
                   data-target="#modal" data-backdrop="static" data-keyboard="false"
                   data-url="{{route('admin.distribution.goods.editBatchGoods',['type'=>'rate','rate_type'=>'organ','status'=>request('status'),'value'=>request('value')])}}"

                   href="javascript:;">批量设置机构推客佣金</a></li>

            <li><a class="export-goods" data-toggle="modal-filter"
                   data-target="#modal" data-backdrop="static" data-keyboard="false"
                   data-url="{{route('admin.distribution.goods.editBatchGoods',['type'=>'rate','rate_type'=>'shop','status'=>request('status'),'value'=>request('value')])}}"

                   href="javascript:;">批量设置门店推客佣金</a></li>
        </ul>

        <a class="btn btn-primary" data-toggle="modal-filter"
           data-target="#modal" data-backdrop="static" data-keyboard="false"
           data-url="{{route('admin.distribution.goods.editBatchGoods',['type'=>'status','status'=>request('status'),'value'=>request('value')])}}"
           href="javascript:;">
            分销状态设置
        </a>
    </div>

    <div class="col-md-4">
        <form action="" method="get" class="form-horizontal">
            <div class="form-group">
                <input type="hidden" name="status" value="{{request('status')}}">
                <div class="input-group">
                    <input type="text" name="value" placeholder="商品名称"
                           value="{{!empty(request('value'))?request('value'):''}}"
                           class=" form-control"> <span
                            class="input-group-btn">
    <button type="submit" class="btn btn-primary">查找</button> </span></div>

            </div>
        </form>
    </div>

</div>


<table class="table table-hover table-striped table-bordered" id="goods-table">
    <thead>
    <tr>
        <th><input type="checkbox" class="check-all"></th>
        <th>商品</th>
        <th>是否参与推广</th>
        <th>佣金比例</th>
        <th>预估佣金</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($goods as $value)
        @if($good=$value->Goods)
            <tr class="goods{{$value->id}}" data-id="{{$value->id}}">
                <td><input class="checkbox" type="checkbox" value="{{$value->id}}" name="ids[]"></td>
                <td>
                    <div class="thumb"><img src="{{$good->img}}" width="50"></div>
                    <p>{{$good->name}}</p>
                    <p>{{$value->goods_sell_price}}</p>
                </td>
                <td>
                    {{$value->activity==1?'参与':'不参与'}}
                </td>
                <td>
                    <p>普通：{{$value->rate}}%</p>
                    <p>机构：{{$value->rate_organ}}%</p>
                    <p>门店：{{$value->rate_shop}}%</p>
                </td>
                <td>
                    <p>普通：{{$value->getGoodsCommission()}}</p>
                    <p>机构：{{$value->getGoodsCommission('organ')}}</p>
                    <p>门店：{{$value->getGoodsCommission('shop')}}</p>
                </td>
                <td>
                    <a class="btn btn-xs btn-primary" data-toggle="modal"
                       data-target="#modal" data-backdrop="static" data-keyboard="false"
                       data-url="{{route('admin.distribution.goods.editGoods',['id'=>$value->id])}}"
                       href="javascript:;">
                        <i data-toggle="tooltip" data-placement="top"
                           class="fa fa-pencil-square-o"
                           title="编辑"></i>
                    </a>
                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>