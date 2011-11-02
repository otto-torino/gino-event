<?php

interface eventInt {

	public function show($img_prop=null);
	public function formItem($action, $interface);
	public static function getOrderedItems($instance, $options=array());
	public static function getDateItems($instance, $date, $ctg=null, $private=false, $bool=false);
}
?>