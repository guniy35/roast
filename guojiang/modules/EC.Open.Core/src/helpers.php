<?php

/*
 * This file is part of ibrand/EC-Open-Core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!function_exists('collect_to_array')) {
	/**
	 * @param $collection
	 *
	 * @return array
	 */
	function collect_to_array($collection)
	{
		$array = [];
		foreach ($collection as $item) {
			$array[] = $item;
		}

		return $array;
	}
}

if (!function_exists('export_csv')) {
	function export_csv($data, $head, $file_name = '')
	{
		set_time_limit(10000);
		ini_set('memory_limit', '300M');

		/*Storage::makeDirectory('public/exports');
		 $path=storage_path('app/public/exports/') .$file_name.'.csv';*/

		$path = storage_path('exports') . '/' . $file_name . '.csv';

		$fp = fopen($path, 'w');

		foreach ($head as $i => $v) {
			$head[$i] = mb_convert_encoding($v, 'gbk', 'utf-8');
		}

		fputcsv($fp, $head);

		foreach ($data as $i => $v) {
			$row = [];
			foreach ($v as $key => $value) {
				$row[$key] = mb_convert_encoding($value, 'gbk', 'utf-8');
			}
			fputcsv($fp, $row);
		}
		fclose($fp);

		/*$result = '/storage/exports/' . $file_name . '.csv';*/

		return $path;
	}
}

if (!function_exists('isMobile')) {
	/**
	 * isMobile函数:检测参数的值是否为正确的中国手机号码格式
	 * 返回值:是正确的手机号码返回手机号码,不是返回false
	 */
	function isMobile($Argv)
	{
		$RegExp = '/^(\+?0?86\-?)?((13\d|14[57]|15[^4,\D]|17[678]|18\d)\d{8}|170[059]\d{7})$/';

		return preg_match($RegExp, $Argv) ? $Argv : false;
	}
}

if (!function_exists('isMail')) {
	/**
	 * isMail函数:检测是否为正确的邮件格式
	 * 返回值:是正确的邮件格式返回邮件,不是返回false
	 */
	function isMail($Argv)
	{
		$RegExp = '/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';

		return preg_match($RegExp, $Argv) ? $Argv : false;
	}
}

