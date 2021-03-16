<style type="text/css">
    .table-hover tr td {
        cursor: pointer
    }

    .pd10 {
        padding: 10px 0
    }
</style>

<div class="ibox float-e-margins">
    <div class="ibox-content" style="display: block;">
        <div class="row">
            <div class="panel-body">
                <div class="table-responsive">
                    @if(count($list)>0)
                        <table class="table" style="table-layout:fixed;">
                            <tbody>
                            <!--tr-th start-->
                            <tr>
                                <th>团长</th>
                                <th>开团时间</th>
                                <th>参团人数</th>
                                <th>状态</th>
                            </tr>
                            <!--tr-th end-->
                            @foreach ($list as $item)
                                <tr class="toggle-item" title="点击查看团员信息">
                                    <td>
                                        <img src="{{$item->getLeader()->meta['avatar']?$item->getLeader()->meta['avatar']:'/assets/backend/images/default_head_ico.png'}}"
                                             width="50"><br>
                                        昵称：{!! $item->getLeader()->meta['nick_name']?$item->getLeader()->meta['nick_name']:'/' !!}
                                        <br>
                                        手机：{{$item->getLeader()->user?$item->getLeader()->user->mobile:'/'}}
                                    </td>
                                    <td>{{$item->starts_at}}</td>
                                    <td>{{$item->getCountUser()}}</td>
                                    <td>
                                        {{$item->status_text}}
                                    </td>
                                </tr>

                                <tr class="show-item hide">
                                    <td colspan="4">
                                        @if($item->users()->where('is_leader', 0)->where('status',1)->count())
                                            <div class="col-sm-3 pd10">团员信息</div>
                                            <div class="col-sm-3 pd10">参团时间</div>
                                            <div class="clearfix"></div>
                                            @foreach($item->users as $user)
                                                @if($user->status==1 AND !$user->is_leader)
                                                    <div class="col-sm-3">
                                                        <img src="{{$user->meta['avatar']?$user->meta['avatar']:'/assets/backend/images/default_head_ico.png'}}"
                                                             width="30"><br>
                                                        昵称：{!! $user->meta['nick_name']?$user->meta['nick_name']:'/' !!}
                                                        &nbsp;
                                                        手机：{!! $user->user?$user->user->mobile:'/' !!}
                                                    </div>
                                                    <div class="col-sm-3">{{$user->created_at}}</div>
                                                    <div class="clearfix"></div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="pull-left">
                            &nbsp;&nbsp;共&nbsp;{!! $list->total() !!} 条记录
                        </div>

                        <div class="pull-right id='ajaxpag'">
                            {!! $list->appends(request()->except('page'))->render() !!}
                        </div>

                        <!-- /.box-body -->

                    @else
                        <div>
                            &nbsp;&nbsp;&nbsp;当前无数据
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $('.toggle-item').click(function () {
        $($(this).next()[0]).toggleClass('hide');
    })
</script>


