{!! Form::open( [ 'url' => [route('admin.promotion.reduce.update')], 'method' => 'POST', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}
<input type="hidden" name="id" value="{{$reduce->id}}">
<div class="form-group">
    <label class="col-sm-2 control-label">砍价商品：</label>
    <div class="col-sm-10">
        {{--<a class="btn btn-success" id="chapter-create-btn" data-toggle="modal"--}}
        {{--data-target="#modal" data-backdrop="static" data-keyboard="false"--}}
        {{--data-url="{{route('admin.promotion.reduce.getSpuModal')}}">--}}
        {{--点击选择--}}
        {{--</a>--}}

        <div class="row">
            <div class="col-sm-2">
                <img id="img" src="{{$reduce->goods->img}}" width="100">
            </div>
            <div class="col-sm-6">
                <p id="name">
                    <a href="{{route('admin.goods.edit',$reduce->goods_id)}}" target="_blank">{{$reduce->goods->name}}</a>
                </p>
                <p id="price">销售价：{{$reduce->goods->sell_price}}</p>
                <p id="nums">库存：{{$reduce->goods->store_nums}}</p>
            </div>
            <input type="hidden" name="goods_id" value="{{$reduce->goods_id}}">
            <input type="hidden" name="goods_nums" value="{{$reduce->goods->store_nums}}">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">活动名称：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="title" value="{{$reduce->title}}" placeholder="" required/>
    </div>
</div>



<div class="form-group">
    <label class="col-sm-2 control-label">砍价底价：</label>
    <div class="col-sm-10">
        <div class="input-group m-b"><span class="input-group-addon">¥</span>
            <input class="form-control" type="text" value="{{$reduce->price}}"  required name="price">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">帮砍人数(2-100)：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="number"  disabled value="{{$reduce->number}}"  placeholder="每个砍价订单的帮砍人数达到该人数才可以砍至低价" required/>
        <input type="hidden" name="number" value="{{$reduce->number}}" >
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">当前砍价商品库存:</label>
    <div class="col-sm-10">

        <input class="form-control" type="text" value="{{$reduce->store_nums}}"   name="reduce_store_nums" required placeholder="需小于商品库存">

    </div>
</div>


<div class="form-group" id="two-inputs">
    <label class="col-sm-2 control-label">活动时间：</label>
    <div class="col-sm-4">
        <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;开始</span>
            <input type="text" name="starts_at" class="form-control inline" id="date-range200" size="20" value="{{$reduce->starts_at}}"
                   placeholder="点击选择时间" readonly>
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
        <div id="date-range12-container"></div>
    </div>

    <div class="col-sm-4">
        <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;截止</span>
            <input type="text" name="ends_at" class="form-control inline" id="date-range201" size="20" value="{{$reduce->ends_at}}"
                   placeholder="" readonly>
            <span class="add-on"><i class="icon-th"></i></span>
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">砍价有效期（1-24）：</label>
    <div class="col-sm-10">
        <div class="input-group m-b"><span class="input-group-addon">小时</span>
            <input class="form-control" type="text" name="hour" value="{{$reduce->hour}}" disabled  required placeholder="自用户发起砍价到砍价截止的时间">
            <input type="hidden" name="hour" value="{{$reduce->hour}}" >
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">活动排序：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" value="{{$reduce->sort}}"  name="sort" required value="9"/>
    </div>
</div>




<div class="hr-line-dashed"></div>
<div class="form-group">
    <div class="col-sm-4 col-sm-offset-2">
        <a class="btn btn-primary" href="{{route('admin.promotion.reduce.index')}}">返回</a>
    </div>
</div>

{!! Form::close() !!}
