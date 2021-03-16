<ul class="nav nav-tabs">
    <li @if($type=='micro_page_componet_slide')class="active"@endif >
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_slide')}}">幻灯片</a>
    </li>
    <li @if($type=='micro_page_componet_coupon')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_coupon')}}">优惠券</a>
    </li>
    <li @if($type=='micro_page_componet_nav')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_nav')}}">快捷导航</a>
    </li>
    <li @if($type=='micro_page_componet_cube')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_cube')}}">魔方</a>
    </li>
    <li @if($type=='micro_page_componet_seckill')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_seckill')}}">秒杀</a>
    </li>
    {{--<li @if($type=='micro_page_componet_free_event')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_free_event')}}">集call</a>
    </li>--}}
    <li @if($type=='micro_page_componet_groupon')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_groupon')}}">拼团</a>
    </li>
    {{--<li @if($type=='micro_page_componet_suit')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_suit')}}">套餐</a>
    </li>--}}
    <li @if($type=='micro_page_componet_category')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_category')}}">分类商品</a>
    </li>
    <li @if($type=='micro_page_componet_goods_group')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_goods_group')}}">商品分组</a>
    </li>
    <li @if($type=='micro_page_componet_article')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_article')}}">文章</a>
    </li>
    <li @if($type=='micro_page_componet_guess_like')class="active"@endif>
        <a href="{{route('admin.setting.micro.page.compoent.index','micro_page_componet_guess_like')}}">猜你喜欢</a>
    </li>
</ul>