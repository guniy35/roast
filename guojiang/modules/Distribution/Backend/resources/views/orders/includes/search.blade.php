<form action="" method="get" class="form-horizontal">
    <div class="form-group">
        <div class="col-md-6">
            <div class="col-sm-6" style="padding-left: 0">
                <div class="input-group date form_datetime">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>&nbsp;&nbsp;时间</span>
                    <input type="text" class="form-control inline" name="stime"
                           value="{{request('stime')}}" placeholder="开始" readonly>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
            <div class="col-sm-5" style="padding-left: 0">
                <div class="input-group date form_datetime">
                                        <span class="input-group-addon" style="cursor: pointer">
                                            <i class="fa fa-calendar"></i></span>
                    <input type="text" class="form-control" name="etime" value="{{request('etime')}}"
                           placeholder="截止" readonly>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="input-group">
                <input type="hidden" value="{{request('status')}}" name="status">
                <div class="input-group-btn">
                    <select class="form-control" name="field" style="width: 150px">
                        <option value="">请选择条件搜索</option>
                        <option value="agent_order_no" {{request('field')=='agent_order_no'?'selected':''}} >
                            分销单号
                        </option>
                        <option value="name" {{request('field')=='name'?'selected':''}} >
                            分销员姓名
                        </option>
                        <option value="order_no" {{request('field')=='order_no'?'selected':''}} >订单编号
                        </option>
                    </select>
                </div>


                <input type="text" name="value" value="{{request('value')}}" placeholder="Search"
                       class="form-control">
                                           <span class="input-group-btn"> <button type="submit" class="btn btn-primary">搜索
                                               </button> </span>
            </div>
        </div>

        <div class="col-md-2">
            <a class="btn btn-primary ladda-button dropdown-toggle batch" data-toggle="dropdown"
               href="javascript:;" data-style="zoom-in">导出 <span
                        class="caret"></span></a>
            <ul class="dropdown-menu">

                <li><a class="export-order" data-toggle="modal"
                       data-target="#modal" data-backdrop="static" data-keyboard="false"
                       data-link="{{route('admin.distribution.orders.getExportData')}}" id="xls"
                       data-url="{{route('admin.export.index',['toggle'=>'xls'])}}"
                       data-type="xls"
                       href="javascript:;">导出xls格式</a></li>

                <li><a class="export-order" data-toggle="modal"
                       data-target="#modal" data-backdrop="static" data-keyboard="false"
                       data-link="{{route('admin.distribution.orders.getExportData')}}" id="csv"
                       data-url="{{route('admin.export.index',['toggle'=>'csv'])}}"
                       data-type="csv"
                       href="javascript:;">导出csv格式</a></li>

            </ul>
        </div>
    </div>
</form>