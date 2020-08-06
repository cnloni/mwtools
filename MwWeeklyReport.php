<?php
class MwWeeklyReport {
	private static $namespace = '週報';
	private static $template1 = <<<END
[[%{namespace}:%{lastWeekYear}年%{lastWeek}週|前週]] --- [[%{namespace}:%{nextWeekYear}年%{nextWeek}週|次週]]

== %{w[1]}(月) ==

== %{w[2]}(火) ==

== %{w[3]}(水) ==

== %{w[4]}(木) ==

== %{w[5]}(金) ==

== %{w[6]}(土) ==

== %{w[7]}(日) ==


<hr />
[[%{namespace}:%{lastWeekYear}年%{lastWeek}週|前週]] --- [[%{namespace}:%{nextWeekYear}年%{nextWeek}週|次週]]

END;
	private static $template2 = <<<END
* [[%{namespace}:%{thisYear}年%{thisWeek}週|%{thisYear}年%{thisWeek}週 (%{w[1]}-%{w[7]})]]

END;
	/*
		現在の週番号を計算し、週報のURLを求める。
		GET変数diffが設定されている時は、週番号にdiffを加える。
	*/
	public static function getTitle($diff) {
		list($wn,$year) = MwWeeklyReport::getWeekNumber($diff);
		$shortTitle = $year . '年' . $wn . '週';
		return MwWeeklyReport::$namespace . ':' . $shortTitle;
	}
	/*
		$diff = '1week' / '2weeks' ...
		$diff が数値なら週単位とみなす
	*/
	public static function modify($date, $diff) {
		if ($diff != null) {
			if (is_numeric($diff)) {
				// 数値なら週単位であるとみなす
				$diff = (string)(int)$diff . " weeks";
			}
			$date = $date->modify($diff);
		}
		return $date;
	}
	/*
		現在の週番号を計算する。
		GET変数diffが設定されている時は、週番号にdiffを加える。
		週は月曜日から始まるとし、１月４日を含む週をその年の第１週とする。
		従って、例えば１月２日が月曜日であれば、１月１日は前年の最終週である。
	*/
	public static function getWeekNumber($diff, $dd=null) {
		$date = new DateTime();
		$date = MwWeeklyReport::modify($date, $diff);
		$date = MwWeeklyReport::modify($date, $dd);
		$wn = $date->format('W');
		$year = $date->format('Y');
		if ($wn == 1 && $date->format('m') == 12) {
			$year++;
		}
		return array($wn, $year);
	}
	public static function getBlankPage($diff) {
		return MwWeeklyReport::getTemplate($diff, MwWeeklyReport::$template1);
	}
	public static function getTitleWithRange($diff) {
		return MwWeeklyReport::getTemplate($diff, MwWeeklyReport::$template2);
	}
	protected static function getTemplate($diff, $template) {
		$t = $template;
		$t = preg_replace('/%{namespace}/', MwWeeklyReport::$namespace, $t);
		//前週
		$a = MwWeeklyReport::getWeekNumber($diff, '-1 weeks');
		$t = preg_replace('/%{lastWeekYear}/', $a[1], $t);
		$t = preg_replace('/%{lastWeek}/', $a[0], $t);
		//次週
		$a = MwWeeklyReport::getWeekNumber($diff, '1 week');
		$t = preg_replace('/%{nextWeekYear}/', $a[1], $t);
		$t = preg_replace('/%{nextWeek}/', $a[0], $t);
		$a = MwWeeklyReport::getWeekNumber($diff);
		$t = preg_replace('/%{thisYear}/', $a[1], $t);
		$t = preg_replace('/%{thisWeek}/', $a[0], $t);
		$dt = new DateTime();
		for($i=1;$i<=7;$i++) {
			$dt->setISODate($a[1], $a[0], $i);
			$re = "/%{w\[" . $i . "\]}/";
			$value = $dt->format('n/j');
			$t = preg_replace($re, $value, $t);
		}
		return $t;
	}
}
