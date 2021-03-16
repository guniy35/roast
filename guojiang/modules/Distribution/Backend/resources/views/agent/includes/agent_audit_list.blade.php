<table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <th>姓名</th>
        <th>手机</th>
        <th>申请时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($agents as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>{{$item->mobile}}</td>
            <td>{{$item->created_at}}</td>
            <td>
                    <a class="btn btn-xs btn-primary"
                       href="{{route('admin.distribution.agent.audit',['id'=>$item->id])}}">
                        <i data-toggle="tooltip" data-placement="top"
                           class="fa fa-pencil-square-o"
                           title="审核"></i>
                    </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>