<?php

namespace GuoJiangClub\Discover\Backend\Middleware;

use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

class Bootstrap
{
	public function handle(Request $request, \Closure $next)
	{
		/*Admin::css('/assets/backend/libs/Tagator/fm.tagator.jquery.css');
		Admin::css('/assets/backend/css/jquery.fonticonpicker.min.css');
		Admin::css('/assets/backend/css/jquery.fonticonpicker.bootstrap.min.css');
		Admin::css('/assets/backend/css/iconfont.css');

		Admin::js('/assets/backend/libs/Tagator/fm.tagator.jquery.js');
		Admin::js('/assets/backend/js/jquery.fonticonpicker.min.js');

		if (file_exists($bootstrap = __DIR__ . '/../../Extensions/' . 'bootstrap.php')) {
			require $bootstrap;
		}*/

		$script = <<<EOT
$('body').on('ifChanged', '.grid-select-all', function(event) {
    if (this.checked) {
        $('.grid-row-checkbox').iCheck('check');
    } else {
        $('.grid-row-checkbox').iCheck('uncheck');
    }
});

$('body').on('ifChanged', '.grid-row-checkbox', function () {
	if (this.checked) {
	    $(this).closest('tr').css('background-color', '#ffffd5');
	} else {
	    $(this).closest('tr').css('background-color', '');
	}
});
EOT;

		Admin::script($script);

		return $next($request);
	}
}