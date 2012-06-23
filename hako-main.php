<?php
/*******************************************************************

	Ȣ����磲 for PHP

	
	$Id$

*******************************************************************/
//ʸ�����󥳡��ɤλ���
mb_language('Japanese');
// for XOOPS
include("../../mainfile.php");
//
//error_reporting(E_ALL);
require 'jcode.phps';
require 'config.php';
require 'hako-html.php';
require 'hako-turn.php';
$init = new Init;

define("READ_LINE", 1024);
$THIS_FILE =	$init->mainFileUrl;
$BACK_TO_TOP = "<A HREF=\"{$THIS_FILE}?\">{$init->tagBig_}�ȥåפ����{$init->_tagBig}</A>";
$ISLAND_TURN; // �������

//--------------------------------------------------------------------
class Hako extends HakoIO {
	var $islandList;	// ��ꥹ��
	var $targetList;	// �������åȤ���ꥹ��
	var $defaultTarget;	// ��ɸ��­�ѥ������å�
	
	function readIslands(&$cgi) {
	global $init;
	
	$m = $this->readIslandsFile($cgi);
	$this->islandList = $this->getIslandList($cgi->dataSet['defaultID']);
	if($init->targetIsland == 1) {
		// ��ɸ���� ��ͭ���礬���򤵤줿�ꥹ��
		$this->targetList = $this->islandList;
	} else {
		// ��̤�TOP���礬���򤵤줿���֤Υꥹ��
		$this->targetList = $this->getIslandList($cgi->dataSet['defaultTarget']);
	}
	return $m;
	}

	//---------------------------------------------------
	// ��ꥹ������
	//---------------------------------------------------
	function getIslandList($select = 0) {
	$list = "";
	for($i = 0; $i < $this->islandNumber; $i++) {
		$name = $this->islands[$i]['name'];
		$id	= $this->islands[$i]['id'];

		// ������ɸ�򤢤餫���Ἣʬ����ˤ���
		if(empty($this->defaultTarget)) {$this->defaultTarget = $id;}

		if($id == $select) {
		$s = "selected";
		} else {
		$s = "";
		}
		$list .= "<option value=\"$id\" $s>${name}��</option>\n";
	}
	return $list;
	}
	//---------------------------------------------------
	// �ޤ˴ؤ���ꥹ�Ȥ�����
	//---------------------------------------------------
	function getPrizeList($prize) {
	global $init;
	list($flags, $monsters, $turns) = split(",", $prize, 3);

	$turns = split(",", $turns);
	$prizeList = "";
	// ��������
	$max = -1;
	$nameList = "";
	if($turns[0] != "") {
		for($k = 0; $k < count($turns) - 1; $k++) {
		$nameList .= "[{$turns[$k]}] ";
		$max = $k;
		}
	}
	if($max != -1) {
		$prizeList .= "<img src=\"{$init->imgDir}/prize0.gif\" alt=\"$nameList\" width=\"16\" height=\"16\" /> ";
	}
	// ��
	$f = 1;
	for($k = 1; $k < count($init->prizeName); $k++) {
		if($flags & $f) {
		$prizeList .= "<img src=\"{$init->imgDir}/prize{$k}.gif\" alt=\"{$init->prizeName[$k]}\" width=\"16\" height=\"16\" /> ";
		}
		$f = $f << 1;
	}
	// �ݤ������åꥹ��
	$f = 1;
	$max = -1;
	$nameList = "";
	for($k = 0; $k < $init->monsterNumber; $k++) {
		if($monsters & $f) {
		$nameList .= "[{$init->monsterName[$k]}] ";
		$max = $k;
		}
		$f = $f << 1;
	}
	if($max != -1) {
		$prizeList .= "<img src=\"{$init->imgDir}/{$init->monsterImage[$max]}\" alt=\"{$nameList}\" width=\"16\" height=\"16\" /> ";
	}
	return $prizeList;
	}
	//------------------------------------------------------------------

