<?php

namespace App\Helpers;

class StringHelper{
	public static function natural_language_join(array $list, $conjunction = 'and') {
		$last = array_pop($list);
		if ($list) {
			return implode(', ', $list) . ' ' . $conjunction . ' ' . $last;
		}
		return $last;
	}
}