<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>ID</th>
        <th>分销员</th>
        <th>分销角色</th>
        <th>code</th>
        <th>用户数</th>
        @if(settings('distribution_level')>1)
            <th>下级分销员</th>
        @endif
        <th>订单数</th>
        <th>累计佣金(元)</th>
        <th>待结算佣金(元)</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($agents as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>
                <div class="thumb"><img src="{{$item->user->avatar}}" width="50" height="50"></div>
                <p>{{$item->name}}</p>
                <p>{{$item->mobile}}</p>
            </td>
            <td>
                {{$item->agent_role}}
            </td>
            <td>
                {{$item->code}}
            </td>
            <td>
                <a href="{{route('admin.distribution.agent.agentUsers',['id'=>$item->id])}}">{{$item->manyUsers()->count()}}</a>
            </td>

            @if(settings('distribution_level')>1)
                <td>
                    @for($i=2;$i<=settings('distribution_level');$i++)
                        {{$i}}级分销员数：{{$item->subAgentsCount($i)}}<br>
                    @endfor
                </td>
            @endif

            <td>
                <a href="{{route('admin.distribution.agent.orders.index',['id'=>$item->id,'status'=>'STATUS_ALL'])}}">
                    {{$item->orders()->count()}}
                </a>
            </td>
            <td>{{$item->calculateCash()}}</td>
            <td>{{$item->calculateCommission(0)}}</td>
            <td>
                <a class="btn btn-xs btn-primary"
                   href="{{route('admin.distribution.agent.edit',['id'=>$item->id])}}">
                    <i data-toggle="tooltip" data-placement="top"
                       class="fa fa-pencil-square-o"
                       title="修改"></i></a>


                <a class="btn btn-xs btn-primary"
                   href="{{route('admin.distribution.agent.agentUsers',['id'=>$item->id])}}">
                    <i data-toggle="tooltip" data-placement="top"
                       class="fa fa-user"
                       title="查看他的用户"></i></a>

                @if(settings('distribution_level')>1)
                    <a class="btn btn-xs btn-primary"
                       href="{{route('admin.distribution.agent.subAgent',['id'=>$item->id])}}">
                        <i data-toggle="tooltip" data-placement="top"
                           class="fa fa-sitemap"
                           title="查看他的下级分销员"></i></a>
                @endif

                <a class="btn btn-xs btn-primary"
                   href="{{route('admin.distribution.agent.orders.index',['id'=>$item->id,'status'=>'STATUS_ALL'])}}">
                    <i data-toggle="tooltip" data-placement="top"
                       class="fa fa-database"
                       title="查看他的订单"></i></a>

                <a class="btn btn-xs btn-primary"
                   href="{{route('admin.distribution.agent.commission.index',['id'=>$item->id])}}">
                    <i data-toggle="tooltip" data-placement="top"
                       class="fa fa-money"
                       title="查看他的佣金记录"></i></a>

                @if(env('MAODA_COMMISSION'))
                    <a class="btn btn-xs btn-primary"
                       href="{{route('admin.distribution.agent.orders.addAgentOrder',['id'=>$item->id])}}">
                        <i data-toggle="tooltip" data-placement="top"
                           class="fa fa-bookmark"
                           title="添加佣金"></i></a>
                @endif

                @if($item->status==1)
                    <a class="btn btn-xs btn-danger retreat" href="javascript:;"
                       data-id="{{$item->id}}"
                       data-url="{{route('admin.distribution.agent.retreatAgent')}}">
                        <i data-toggle="tooltip" data-placement="top"
                           class="fa fa-remove"
                           title="清退"></i></a>
                @elseif($item->status==3)
                    <a class="btn btn-xs btn-primary restore" href="javascript:;"
                       data-id="{{$item->id}}"
                       data-url="{{route('admin.distribution.agent.restoreAgent')}}">
                        <i data-toggle="tooltip" data-placement="top"
                           class="fa fa-refresh"
                           title="恢复"></i></a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>