	//---------------------------------------------------
	// �Ϸ��˴ؤ���ǡ�������
	//---------------------------------------------------
	function landString($island, $l, $lv, $x, $y, $mode, $comStr) {
	global $init;
	$point = "({$x},{$y})";
	$naviExp = "''";

	if($x < $init->islandSize / 2)
		$naviPos = 0;
	else
		$naviPos = 1;

	switch($l) {
	case $init->landSea:
		switch($lv) {
		case 1:
		// ����
		$image = 'land14.gif';
		$naviTitle = '����';
		break;
		case 2:
		# ����
		$image = 'ship.gif';
		$naviTitle = $init->shipName[0];
		break;
		case 3:
		# ����
		$image = 'fishingboat.gif';
		$naviTitle = $init->shipName[1];
		break;
		case 255:
		# ��±��
		$image = 'viking.gif';
		$naviTitle = '��±��';
		break;
		default:
		// ��
		$image = 'land0.gif';
		$naviTitle = '��';
		}
		break;

	case $init->landPort:
		// ��
		$image = 'port.gif';
		$naviTitle = '��';
		break;
		
	case $init->landSeaSide:
				// ����
				$image = 'sunahama.gif';
				$naviTitle = '����';
				$naviText = "{$lv}{$init->unitPop}����";
				break;

	case $init->landSeaResort:
		# ���β�
		if($lv < 30) {
			$image = 'umi1.gif';
			$naviTitle = '���β�';
		} else if($lv < 100) {
			$image = 'umi2.gif';
			$naviTitle = '̱��';
		} else {
			$image = 'umi3.gif';
			$naviTitle = '�꥾���ȥۥƥ�';
		}
				$nt = Turn::countAroundLevel($island, $x, $y, $init->landTown, 19);//����2�إå����ο͸�
				$ns = Turn::countAroundLevel($island, $x, $y, $init->landSeaSide, 19);//����2�إå����κ��ͼ��ƿͿ�
		$naviText	 = "��:{$lv}{$init->unitPop} <br />";
		$naviText .= "��:{$nt}{$init->unitPop} <br />";
		$naviText .= "��:{$ns}{$init->unitPop} ";
		break;
		
	case $init->landPark:
		# ͷ����
		$image = 'park.gif';
		$naviTitle = 'ͷ����';
		break;
		
	case $init->landWaste:
		// ����
		if($lv == 1) {
		$image = 'land13.gif'; // ������
		} else {
		$image = 'land1.gif';
		}
		$naviTitle = '����';
		break;
	case $init->landPlains:
		// ʿ��
		$image = 'land2.gif';
		$naviTitle = 'ʿ��';
		break;
	case $init->landForest:
		// ��
		if($mode !== 1) {
		$image = 'land6.gif';
		$naviText= "${lv}{$init->unitTree}";
		} else {
		// �Ѹ��Ԥξ����ڤ��ܿ�����
		$image = 'land6.gif';
		}
		$naviTitle = '��';
		break;
	case $init->landTown:
		// Į
		$p; $n;
		if($lv < 30) {
		$p = 3;
		$naviTitle = '¼';
		} else if($lv < 100) {
		$p = 4;
		$naviTitle = 'Į';
		} else {
		$p = 5;
		$naviTitle = '�Ի�';
		}
		$image = "land{$p}.gif";
		$naviText = "{$lv}{$init->unitPop}";
		break;
	case $init->landFarm:
		// ����
		$image = 'land7.gif';
		$naviTitle = '����';
		$naviText = "{$lv}0{$init->unitPop}����";
		break;
	case $init->landFactory:
		// ����
		$image = 'land8.gif';
		$naviTitle = '����';
		$naviText = "{$lv}0{$init->unitPop}����";
		break;
	case $init->landBase:
		if($mode !== 1) {
		// �Ѹ��Ԥξ��Ͽ��Τդ�
		$image = 'land6.gif';
		$naviTitle = '��';
		} else {
		// �ߥ��������
		$level = Util::expToLevel($l, $lv);
		$image = 'land9.gif';
		$naviTitle = '�ߥ��������';
		$naviText = "��٥� ${level} / �и��� {$lv}";
		}
		break;
	case $init->landSbase:
		// �������
		if($mode !== 1) {
		// �Ѹ��Ԥξ��ϳ��Τդ�
		$image = 'land0.gif';
		$naviTitle = '��';
		} else {
		$level = Util::expToLevel($l, $lv);
		$image = 'land12.gif';
		$naviTitle = '�������';
		$naviText = "��٥� ${level} / �и��� {$lv}";
		}
		break;
	case $init->landDefence:
		// �ɱһ���
		$image = 'land10.gif';
		$naviTitle = '�ɱһ���';
		break;
	case $init->landHaribote:
		// �ϥ�ܥ�
		$image = 'land10.gif';
		if($mode !== 1) {
		// �Ѹ��Ԥξ����ɱһ��ߤΤդ�
		$naviTitle = '�ɱһ���';
		} else {
		$naviTitle = '�ϥ�ܥ�';
		}
		break;
	case $init->landOil:
		// ��������
		$image = 'land16.gif';
		$naviTitle = '��������';
		break;
	case $init->landMountain:
		// ��
		if($lv > 0) {
		$image = 'land15.gif';
		$naviTitle = '�η���';
		$naviText = "{$lv}0{$init->unitPop}����";
		} else {
		$image = 'land11.gif';
		$naviTitle = '��';
		}
		break;
	case $init->landMonument:
		// ��ǰ��
		$image = $init->monumentImage[$lv];
		$naviTitle = '��ǰ��';
		$naviText = $init->monumentName[$lv];
		break;
	case $init->landMonster:
		// ����
		$monsSpec = Util::monsterSpec($lv);
		$special = $init->monsterSpecial[$monsSpec['kind']];
		$image = $init->monsterImage[$monsSpec['kind']];
		$naviTitle = '����';

		// �Ų���?
		if((($special == 3) && (($this->islandTurn % 2) == 1)) ||
		 (($special == 4) && (($this->islandTurn % 2) == 0))) {
		// �Ų���
		$image = $init->monsterImage2[$monsSpec['kind']];
		}
		$naviText = "����{$monsSpec['name']}(����{$monsSpec['hp']})";
	}

	if($mode == 1 || $mode == 2) {
		print "<span onclick=\"ps($x,$y)\" style=\"cursor: crosshair;\">";
		$naviText = "{$comStr}{$naviText}";
	} else {
		print "<span style=\"cursor: crosshair;\">";
	}
	print "<img id=\"hakoimg{$point}\" src=\"{$init->imgDir}/{$image}\" width=\"32\" height=\"32\" alt=\"{$point} {$naviTitle} {$comStr}\" onMouseOver=\"Navi('{$image}', '{$naviTitle}', '{$point}', '{$naviText}', {$naviExp}, {$y});\" onMouseOut=\"NaviClose(); return false\" /></span>";

	// ��ɸ�����Ĥ�
	//if($mode == 1)
	//	print "</span>";
	}
}
//--------------------------------------------------------------------
class HakoIO {
	var $islandTurn;	// �������
	var $islandLastTime;	// �ǽ���������
	var $islandNumber;	// ������
	var $islandNextID;	// ���˳�����Ƥ���ID
	var $islands;		// ����ξ�����Ǽ
	var $idToNumber;
	var $idToName;

