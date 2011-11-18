<?php

interface eventInt {

	public function show($img_prop=null);
	public function formItem($action, $interface, $options=null);
	public function formDelItem($action);
	public static function getOrderedItems($instance, $options=array());
}
?>