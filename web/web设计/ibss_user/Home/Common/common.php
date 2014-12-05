<?php
	/**
	+----------------------------------------------------------
	* 合并两个数组函数
	+----------------------------------------------------------
	* @access public
	+----------------------------------------------------------
	* @param array $list1 数组名
	* @param array $list2 数组名
	* $username=MergeArray($list1,$list2);
	+----------------------------------------------------------
	* @return array
	+----------------------------------------------------------
	*/
	function MergeArray($list1, $list2){
		if(!empty($list1) && !empty($list2)) 
		{
			return array_merge($list1,$list2);
		}
		else return (empty($list1)?(empty($list2)?null:$list2):$list1);
	}

?>