	//---------------------------------------------------
	// ����ǡ������ɤ߹���
	// 'mode'���Ѥ���ǽ��������Τ�$cgi�򻲾ȤǼ������
	//---------------------------------------------------
	function readIslandsFile(&$cgi) {
	global $init;
	$num = $cgi->dataSet['ISLANDID'];

	$fileName = "{$init->dirName}/{$init->datafileName}";
	if(!is_file($fileName)) {
		return false;
	}
	$fp = fopen($fileName, "r");
	$this->islandTurn		= chop(fgets($fp, READ_LINE));
	$this->islandLastTime = chop(fgets($fp, READ_LINE));
	$this->islandNumber		= chop(fgets($fp, READ_LINE));
	$this->islandNextID		= chop(fgets($fp, READ_LINE));

	$GLOBALS['ISLAND_TURN'] = $this->islandTurn;
		
	// ���������Ƚ��
	$now = time();
	if((DEBUG && (strcmp($cgi->dataSet['mode'], 'debugTurn') == 0)) ||
		 (($now - $this->islandLastTime) >= $init->unitTime)) {
		
		//MEMO �����ǥ�����ʹ���򼨤���å��ե���������(turn.lock)
		//MEMO ��ʬ�ʾ�вᤷ�Ƥ���ե�����ϡ����
		//MEMO ��å��ե����뤬¸�ߤ��Ƥ����饿����������
		$now_turning = false;
		//��å��ե������¸�߳�ǧ
		if (file_exists("turn.lock")){
			//�ե�����Υ����ॹ����׳�ǧ
			$lock_time = filemtime("turn.lock");
			if ($lock_time < time() - 60) {
				//��å��ե����뤬�Ť�";
				//�ե�������
				unlink("turn.lock");
				//�ե��������
				$fno = fopen("turn.lock", 'w');
				fclose($fno);
			} else {
				//���ߥ����������
				$now_turning = true;
			}
		} else {
			//�ե��������
			$fno = fopen("turn.lock", 'w');
			fclose($fno);
		}
		if (!$now_turning){
			if ($cgi->mode == "turn_only"){
				$cgi->mode = $data['mode'] = 'turn_only_now';
			} else {
				$cgi->mode = $data['mode'] = 'turn';
			}
			$num = -1;
		}
	}
	for($i = 0; $i < $this->islandNumber; $i++) {
		$this->islands[$i] = $this->readIsland($fp, $num);
		$this->idToNumber[$this->islands[$i]['id']] = $i;
	}
	fclose($fp);
	return true;
	}
	//---------------------------------------------------
	// ��ҤȤ��ɤ߹���
	//---------------------------------------------------
	function readIsland($fp, $num) {
	global $init;
	$name		= chop(fgets($fp, READ_LINE));
	list($name, $owner, $monster, $email, $tomail, $port, $passenger, $fishingboat ,$viking ) = split(",", $name);
	$id			= chop(fgets($fp, READ_LINE));
	$prize		= chop(fgets($fp, READ_LINE));
	$absent		= chop(fgets($fp, READ_LINE));
	$comment	= chop(fgets($fp, READ_LINE));
	list($comment, $comment_turn) = split(",", $comment);
	$password = chop(fgets($fp, READ_LINE));
	$money		= chop(fgets($fp, READ_LINE));
	$food		= chop(fgets($fp, READ_LINE));
	$pop		= chop(fgets($fp, READ_LINE));
	$area		= chop(fgets($fp, READ_LINE));
	$farm		= chop(fgets($fp, READ_LINE));
	$factory	= chop(fgets($fp, READ_LINE));
	$mountain = chop(fgets($fp, READ_LINE));

	$this->idToName[$id] = $name;
	
	if(($num == -1) || ($num == $id)) {
		$fp_i = fopen("{$init->dirName}/island.{$id}", "r");

		// �Ϸ�
		$offset = 4; // ���ФΥǡ�������ʸ����
		for($y = 0; $y < $init->islandSize; $y++) {
		$line = chop(fgets($fp_i, READ_LINE));
		for($x = 0; $x < $init->islandSize; $x++) {
			$l = substr($line, $x * $offset	 , 2);
			$v = substr($line, $x * $offset + 2, 2);
			$land[$x][$y]		 = hexdec($l);
			$landValue[$x][$y] = hexdec($v);
		}
		}
		
		// ���ޥ��
		for($i = 0; $i < $init->commandMax; $i++) {
		$line = chop(fgets($fp_i, READ_LINE));
		list($kind, $target, $x, $y, $arg) = split(",", $line);
		$command[$i] = array (
			'kind'	 => $kind,
			'target' => $target,
			'x'		 => $x,
			'y'		 => $y,
			'arg'		 => $arg,
			);
		}
		// ������Ǽ���
		for($i = 0; $i < $init->lbbsMax; $i++) {
		$line = chop(fgets($fp_i, READ_LINE));
		$lbbs[$i] = $line;
		}
		fclose($fp_i);
	}
	return array(
		'name'	 => $name,
		'owner'	 => $owner,
		'id'		 => $id,
		'prize'	 => $prize,
		'absent'	 => $absent,
		'comment'	 => $comment,
		'comment_turn' => $comment_turn,
		'password' => $password,
		'money'	 => $money,
		'food'	 => $food,
		'pop'		 => $pop,
		'area'	 => $area,
		'farm'	 => $farm,
		'factory'	 => $factory,
		'mountain' => $mountain,
		'monster'	 => $monster,
		'land'	 => $land,
		'landValue'=> $landValue,
		'command'	 => $command,
		'lbbs'	 => $lbbs,
		'email'	 => $email,
		'tomail'	 => $tomail,
		'port'	 => $port,
		'ship'	 => array('passenger' => $passenger, 'fishingboat' => $fishingboat, 'viking' => $viking)
		);
	}
	//---------------------------------------------------
	// ����ǡ�����񤭹���
	//---------------------------------------------------
	function writeIslandsFile($num = 0) {
	global $init;
	$fileName = "{$init->dirName}/{$init->datafileName}";

	if(!is_file($fileName))
		touch($fileName);

	$fp = fopen($fileName, "w");
	flock($fp, LOCK_EX);
	fputs($fp, $this->islandTurn . "\n");
	fputs($fp, $this->islandLastTime . "\n");
	fputs($fp, $this->islandNumber . "\n");
	fputs($fp, $this->islandNextID . "\n");
	for($i = 0; $i < $this->islandNumber; $i++) {
		$this->writeIsland($fp, $num, $this->islands[$i]);
	}
	flock($fp, LOCK_UN);
	fclose($fp);
//		chmod($fileName, 0666);
	}
	//---------------------------------------------------
	// ��ҤȤĽ񤭹���
	//---------------------------------------------------
	function writeIsland($fp, $num, $island) {
	global $init;
	$ships = $island['ship']['passenger'].",".$island['ship']['fishingboat'].",".$island['ship']['viking'];
	fputs($fp, $island['name'].",".$island['owner'].",".$island['monster'].",".$island['email'].",".$island['tomail'].",".$island['port'].",".$ships."\n");
	fputs($fp, $island['id'] . "\n");
	fputs($fp, $island['prize'] . "\n");
	fputs($fp, $island['absent'] . "\n");
	fputs($fp, $island['comment'] . "," . $island['comment_turn'] . "\n");
	fputs($fp, $island['password'] . "\n");
	fputs($fp, $island['money'] . "\n");
	fputs($fp, $island['food'] . "\n");
	fputs($fp, $island['pop'] . "\n");
	fputs($fp, $island['area'] . "\n");
	fputs($fp, $island['farm'] . "\n");
	fputs($fp, $island['factory'] . "\n");
	fputs($fp, $island['mountain'] . "\n");
	// �Ϸ�
	if(($num <= -1) || ($num == $island['id'])) {
		$fileName = "{$init->dirName}/island.{$island['id']}";

		if(!is_file($fileName))
		touch($fileName);

		$fp_i = fopen($fileName, "w");
		flock($fp_i, LOCK_EX);
		$land = $island['land'];
		$landValue = $island['landValue'];
	
		for($y = 0; $y < $init->islandSize; $y++) {
		for($x = 0; $x < $init->islandSize; $x++) {
			$l = sprintf("%02x%02x", $land[$x][$y], $landValue[$x][$y]);
			fputs($fp_i, $l);
		}
		fputs($fp_i, "\n");
		}

		// ���ޥ��
		$command = $island['command'];
		for($i = 0; $i < $init->commandMax; $i++) {
		$com = sprintf("%d,%d,%d,%d,%d\n",
					 $command[$i]['kind'],
					 $command[$i]['target'],
					 $command[$i]['x'],
					 $command[$i]['y'],
					 $command[$i]['arg']
				);
		fputs($fp_i, $com);
		}

		// ������Ǽ���
		$lbbs = $island['lbbs'];
		for($i = 0; $i < $init->lbbsMax; $i++) {
		fputs($fp_i, $lbbs[$i] . "\n");
		}
		flock($fp_i, LOCK_UN);
		fclose($fp_i);
//		chmod($fileName, 0666);
	}
	}
	//---------------------------------------------------
	// �ǡ����ΥХå����å�
	//---------------------------------------------------
	function backUp() {
	global $init;

	if($init->backupTimes <= 0)
		return;
	
	$tmp = $init->backupTimes - 1;
	$this->rmTree("{$init->dirName}.bak{$tmp}");
	for($i = ($init->backupTimes - 1); $i > 0; $i--) {
		$j = $i - 1;
		if(is_dir("{$init->dirName}.bak{$j}"))
		rename("{$init->dirName}.bak{$j}", "{$init->dirName}.bak{$i}");
	}
	if(is_dir("{$init->dirName}"))
		rename("{$init->dirName}", "{$init->dirName}.bak0");

	mkdir("{$init->dirName}", $init->dirMode);

	// ���ե�����������ԡ�����
	for($i = 0; $i <= $init->logMax; $i++) {
		if(is_file("{$init->dirName}.bak0/hakojima.log{$i}"))
		copy("{$init->dirName}.bak0/hakojima.log{$i}", "{$init->dirName}/hakojima.log{$i}");
	}
	if(is_file("{$init->dirName}.bak0/hakojima.his"))
		copy("{$init->dirName}.bak0/hakojima.his", "{$init->dirName}/hakojima.his");
	}
	//---------------------------------------------------
	// ���פʥǥ��쥯�ȥ�ȥե��������
	//---------------------------------------------------
	function rmTree($dirName) {
	if(is_dir("{$dirName}")) {
		$dir = opendir("{$dirName}/");
		while($fileName = readdir($dir)) {
		if(!(strcmp($fileName, ".") == 0 || strcmp($fileName, "..") == 0))
			unlink("{$dirName}/{$fileName}");
		}
		closedir($dir);
		rmdir($dirName);
	}
	}
}
//--------------------------------------------------------------------
class LogIO {
	var $logPool = array();
	var $secretLogPool = array();
	var $lateLogPool = array();
	
