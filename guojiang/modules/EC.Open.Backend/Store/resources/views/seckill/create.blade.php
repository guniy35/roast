
    {!! Html::style(env("APP_URL").'/assets/backend/libs/dategrangepicker/daterangepicker.css') !!}
    {!! Html::style(env("APP_URL").'/assets/backend/libs/Tagator/fm.tagator.jquery.css') !!}
    <style type="text/css">
        input[type=file] {
            width: 72px;
            margin-top: 10px;
        }
    </style>

    <div class="ibox float-e-margins">
        <div class="ibox-content" style="display: block;">
            <div class="row">

                <div class="panel-body">
                    <input type="hidden" id="selected_spu">
                    @include('store-backend::seckill.includes.create_form')
                </div>

            </div>
        </div>
    </div>

    <div id="modal" class="modal inmodal fade" data-keyboard=false data-backdrop="static"></div>


    @include('store-backend::seckill.includes.scripts')
    <script>
        $('#base-form').ajaxForm({
            success: function (result) {
                if (!result.status) {
                    swal("保存失败!", result.message, "error")
                } else {
                    swal({
                        title: "保存成功！",
                        text: "",
                        type: "success"
                    }, function () {
                        location = '{{route('admin.promotion.seckill.index')}}';
                    });
                }

            }

        });
    </script>