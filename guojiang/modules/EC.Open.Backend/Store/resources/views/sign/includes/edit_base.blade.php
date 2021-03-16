<h4>签到活动基础信息</h4>

<div class="form-group">
    <label class="col-sm-2 control-label"><span class="sp-require">*</span>活动名称：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="title" value="{{$sign->title}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">说明：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="label" value="{{$sign->label}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">分享文案：</label>
    <div class="col-sm-10">
        <input type="text" class="form-control" name="share_text" value="{{$sign->share_text}}"/>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">状态：</label>
    <div class="col-sm-10">
        <label class="checkbox-inline i-checks"><input name="status" type="radio"
                                                       value="1" {{$sign->status?'checked':''}}> 启用</label>
        <label class="checkbox-inline i-checks"><input name="status" type="radio"
                                                       value="0" {{$sign->status?'':'checked'}}> 禁用</label>
    </div>
</div>
<hr>