	//---------------------------------------------------
	// ���ե��������ˤ��餹
	//---------------------------------------------------
	function slideBackLogFile() {
	global $init;
	for($i = $init->logMax - 1; $i >= 0; $i--) {
		$j = $i + 1;
		$s = "{$init->dirName}/hakojima.log{$i}";
		$d = "{$init->dirName}/hakojima.log{$j}";
		if(is_file($s)) {
		if(is_file($d))
			 unlink($d);
		rename($s, $d);
		}
	}
	}
	//---------------------------------------------------
	// �Ƕ�ν���������
	//---------------------------------------------------
	function logFilePrint($num = 0, $id = 0, $mode = 0, $island_name = "") {
	global $init;
	$fileName = $init->dirName . "/hakojima.log" . $num;
	if(!is_file($fileName)) {
		return;
	}
	$fp = fopen($fileName, "r");
	
	print "<table class='hako' border=\"0\" cellspacing=\"0\">";
	$bef_turn = 0;
	$isdata = false;
	while($line = chop(fgets($fp, READ_LINE))) {
		list($m, $turn, $id1, $id2, $message) = split(",", $line, 5);
		if($m == 1) {
		if(($mode == 0) || ($id1 != $id)) {
			continue;
		}
		$m = "<strong>(��̩)</strong>";
		} else {
		$m = "";
		}
		if($id != 0) {
		if(($id != $id1) && ($id != $id2)) {
			continue;
		} else {
			if ($island_name){
				$message = preg_replace("/".$island_name."��(\([0-9])/","����$1",$message);
				$message = preg_replace("/".$island_name."/","��",$message);
			}
		}
		}
		if($message) $isdata = true;
		if ($turn != $bef_turn){
		print "<tr><tr><td valign=\"top\"nowrap>{$init->tagNumber_}������{$turn}{$init->_tagNumber}��</td><td>{$init->tagDisaster_}{$m}{$init->_tagDisaster}{$message}</td></tr>\n";
		} else {
		print "<tr><td></td><td>{$message}</td></tr>\n";
		}
		$bef_turn=$turn;
	}
	print "</table>";
	if ($isdata) print "<hr width=\"80%\">";
	fclose($fp);
	}
	//---------------------------------------------------
	// ȯ���ε�Ͽ�����
	//---------------------------------------------------
	function historyPrint() {
	global $init;
	$fileName = $init->dirName . "/hakojima.his";
	if(!is_file($fileName)) {
		return;
	}
	$fp = fopen($fileName, "r");
	$history = array();
	$k = 0;
	while($line = chop(fgets($fp, READ_LINE))) {
		array_push($history, $line);
		$k++;
	}
	print "<table class='hako' border=\"0\" cellspacing=\"0\">";
	$bef_turn = 0;
	for($i = 0; $i < $k; $i++) {
		list($turn, $his) = split(",", array_pop($history), 2);
		if ($turn != $bef_turn){
			print "<tr><td valign=\"top\"nowrap>{$init->tagNumber_}������{$turn}{$init->_tagNumber}��</td><td>$his</td></tr>\n";
		} else {
			print "<tr><td></td><td>$his</td></tr>\n";
		}
		$bef_turn=$turn;
	}
	print "</table>";
	}
	//---------------------------------------------------
	// ȯ���ε�Ͽ����¸
	//---------------------------------------------------
	function history($str) {
	global $init;
	$fileName = "{$init->dirName}/hakojima.his";

	if(!is_file($fileName))
		touch($fileName);

	$fp = fopen($fileName, "a");
	flock($fp, LOCK_EX);
	fputs($fp, "{$GLOBALS['ISLAND_TURN']},{$str}\n");
	fclose($fp);
//		chmod($fileName, 0666);
	
	}
	//---------------------------------------------------
	// ȯ���ε�Ͽ��Ĵ��
	//---------------------------------------------------
	function historyTrim() {
	global $init;
	$fileName = "{$init->dirName}/hakojima.his";
	if(is_file($fileName)) {
		$fp = fopen($fileName, "r");

		$line = array();
		while($l = chop(fgets($fp, READ_LINE))) {
		array_push($line, $l);
		$count++;
		}
		fclose($fp);
		if($count > $init->historyMax) {

		if(!is_file($fileName))
			touch($fileName);

		$fp = fopen($fileName, "w");
		flock($fp, LOCK_EX);
		for($i = ($count - $init->historyMax); $i < $count; $i++) {
			fputs($fp, "{$line[$i]}\n");
		}
		fclose($fp);
//			chmod($fileName, 0666);
		}
	}
	}
	//---------------------------------------------------
	// ��
	//---------------------------------------------------
	function out($str, $id = "", $tid = "") {
	array_push($this->logPool, "0,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}");
	}
	//---------------------------------------------------
	// ��̩��
	//---------------------------------------------------
	function secret($str, $id = "", $tid = "") {
	array_push($this->secretLogPool,"1,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}");
	}
	//---------------------------------------------------
	// �ٱ��
	//---------------------------------------------------
	function late($str, $id = "", $tid = "") {
	array_push($this->lateLogPool,"0,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}");
	}
	//---------------------------------------------------
	// ���񤭽Ф�
	//---------------------------------------------------
	function flush() {
	global $init;
	
	$fileName = "{$init->dirName}/hakojima.log0";

	if(!is_file($fileName))
		touch($fileName);
		
	$fp = fopen($fileName, "w");
	flock($fp, LOCK_EX);

	// �����ս�ˤ��ƽ񤭽Ф�<- ����ǵ�Ͽ���ѹ� nao-pon
	if(!empty($this->secretLogPool)) {
		//for($i = count($this->secretLogPool) - 1; $i >= 0; $i--) {
		for($i = 0; $i < count($this->secretLogPool); $i++) {
		fputs($fp, "{$this->secretLogPool[$i]}\n");
		}
	}
	if(!empty($this->lateLogPool)) {
		//for($i = count($this->lateLogPool) - 1; $i >= 0; $i--) {
		for($i = 0; $i < count($this->lateLogPool); $i++) {
		fputs($fp, "{$this->lateLogPool[$i]}\n");
		}
	}
	if(!empty($this->logPool)) {
		//for($i = count($this->logPool) - 1; $i >= 0; $i--) {
		for($i = 0; $i < count($this->logPool); $i++) {
		fputs($fp, "{$this->logPool[$i]}\n");
		}
	}
	fclose($fp);
//		chmod($fileName, 0666);
	}
	//---------------------------------------------------
	// �᡼������
	//---------------------------------------------------
	function SendMail($emails){
		global $init,$xoopsConfig;
		$email="";
		if ((!empty($this->lateLogPool)) || (!empty($this->logPool))){
			if ($emails) $email = implode(",", $emails);
			$to = $xoopsConfig['adminmail'];
			$subject = strip_tags($xoopsConfig['sitename'])."-".$init->title."-�ǿ��˥塼��";
			$mailBody = strip_tags($xoopsConfig['sitename'])." ������ ".$init->title." �κǿ��˥塼�����Τ餻���ޤ���\n\n";
			$mailBody .= "-----------------------------------------------------------------------------\n";
			if(!empty($this->lateLogPool)) {
				for($i = 0; $i < count($this->lateLogPool); $i++) {
				$log_text = explode(",",$this->lateLogPool[$i],5);
				$mailBody .= "������:".$log_text[1]." - ".strip_tags($log_text[4])."\n";
				}
			}
			if(!empty($this->logPool)) {
				for($i = 0; $i < count($this->logPool); $i++) {
				$log_text = explode(",",$this->logPool[$i],5);
				$mailBody .= "������:".$log_text[1]." - ".strip_tags($log_text[4])."\n";
				}
			}
			$mailBody .= "-----------------------------------------------------------------------------\n\n";
			$mailBody .= "���ʾ�ǿ��˥塼�����Τ餻���ޤ�����\n\n";
			$mailBody .= strip_tags($xoopsConfig['sitename'])."-".$init->title."\n(URL)".$init->mainFileUrl;
			
			//echo $mailBody;
			/* �ɲåإå� */
			$headers .= "From: ".Util::make_header($xoopsConfig['sitename'])." <".$to.">\n";
			if ($email) $headers .= "Bcc: $email\n";

			/* �����ǥ᡼����������� */
			@mb_language("ja");
			if (strlen(ini_get("safe_mode"))< 1) {
				$old_from = ini_get("sendmail_from");

				ini_set("sendmail_from", $xoopsConfig['adminmail']);
				$params = sprintf("-oi -f %s", $xoopsConfig['adminmail']);

				@mb_send_mail($to, $subject, $mailBody, $headers, $params);
				
				ini_set("sendmail_from", $old_from);
			} else {
				@mb_send_mail($to, $subject, $mailBody, $headers);
			}
		}

	}
	
}

