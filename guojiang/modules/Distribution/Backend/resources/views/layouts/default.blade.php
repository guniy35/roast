@extends('backend::layouts.default')

@section('sidebar-menu')

    <li class="{{ Active::pattern('admin/distribution/setting*') }}">
        <a href="#">
            <i class="iconfont icon-fenxiaoshezhi"></i>
            <span class="nav-label">分销设置</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Active::pattern('admin/distribution/setting/sys_setting') }}">
                <a href="{{route('admin.distribution.system.setting')}}">系统设置</a></li>

            <li class="{{ Active::pattern('admin/distribution/setting/goods*') }}">
                <a href="{{route('admin.distribution.goods.setting',['status'=>'ACTIVITY'])}}">商品设置</a></li>
        </ul>
    </li>

    <li class="{{ Active::pattern(['admin/distribution/cash*','admin/distribution/agent*']) }}">
        <a href="#">
            <i class="iconfont icon-fenxiaoyuanguanli"></i>
            <span class="nav-label">分销员管理</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Active::pattern('admin/distribution/agent*') }}">
                <a href="{{route('admin.distribution.agent.index',['status'=>'STATUS_AUDITED'])}}">分销员列表</a></li>


            <li class="{{ Active::pattern('admin/distribution/cash*') }}">
                <a href="{{route('admin.balance.cash.index',['status'=>'STATUS_AUDIT'])}}">佣金提现管理</a></li>


        </ul>
    </li>

    <li class="{{ Active::pattern('admin/distribution/orders*') }}">
        <a href="#">
            <i class="iconfont icon-fenxiaodingdan"></i>
            <span class="nav-label">分销订单管理</span>
            <span class="fa arrow"></span>
        </a>
        <ul class="nav nav-second-level collapse">
            <li class="{{ Active::pattern('admin/distribution/orders*') }}">
                <a href="{{route('admin.distribution.orders.index',['status'=>'ALL'])}}">订单列表</a></li>


        </ul>
    </li>
@endsection


