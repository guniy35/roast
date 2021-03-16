<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div class="row">
            @include('backend-distribution::orders.includes.order_show_base')
        </div>

        <div class="row">
            @include('backend-distribution::orders.includes.order_show_agent')
        </div>
        <div class="row">
            @include('backend-distribution::orders.includes.order_show_item')
        </div>
        <div class="hr-line-dashed"></div>

        <div class="col-md-offset-2 col-md-8 controls clearfix">

            <a href="javascript:history.go(-1)"
               class="btn btn-danger">返回</a>
        </div>
        <div class="clearfix"></div>
    </div>
</div>