//--------------------------------------------------------------------
class Util {
	//---------------------------------------------------
	// ����ɽ��
	//---------------------------------------------------
	function aboutMoney($money = 0) {
	global $init;
	if($init->moneyMode) {
		if($money < 500) {
		return "����500{$init->unitMoney}̤��";
		} else {
		return "����" . round($money / 1000) . "000" . $init->unitMoney;
		}
	} else {
		return $money . $init->unitMoney;
	}
	}
	//---------------------------------------------------
	// �и��Ϥ���ߥ�������ϥ�٥�򻻽�
	//---------------------------------------------------
	function expToLevel($kind, $exp) {
	global $init;
	if($kind == $init->landBase) {
		// �ߥ��������
		for($i = $init->maxBaseLevel; $i > 1; $i--) {
		if($exp >= $init->baseLevelUp[$i - 2]) {
			return $i;
		}
		}
		return 1;
	} else {
		// �������
		for($i = $init->maxSBaseLevel; $i > 1; $i--) {
		if($exp >= $init->sBaseLevelUp[$i - 2]) {
			return $i;
		}
		}
		return 1;
	}
	}
	//---------------------------------------------------
	// ���äμ��ࡦ̾�������Ϥ򻻽�
	//---------------------------------------------------
	function monsterSpec($lv) {
	global $init;
	// ����
	$kind = (int)($lv / 10);
	// ̾��
	$name = $init->monsterName[$kind];
	// ����
	$hp = $lv - ($kind * 10);
	return array ( 'kind' => $kind, 'name' => $name, 'hp' => $hp );
	}
	//---------------------------------------------------
	// ���̾�������ֹ�򻻽�
	//---------------------------------------------------
	function	nameToNumber($hako, $name) {
	// ���礫��õ��
	for($i = 0; $i < $hako->islandNumber; $i++) {
		if(strcmp($name, "{$hako->islands[$i]['name']}") == 0) {
		return $i;
		}
	}
	// ���Ĥ���ʤ��ä����
	return -1;
	}
	//---------------------------------------------------
	// �����ʡ�Email�����ֹ�򻻽�
	//---------------------------------------------------
	function	emailToNumber($hako, $email) {
	// ���礫��õ��
	for($i = 0; $i < $hako->islandNumber; $i++) {
		if(strcmp($email, "{$hako->islands[$i]['email']}") == 0) {
		return $i;
		}
	}
	// ���Ĥ���ʤ��ä����
	return -1;
	}
	//---------------------------------------------------
	// �ѥ���ɥ����å�
	//---------------------------------------------------
	function checkPassword($p1 = "", $p2 = "") {
	global $init,$xoopsUser;

	// null�����å�
	if(empty($p2))
		return false;
	// XOOPS�����Ԥ�True���֤�
	if ( $xoopsUser ) {
		$xoopsModule = XoopsModule::getByDirname("hako");
		if ($xoopsUser->isAdmin($xoopsModule->mid())) { 
			return true;
		}
	}
	// �ޥ������ѥ���ɥ����å�
	//if(strcmp($init->masterPassword, $p2) == 0)
		//return true;

	if(strcmp($p1, Util::encode($p2)) == 0)
		return true;
	
	return false;
	}
	//---------------------------------------------------
	// �ѥ���ɤΥ��󥳡���
	//---------------------------------------------------
	function encode($s) {
	global $init;
	if($init->cryptOn) {
		return crypt($s, 'h2');
	} else {
		return $s;
	}
	}
	//---------------------------------------------------
	// 0 0 num -1 ���������
	//---------------------------------------------------
	function random($num = 0) {
	if($num <= 1) return 0;
	// �ޥ������ä�ɽ�������֤ˤ�ꥷ����
	list($usec, $sec) = explode(' ', microtime());
	mt_srand((float) $sec + ((float) $usec * 100000));
	return mt_rand(0, $num - 1);
	}
	//---------------------------------------------------
	// ������Ǽ��ĤΥ�å������������ˤ��餹
	//---------------------------------------------------
	function slideBackLbbsMessage(&$lbbs, $num) {
	global $init;
	array_splice($lbbs, $num, 1);
	$lbbs[$init->lbbsMax - 1] = '0>>';
	}
	//---------------------------------------------------
	// ������Ǽ��ĤΥ�å��������ĸ��ˤ��餹
	//---------------------------------------------------
	function slideLbbsMessage(&$lbbs) {
	array_pop($lbbs);
	array_unshift($lbbs, $lbbs[0]);
	}
	//---------------------------------------------------
	// ������ʺ�ɸ������
	//---------------------------------------------------
	function makeRandomPointArray() {
	global $init;
	$rx = $ry = array();
	for($i = 0; $i < $init->islandSize; $i++)
		for($j = 0; $j < $init->islandSize; $j++)
		$rx[$i * $init->islandSize + $j] = $j;

	for($i = 0; $i < $init->islandSize; $i++)
		for($j = 0; $j < $init->islandSize; $j++)
		$ry[$j * $init->islandSize + $i] = $j;
	

	for($i = $init->pointNumber; --$i;) {
		$j = Util::random($i + 1);
		if($i != $j) {
		$tmp = $rx[$i];
		$rx[$i] = $rx[$j];
		$rx[$j] = $tmp;
			
		$tmp = $ry[$i];
		$ry[$i] = $ry[$j];
		$ry[$j] = $tmp;
		}
	}
	return array($rx, $ry);
	}
	//---------------------------------------------------
	// ���������ν��������
	//---------------------------------------------------
	function randomArray($n = 1) {
	// �����
	for($i = 0; $i < $n; $i++) {
		$list[$i] = $i;
	}

	// ����åե�
	for($i = 0; $i < $n; $i++) {
		$j = Util::random($n - 1);
		if($i != $j) {
		$tmp = $list[$i];
		$list[$i] = $list[$j];
		$list[$j] = $tmp;
		}
	}
	return $list;
	}
	//---------------------------------------------------
	// ���ޥ�ɤ����ˤ��餹
	//---------------------------------------------------
	function slideFront(&$command, $number = 0) {
	global $init;
	// ���줾�줺�餹
	array_splice($command, $number, 1);

	// �Ǹ�˻�ⷫ��
	$command[$init->commandMax - 1] = array (
		'kind'	 => $init->comDoNothing,
		'target' => 0,
		'x'		 => 0,
		'y'		 => 0,
		'arg'		 => 0
		);
	}
	//---------------------------------------------------
	// ���ޥ�ɤ��ˤ��餹
	//---------------------------------------------------
	function slideBack(&$command, $number = 0) {
	global $init;
	// ���줾�줺�餹
	if($number == count($command) - 1)
		return;

	for($i = $init->commandMax - 1; $i >= $number; $i--) {
		$command[$i] = $command[$i - 1];
	}
	}

