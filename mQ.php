<?php
/**
 * v1.1.0
 * Copyright (c) 2015 Andre Caetano
 * mQ.php is open sourced under the MIT license.
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

function mIns($table,$set,$getLast=false){
	$sql = [
		'INSERT INTO '.countAdd('',strToArr($table),', '),
		'SET '.countAdd('',strToArr($set),', ')
	];

	mQ(implode(" ",$sql));
	if($getLast) return oV('SELECT last_Insert_Id() FROM '.$table);
}


function mDel($table,$where){
	$sql = [
		'DELETE FROM '.countAdd('',strToArr($table),', '),
		countAdd('WHERE',strToArr($where),' and ')
	];
	mQ(implode(" ",$sql));
}

function mSel($data=[],$utf8=true){
	$sql=[];
	$attrs = [
		'cols'=>['head'=>'SELECT','separator'=>', '],
		'from'=>['head'=>'FROM','separator'=>', '],
		'where'=>['head'=>'WHERE','separator'=>' and '],
		'group'=>['head'=>'GROUP BY','separator'=>', '],
		'have'=>['head'=>'HAVING','separator'=>' and '],
		'order'=>['head'=>'ORDER BY','separator'=>', '],
		'limit'=>['head'=>'LIMIT','separator'=>', ']
	];
	foreach ($attrs as $k => $val) if(isset($data[$k])) pushNotEmpty($sql,countAdd($attrs[$k]['head'],strToArr($data[$k]),$attrs[$k]['separator']));
	
	return mRows(implode(" ",$sql),'L',$utf8);
}

function mSet($table,$set,$where=null){	
	$sql = [
		'UPDATE '.countAdd('',strToArr($table),', '),
		'SET '.countAdd('',strToArr($set),', ')
	];
	pushNotEmpty($sql,countAdd('WHERE',strToArr($where),' and '));

	mQ(implode(" ",$sql));
}

function quotes($str){
	if (is_numeric($str)){
		return intval($str);
	}else{
		return "'".addslashes($str)."'";
	}
}


function strToArr($var){
	if(!$var) return [];
	if(!is_array($var)) return [$var];
	return $var;
}

function countAdd($head,$arr,$separator){
	if(!count($arr)) return '';
	return ($head ? $head.' ' : '').implode($arr,$separator);
}

function pushNotEmpty(&$arr,$val){
	if(count($val) && $val) $arr[]=$val;
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