<?php
/**
 * v1.0.3
 * Copyright (c) 2015 Andre Caetano
 * mQ.php is open sourced under the MIT license.
 * Author Andre Caetano
 */

function connect($db=null){
	require 'config.php';
	$db=(!$db ? $config['DB']['db'] : $db);
	$c = mysql_connect("localhost",$config['DB']['user'],$config['DB']['pass'])or die(mysql_error());
	mysql_select_db($db,$c)or die(mysql_error());
	return $c;
}

function mAr($sql,$t='L'){
	switch ($t) {
		case 1:	return mysql_fetch_array($sql); break;
		case 'N': return mysql_fetch_row($sql); break;
		case 'L': return mysql_fetch_assoc($sql); break;
	}
}

function mQ($sql,$error=false){$x=mysql_query($sql)or die(($error ? mysql_error() : $sql.'_'.mysql_error())); return $x;}
function mNum($sql){return mysql_num_rows($sql);}
function mQN($sql){return mNum(mQ($sql));}
function oV($sql,$utf8=false){ $x=mysql_fetch_row(mQ($sql)); return ($utf8 ? utf8($x[0]) : $x[0]);}
function mRow($sql,$t='L',$utf8=false){
	$resp = [];
	if(!$utf8){
		$resp=mAr(mQ($sql),$t);
	} else {
		$resp=arrToUtf8(mAr(mQ($sql),$t));
	}	
	return $resp;
}
function mRows($sql,$t='L',$utf8=false){
	$sql=mQ($sql,$utf8);
	$resp=[];

	if($utf8){
		while ($data=mAr($sql,$t)) $resp[]=$data;
	} else {
		while ($data=mAr($sql,$t)) $resp[]=arrToUtf8($data);
	}
	return $resp;
}

function treatCols(&$cols){
	foreach ($cols as $i => $v) $cols[$i]=$i.'='.quotes($v);
}

function mIns($table,$cols,$getLast=false){
	mQ('INSERT INTO '.$table.' SET '.implode(', ', $cols));
	if($getLast) return oV('SELECT last_Insert_Id() FROM '.$table);
}

function quotes($str){
	if (is_numeric($str)){
		return intval($str);
	}else{
		return "'".addslashes($str)."'";
	}
}

function mDel($table,$where){
	mQ('DELETE FROM '.$table.' WHERE '.implode(' and ', $where));
}

function _mSel($cols=null,$table=null,$where=null,$group=null,$order=null,$limit=null,$utf8=true){
	$sql=[];
	if($cols) $sql['cols']=$cols;
	if($table) $sql['table']=$table;
	if($where) $sql['where']=$where;
	if($group) $sql['group']=$group;
	if($order) $sql['order']=$order;
	if($limit) $sql['limit']=$limit;

	return mSel($sql,$utf8);
}

function mSel($data=[],$utf8=true){
	$resp=[];

	$where=$data['where'];
	$from=$data['from'];
	$cols=$data['cols'];

	$group=(isset($data['group']) ? $data['group'] : []);
	$order=(isset($data['order']) ? $data['order'] : []);
	$have=(isset($data['have']) ? $data['have'] : []);

	return mRows('SELECT '.(gettype($cols)=='array' ? implode(', ', $cols) : $cols).' '.
	'FROM '.(gettype($from)=='array' ? implode(', ', $from) : $from).' '.
	'WHERE '.(gettype($where)=='array' ? implode(' and ', $where) : $where).' '.
	(count($group)>0 ? "GROUP BY " : '').(gettype($group)=='array' ? implode(", ",$group) : $group).' '.
	(count($have)>0 ? "HAVING " : '').implode(" and ",$have).' '. 
	(count($order)>0 ? 'ORDER BY ' : '').implode(", ",$order).' '.
	(isset($data['limit']) && (gettype($data['limit'])=='string' || (gettype($data['limit'])=='array' && $data['limit'][1]>0)) ? 
		'LIMIT '.(gettype($data['limit'])=='array' ? implode(', ', $data['limit']) : $data['limit']) 
	: '' )
	,'L',$utf8);
}

function mSet($table,$set,$where=''){
	$sql='UPDATE '.(gettype($table)=='array' ? implode(', ', $table) : $table).
	' SET '.(gettype($set)=='array' ? implode(', ', $set) : $set).
	($where=='' ?
		''
		:
		' WHERE '.(gettype($where)=='array' ? implode(' and ', $where) : $where)
	);

	mQ($sql);

	return $sql;
}

function mIU($table,$cols){
	if(!count($cols)) return;
	$cols = array_map('mysql_real_escape_string', $cols); $cv=[];
	foreach ($cols as $i => $val) $cv[]="`".$i."`='".$val."'";
	mQ('INSERT INTO '.$table.' (`'.join('`, `', array_keys($cols)).'`) 
	SELECT "'.join('", "', $cols).'" FROM DUAL 
	WHERE NOT EXISTS (SELECT 1 FROM '.$table.' WHERE '.join(' AND ', $cv).') LIMIT 1');
	return mysql_insert_id();
}

function _json($data){
	return json_encode(arrToUtf8($data));
}

function sql2JSON($sql,$toArray=true,$onlyRow=false){
	$data=mQ($sql);
	$rows=[];
	if(mNum($data)==0) return '';
	if(!$toArray) return _json(mAr($data));

	while($r=mAr($data)){
		if($onlyRow) $r=array_values($r);
		$rows[]=arrToUtf8($r);
	}
	fixTypes($rows);
	return json_encode($rows);
}

function _sql2JSON($sql,$toArray=true,$onlyRow=false){
	echo sql2JSON($sql,$toArray,$onlyRow);
}

function utf8($str){
	return utf8_encode($str);
}

function arrToUtf8($a){
	foreach ($a as $i => $val) $a[$i]=utf8($val);
	return $a;
}
?>