	function euc_convert($arg) {
	// ʸ�������ɤ�EUC-JP���Ѵ������֤�
	// ʸ�����ʸ�������ɤ�Ƚ��
	$code = i18n_discover_encoding("$arg");
	// ��EUC-JP�ξ��Τ�EUC-JP���Ѵ�
	if ( $code != "EUC-JP" ) {
		$arg = i18n_convert("$arg","EUC-JP");
	}
	return $arg;
	}

	function sjis_convert($arg) {
	// ʸ�������ɤ�SHIFT_JIS���Ѵ������֤�
	// ʸ�����ʸ�������ɤ�Ƚ��
	$code = i18n_discover_encoding("$arg");
	// ��SHIFT_JIS�ξ��Τ�SHIFT_JIS���Ѵ�
	if ( $code != "SJIS" ) {
		$arg = i18n_convert("$arg","SJIS");
	}
	return $arg;
	}
	
	//---------------------------------------------------
	// ���ʤΤ��Υ����å�
	//---------------------------------------------------
	function checkShip($kind,$lv) {
		global $init;
		$shiplev = $init->shipKind + 2;
		if(($kind == $init->landSea) && ((($lv > 1) && ($lv < $shiplev)) || ($lv == 255))){
		return true;
		}
		return false; # ���ʳ�
	}

