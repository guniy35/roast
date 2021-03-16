<script>
    $(function () {
        $.getScript('{{env("APP_URL").'/assets/backend/libs/datepicker/bootstrap-datetimepicker.js'}}',function () {
            $.fn.datetimepicker.dates['zh-CN'] = {
                days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
                daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
                months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                today: "今天",
                suffix: [],
                meridiem: ["上午", "下午"]
            };

            $('.form_datetime').datetimepicker({
                language: 'zh-CN',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                minuteStep: 1
            });
        });

    });

    /*清退*/
    $('.retreat').on('click', function () {
        var that = $(this);
        swal({
                    title: "确定清退该分销员吗?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    closeOnConfirm: false
                },
                function () {
                    $.post(that.data('url'), {
                                id: that.data('id'),
                                _token: $('meta[name="_token"]').attr('content')
                            },
                            function (result) {
                                if (result.status) {
                                    swal({
                                        title: "清退成功！",
                                        text: "",
                                        type: "success"
                                    }, function () {
                                        location.reload();
                                    });
                                }
                            });
                });
    });

    /*还原*/
    $('.restore').on('click', function () {
        var that = $(this);
        swal({
                    title: "确定恢复该分销员吗?",
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    closeOnConfirm: false
                },
                function () {
                    $.post(that.data('url'), {
                                id: that.data('id'),
                                _token: $('meta[name="_token"]').attr('content')
                            },
                            function (result) {
                                if (result.status) {
                                    swal({
                                        title: "恢复成功！",
                                        text: "",
                                        type: "success"
                                    }, function () {
                                        location.reload();
                                    });
                                }
                            });
                });
    });


    /*导出分销商数据*/
    $('.export-agents').on('click', function () {
        var url = $(this).data('link');
        var type = $(this).data('type');

        var param = funcUrlDel('page');
        console.log(param);
        if (param == '') {
            url = url + '?type=' + type;
        } else {
            url = url + '?' + param + '&type=' + type;
        }
        $(this).data('link', url);
    });
</script>