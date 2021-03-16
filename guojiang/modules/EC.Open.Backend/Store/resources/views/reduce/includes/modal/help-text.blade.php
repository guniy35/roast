@extends('store-backend::layouts.bootstrap_modal')

@section('modal_class')
    modal-lg
@stop
@section('title')
    活动规则
@stop

@section('after-styles-end')
    {!! Html::style(env("APP_URL").'/assets/backend/libs/ladda/ladda-themeless.min.css') !!}
@stop

@section('body')

    <div class="row">

        <form class="form-horizontal" action="" method="get" id="">

            <div class="col-md-12">
                <div class="form-group" style="margin-top: 50px;">
                    <div class="col-sm-12">

                        <textarea name="reduce_help_text" class="col-sm-12" id="reduce_help_text" rows="10"
                                  placeholder="砍价活动规则用于前端文案显示">{{settings('reduce_help_text')}}</textarea>

                    </div>

                </div>
            </div>

        </form>

    </div>
@stop

@section('footer')

    <button type="button" class="btn btn-link" data-dismiss="modal">取消</button>
    <button type="button" onclick="store();" class="ladda-button btn btn-primary"> 确定
        <script>
            function store() {
                var data = {
                    reduce_help_text: $('#reduce_help_text').val(),
                    _token: _token
                };

                var url ="{{route('admin.promotion.reduce.settings')}}";
                    $.post(url, data, function (res) {
                        swal({
                            title: "设置成功！",
                            text: "",
                            type: "success"
                        }, function () {
                            location = '';
                        });
                    })


            }
        </script>

@endsection