	/////////////////////////////////////////////////
	// �᡼��������
	/////////////////////////////////////////////////
	function make_header($str){
		$str = mb_convert_encoding($str, "JIS", "auto");
		$str = '=?ISO-2022-JP?B?'.base64_encode($str).'?=';
		return $str;
	}
}
class Cgi {
	var $mode = "";
	var $dataSet = array();
	//---------------------------------------------------
	// POST��GET�Υǡ��������
	//---------------------------------------------------
	function parseInputData() {
	global $xoopsUser;
	$this->mode = $_POST['mode'];
	if(!empty($_POST)) {

		// Xoops Protector �к�
		$toupper = array("islandname","developemode","ownername","email","islandid","skin","lbbsname","lbbsmessage","lbbsname","lbbsmessage","password","number","command","pointx","amount","targetid","commandmode","message","comary","from_js","tomail");

		foreach($_POST as $name=>$value) {
//			$value = Util::sjis_convert($value);
		// Ⱦ�ѥ��ʤ���������Ѥ��Ѵ������֤�
//			$value = i18n_ja_jp_hantozen($value,"KHV");
			$value = str_replace(",", "", $value);
			$value = JcodeConvert($value, 0, 1);

			// Xoops Protector �к�
			if (in_array($name,$toupper)) $name = strtoupper($name);

			if($init->stripslashes == true) {
			$this->dataSet["{$name}"] = stripslashes($value);
			} else {
			$this->dataSet["{$name}"] = $value;
			}
			
		}
	}
	if(!empty($_GET['Sight'])) {
		$this->mode = "print";
		$this->dataSet['ISLANDID'] = $_GET['Sight'];
	}
	if(!empty($_GET['target'])) {
		$this->mode = "targetView";
		$this->dataSet['ISLANDID'] = $_GET['target'];
	}
	if($_GET['mode'] == "conf") {
		$this->mode = "conf";
	}
	if($_GET['mode'] == "turn_only") {
		$this->mode = "turn_only";
	}
	if (function_exists('mb_strcut')){
		$this->dataSet["ISLANDNAME"] = mb_strcut ($this->dataSet["ISLANDNAME"], 0, 32);
		$this->dataSet["MESSAGE"] = mb_strcut($this->dataSet["MESSAGE"], 0, 80);
		$this->dataSet["LBBSMESSAGE"] = mb_strcut($this->dataSet["LBBSMESSAGE"], 0, 80);
	}
	if ($xoopsUser){
		// XOOPS�桼����̾��ѥ���ɤ˥��å�
		$this->dataSet["PASSWORD"] = $xoopsUser->uname();
		$this->dataSet["PASSWORD2"] = $xoopsUser->uname();
		$this->dataSet["OLDPASS"] = $xoopsUser->uname();
	}
	}
	function lastModified() {
	global $init;

	// Last Modified�إå������
/*
	if($this->mode == "Sight") {
		$fileName = "{$init->dirName}/island.{$this->dataSet['ISLANDID']}";
	} else {
		$fileName = "{$init->dirName}/{$init->datafileName}";
	}
*/
	$fileName = "{$init->dirName}/{$init->datafileName}";
	$time_stamp = filemtime($fileName);
	$time = gmdate("D, d M Y G:i:s", $time_stamp);
	//nao-pon
	//header ("Last-Modified: $time GMT");
	$this->modifiedSinces($time_stamp);
	}
	function modifiedSinces($time) {
/* nao-pon
	$modsince = $_SERVER{'HTTP_IF_MODIFIED_SINCE'};

	$ms = gmdate("D, d M Y G:i:s", $time) . " GMT";
	if($modsince == $ms)
		// RFC 822
		header ("HTTP/1.1 304 Not Modified\n");

	$ms = gmdate("l, d-M-y G:i:s", $time) . " GMT";
	if($modsince == $ms)
		// RFC 850
		header ("HTTP/1.1 304 Not Modified\n");

	$ms = gmdate("D M j G:i:s Y", $time);
	if($modsince == $ms)
		// ANSI C's asctime() format
		header ("HTTP/1.1 304 Not Modified\n");
*/
	}
	//---------------------------------------------------
	// COOKIE�����
	//---------------------------------------------------
	function getCookies() {
	if(!empty($_COOKIE)) {
		foreach($_COOKIE as $name => $value) {
		switch($name) {
		case "OWNISLANDID":
			$this->dataSet['defaultID'] = $value;
			break;
		case "OWNISLANDPASSWORD":
			$this->dataSet['defaultPassword'] = $value;
			break;
		case "TARGETISLANDID":
			$this->dataSet['defaultTarget'] = $value;
			break;
		case "LBBSNAME":
			$this->dataSet['defaultName'] = $value;
			break;
		case "POINTX":
			$this->dataSet['defaultX'] = $value;
			break;
		case "POINTY":
			$this->dataSet['defaultY'] = $value;
			break;
		case "COMMAND":
			$this->dataSet['defaultKind'] = $value;
			break;
		case "DEVELOPEMODE":
			$this->dataSet['defaultDevelopeMode'] = $value;
			break;
		case "SKIN":
			$this->dataSet['defaultSkin'] = $value;
			break;
		}
		}
	}
	//�ѥ���ɤ�xoops�桼����̾�򥻥å�
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "";
	}
	// ---- //
	$this->dataSet['defaultPassword'] = $name;
	}
	//---------------------------------------------------
	// COOKIE������
	//---------------------------------------------------
	function setCookies() {

	$time = time() + 30 * 86400; // ���� + 30��ͭ��

	// Cookie������ & POST�����Ϥ��줿�ǡ����ǡ�Cookie������������ǡ����򹹿�
	if($this->dataSet['ISLANDID'] && $this->mode == "owner") {
		setcookie("OWNISLANDID",$this->dataSet['ISLANDID'], $time);
		$this->dataSet['defaultID'] = $this->dataSet['ISLANDID'];
	}
	if($this->dataSet['PASSWORD']) {
		setcookie("OWNISLANDPASSWORD",$this->dataSet['PASSWORD'], $time);
		$this->dataSet['defaultPassword'] = $this->dataSet['PASSWORD'];
	}
	if($this->dataSet['TARGETID']) {
		setcookie("TARGETISLANDID",$this->dataSet['TARGETID'], $time);
		$this->dataSet['defaultTarget'] = $this->dataSet['TARGETID'];
	}
	if($this->dataSet['LBBSNAME']) {
		setcookie("lBBSNAME",$this->dataSet['LBBSNAME'], $time);
		$this->dataSet['defaultName'] = $this->dataSet['LBBSNAME'];
	}
	if($this->dataSet['POINTX']) {
		setcookie("POINTX",$this->dataSet['POINTX'], $time);
		$this->dataSet['defaultX'] = $this->dataSet['POINTX'];
	}
	if($this->dataSet['POINTY']) {
		setcookie("POINTY",$this->dataSet['POINTY'], $time);
		$this->dataSet['defaultY'] = $this->dataSet['POINTY'];
	}
	if($this->dataSet['COMMAND']) {
		setcookie("COMMAND",$this->dataSet['COMMAND'], $time);
		$this->dataSet['defaultKind'] = $this->dataSet['COMMAND'];
	}
	if($this->dataSet['DEVELOPEMODE']) {
		setcookie("DEVELOPEMODE",$this->dataSet['DEVELOPEMODE'], $time);
		$this->dataSet['defaultDevelopeMode'] = $this->dataSet['DEVELOPEMODE'];
	}
	if($this->dataSet['SKIN']) {
		setcookie("SKIN",$this->dataSet['SKIN'], $time);
		$this->dataSet['defaultSkin'] = $this->dataSet['SKIN'];
	}
	}
}


