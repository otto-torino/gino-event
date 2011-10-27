<?php

interface eventInt {

	public function show($img_prop=null);
	public function formEvent($action, $interface);
	public static function getOrderedItems($instance, $options=array());
	public static function getDateEvents($instance, $date, $ctg=null, $private=false, $bool=false);

}
?>