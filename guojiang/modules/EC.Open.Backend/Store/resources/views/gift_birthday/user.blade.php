<div class="tabs-container">
    @if (session()->has('flash_notification.message'))
        <div class="alert alert-{{ session('flash_notification.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {!! session('flash_notification.message') !!}
        </div>
    @endif

    <ul class="nav nav-tabs">
        <li class=""><a href="{{route('admin.promotion.gift.birthday.index')}}" aria-expanded="true"> 生日礼列表</a></li>
        <a no-pjax href="{{route('admin.promotion.gift.birthday.create')}}" class="btn btn-w-m btn-info pull-right">添加生日礼</a>
        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> 即将生日用户列表</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-1" class="tab-pane active">
            <div class="panel-body">

                <div class="col-md-3">

                    <input type="text" name="day" value="{{$day}}" placeholder="请输入活动天数"
                           class=" form-control num"> <span
                            class="input-group-btn">
                                       </span>

                </div>


                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="mobile" value="{{request('mobile')}}" placeholder="手机"
                               class=" form-control"> <span
                                class="input-group-btn">
                                        <button id="check" type="button" class="btn btn-primary">查找</button></span>
                    </div>
                </div>

                <br>
                <br>
                <br>


                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>昵称</th>
                        <th>电话</th>
                        <th>生日</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($lists)>0)
                        @foreach($lists as $item)
                            <tr>
                                <td>
                                    <a href="{{route('admin.users.edit',['id'=>$item->id])}}"
                                       target="_blank">{{$item->id}}</a>
                                </td>
                                <td>
                                    {{$item->nick_name}}
                                </td>

                                @if(!$item->mobile)
                                <td></td>
                                    @else
                                    <td>{{substr_replace($item->mobile,'****',3,4)}}</td>
                                @endif
                                <td>{{$item->birthday}}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>

                @if(count($lists)>0)
                    <div class="pull-lift">
                        {!! $lists->render() !!}
                    </div>
                @endif

                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

{!! Html::script(env("APP_URL").'/assets/backend/libs/jquery.zclip/jquery.zclip.js') !!}
<script>
    $(function () {

        $('.num').bind('input propertychange', function (e) {
            var value = $(e.target).val()
            if (!/^[-]?[0-9]*\.?[0-9]+(eE?[0-9]+)?$/.test(value)) {
                value = value.replace(/[^\d.].*$/, '');
                $(e.target).val(value);
            }
        });

        $('#check').click(function () {
            var day = $('input[name=day]').val();
            var mobile = $('input[name=mobile]').val();

            if (day == '') {
                swal({title: "查询失败", text: "请输入查询的天数", type: "error"});
                return false;
            }

            if (String(day).indexOf(".") > -1) {
                swal({title: "查询失败", text: "查询的天数必须是正整数", type: "error"});
                return false;
            }

            if (day < 1) {
                swal({title: "查询失败", text: "查询的天数最小1天", type: "error"});
                return false;
            }

            if (day > 30) {
                swal({title: "查询失败", text: "查询的天数最大30天", type: "error"});
                return false;
            }

            var str = '{{route('admin.promotion.gift.birthday.user')}}';
            location = str + "?day=" + day + "&mobile=" + mobile;
        })
    })
</script>