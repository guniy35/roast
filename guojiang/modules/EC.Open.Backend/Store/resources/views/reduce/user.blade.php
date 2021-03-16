<div class="tabs-container">
    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">

            <div class="panel-body">
                {!! Form::open( [ 'route' => ['admin.promotion.reduce.getUserLists'], 'method' => 'get', 'id' => 'base-form','class'=>'form-horizontal'] ) !!}
                <input type="hidden" name="reduce_items_id" value="{{request('reduce_items_id')}}">
                <input type="hidden" name="title" value="{{request('title')}}">
                <div class="form-group">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" name="mobile" value="{{request('mobile')}}"  placeholder="输入手机号">
                    </div>

                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary">搜索</button>

                    </div>
                </div>
                {!! Form::close() !!}

                <div class="table-responsive">

                    @include('store-backend::reduce.user-list')

                </div>

            </div>
        </div>
    </div>
</div>