//--------------------------------------------------------------------
class Main {

	function execute() {
	$hako = new Hako;
	$cgi = new Cgi;
	
	$cgi->parseInputData();
	$cgi->getCookies();
	if(!$hako->readIslands($cgi)) {
		HTML::header($cgi->dataSet);
		Error::noDataFile();
		HTML::footer();
		exit();
	}
	$cgi->setCookies();
	$cgi->lastModified();

	if($cgi->dataSet['DEVELOPEMODE'] == "java") {
		$html = new HtmlJS;
		$com = new MakeJS;
	} else {
		$html = new HtmlMap;
		$com = new Make;
	}
	//echo $cgi->mode;
	switch($cgi->mode) {
	case "turn_only";
		$html = new HtmlTop;
		Error::noturndMessage();
		break;
	case "turn_only_now";
		//$cgi->mode = "turn";
		$turn = new Turn;
		$html = new HtmlTop;
		$turn->turnMain($hako, $cgi->dataSet); 
		//��å��ե�������
		unlink("turn.lock");
		Error::turndMessage();
		break;
	case "turn":
		$turn = new Turn;
		$html = new HtmlTop;
		$html->header($cgi->dataSet);
		$turn->turnMain($hako, $cgi->dataSet);
		//��å��ե�������
		unlink("turn.lock");
		$html->main($hako, $cgi->dataSet); // ����������塢TOP�ڡ���open
		$html->footer();
		break;
	case "owner":
		$html->header($cgi->dataSet);
		$html->owner($hako, $cgi->dataSet);
		$html->footer();
		break;
	case "command":
		$html->header($cgi->dataSet);
		$com->commandMain($hako, $cgi->dataSet);
		$html->footer();
		break;
		
	case "new":
		$html->header($cgi->dataSet);
		$com->newIsland($hako, $cgi->dataSet);
		$html->footer();
		break;
	case "comment":
		$html->header($cgi->dataSet);
		$com->commentMain($hako, $cgi->dataSet);
		$html->footer();
		break;
		
	case "print":
		$html->header($cgi->dataSet);
		$html->visitor($hako, $cgi->dataSet);
		$html->footer();
		break;
	case "targetView":
		$html->header($cgi->dataSet, 'targetView');
		$html->printTarget($hako, $cgi->dataSet);
		$html->footer();
		break;
	case "change":
		$html->header($cgi->dataSet);
		$com->changeMain($hako, $cgi->dataSet);
		$html->footer();
		break;
	case "ChangeOwnerName":
		$html->header($cgi->dataSet);
		$com->changeOwnerName($hako, $cgi->dataSet);
		$html->footer();
		break;
	case "lbbs":
		$lbbs = new Make;
		$html->header($cgi->dataSet);
		$lbbs->localBbsMain($hako, $cgi->dataSet);
		$html->footer();
		break;
		
	case "skin":
		$html = new HtmlSetted;
		$html->header($cgi->dataSet);
		$html->setSkin();
		$html->footer();
		break;
	case "conf":
		$html = new HtmlTop;
		$html->header($cgi->dataSet);
		$html->regist($hako);
		$html->footer();
		break;
		
	default: 
		$html = new HtmlTop;
		$html->header($cgi->dataSet);
		$html->main($hako, $cgi->dataSet);
		$html->footer();
	}
	//exit();
	}
}
if ($_GET['mode'] != "turn_only" && $_GET['mode'] != "targetView"){
	include(XOOPS_ROOT_PATH."/header.php");
	$this_on_xoops = true;
}
$start = new Main;
$start->execute();
if ($this_on_xoops) include(XOOPS_ROOT_PATH."/footer.php");
?>