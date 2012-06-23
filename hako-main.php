<?php
/*******************************************************************

	箱庭諸島２ for PHP

	
	$Id$

*******************************************************************/
//文字エンコードの指定
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
$BACK_TO_TOP = "<A HREF=\"{$THIS_FILE}?\">{$init->tagBig_}トップへ戻る{$init->_tagBig}</A>";
$ISLAND_TURN; // ターン数

//--------------------------------------------------------------------
class Hako extends HakoIO {
	var $islandList;	// 島リスト
	var $targetList;	// ターゲットの島リスト
	var $defaultTarget;	// 目標補足用ターゲット
	
	function readIslands(&$cgi) {
	global $init;
	
	$m = $this->readIslandsFile($cgi);
	$this->islandList = $this->getIslandList($cgi->dataSet['defaultID']);
	if($init->targetIsland == 1) {
		// 目標の島 所有の島が選択されたリスト
		$this->targetList = $this->islandList;
	} else {
		// 順位がTOPの島が選択された状態のリスト
		$this->targetList = $this->getIslandList($cgi->dataSet['defaultTarget']);
	}
	return $m;
	}

	//---------------------------------------------------
	// 島リスト生成
	//---------------------------------------------------
	function getIslandList($select = 0) {
	$list = "";
	for($i = 0; $i < $this->islandNumber; $i++) {
		$name = $this->islands[$i]['name'];
		$id	= $this->islands[$i]['id'];

		// 攻撃目標をあらかじめ自分の島にする
		if(empty($this->defaultTarget)) {$this->defaultTarget = $id;}

		if($id == $select) {
		$s = "selected";
		} else {
		$s = "";
		}
		$list .= "<option value=\"$id\" $s>${name}島</option>\n";
	}
	return $list;
	}
	//---------------------------------------------------
	// 賞に関するリストを生成
	//---------------------------------------------------
	function getPrizeList($prize) {
	global $init;
	list($flags, $monsters, $turns) = split(",", $prize, 3);

	$turns = split(",", $turns);
	$prizeList = "";
	// ターン杯
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
	// 賞
	$f = 1;
	for($k = 1; $k < count($init->prizeName); $k++) {
		if($flags & $f) {
		$prizeList .= "<img src=\"{$init->imgDir}/prize{$k}.gif\" alt=\"{$init->prizeName[$k]}\" width=\"16\" height=\"16\" /> ";
		}
		$f = $f << 1;
	}
	// 倒した怪獣リスト
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
	// 地形に関するデータ生成
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
		// 浅瀬
		$image = 'land14.gif';
		$naviTitle = '浅瀬';
		break;
		case 2:
		# 客船
		$image = 'ship.gif';
		$naviTitle = $init->shipName[0];
		break;
		case 3:
		# 漁船
		$image = 'fishingboat.gif';
		$naviTitle = $init->shipName[1];
		break;
		case 255:
		# 海賊船
		$image = 'viking.gif';
		$naviTitle = '海賊船';
		break;
		default:
		// 海
		$image = 'land0.gif';
		$naviTitle = '海';
		}
		break;

	case $init->landPort:
		// 港
		$image = 'port.gif';
		$naviTitle = '港';
		break;
		
	case $init->landSeaSide:
				// 海岸
				$image = 'sunahama.gif';
				$naviTitle = '砂浜';
				$naviText = "{$lv}{$init->unitPop}規模";
				break;

	case $init->landSeaResort:
		# 海の家
		if($lv < 30) {
			$image = 'umi1.gif';
			$naviTitle = '海の家';
		} else if($lv < 100) {
			$image = 'umi2.gif';
			$naviTitle = '民宿';
		} else {
			$image = 'umi3.gif';
			$naviTitle = 'リゾートホテル';
		}
				$nt = Turn::countAroundLevel($island, $x, $y, $init->landTown, 19);//周囲2ヘックスの人口
				$ns = Turn::countAroundLevel($island, $x, $y, $init->landSeaSide, 19);//周囲2ヘックスの砂浜収容人数
		$naviText	 = "収:{$lv}{$init->unitPop} <br />";
		$naviText .= "人:{$nt}{$init->unitPop} <br />";
		$naviText .= "浜:{$ns}{$init->unitPop} ";
		break;
		
	case $init->landPark:
		# 遊園地
		$image = 'park.gif';
		$naviTitle = '遊園地';
		break;
		
	case $init->landWaste:
		// 荒地
		if($lv == 1) {
		$image = 'land13.gif'; // 着弾点
		} else {
		$image = 'land1.gif';
		}
		$naviTitle = '荒地';
		break;
	case $init->landPlains:
		// 平地
		$image = 'land2.gif';
		$naviTitle = '平地';
		break;
	case $init->landForest:
		// 森
		if($mode !== 1) {
		$image = 'land6.gif';
		$naviText= "${lv}{$init->unitTree}";
		} else {
		// 観光者の場合は木の本数隠す
		$image = 'land6.gif';
		}
		$naviTitle = '森';
		break;
	case $init->landTown:
		// 町
		$p; $n;
		if($lv < 30) {
		$p = 3;
		$naviTitle = '村';
		} else if($lv < 100) {
		$p = 4;
		$naviTitle = '町';
		} else {
		$p = 5;
		$naviTitle = '都市';
		}
		$image = "land{$p}.gif";
		$naviText = "{$lv}{$init->unitPop}";
		break;
	case $init->landFarm:
		// 農場
		$image = 'land7.gif';
		$naviTitle = '農場';
		$naviText = "{$lv}0{$init->unitPop}規模";
		break;
	case $init->landFactory:
		// 工場
		$image = 'land8.gif';
		$naviTitle = '工場';
		$naviText = "{$lv}0{$init->unitPop}規模";
		break;
	case $init->landBase:
		if($mode !== 1) {
		// 観光者の場合は森のふり
		$image = 'land6.gif';
		$naviTitle = '森';
		} else {
		// ミサイル基地
		$level = Util::expToLevel($l, $lv);
		$image = 'land9.gif';
		$naviTitle = 'ミサイル基地';
		$naviText = "レベル ${level} / 経験値 {$lv}";
		}
		break;
	case $init->landSbase:
		// 海底基地
		if($mode !== 1) {
		// 観光者の場合は海のふり
		$image = 'land0.gif';
		$naviTitle = '海';
		} else {
		$level = Util::expToLevel($l, $lv);
		$image = 'land12.gif';
		$naviTitle = '海底基地';
		$naviText = "レベル ${level} / 経験値 {$lv}";
		}
		break;
	case $init->landDefence:
		// 防衛施設
		$image = 'land10.gif';
		$naviTitle = '防衛施設';
		break;
	case $init->landHaribote:
		// ハリボテ
		$image = 'land10.gif';
		if($mode !== 1) {
		// 観光者の場合は防衛施設のふり
		$naviTitle = '防衛施設';
		} else {
		$naviTitle = 'ハリボテ';
		}
		break;
	case $init->landOil:
		// 海底油田
		$image = 'land16.gif';
		$naviTitle = '海底油田';
		break;
	case $init->landMountain:
		// 山
		if($lv > 0) {
		$image = 'land15.gif';
		$naviTitle = '採掘場';
		$naviText = "{$lv}0{$init->unitPop}規模";
		} else {
		$image = 'land11.gif';
		$naviTitle = '山';
		}
		break;
	case $init->landMonument:
		// 記念碑
		$image = $init->monumentImage[$lv];
		$naviTitle = '記念碑';
		$naviText = $init->monumentName[$lv];
		break;
	case $init->landMonster:
		// 怪獣
		$monsSpec = Util::monsterSpec($lv);
		$special = $init->monsterSpecial[$monsSpec['kind']];
		$image = $init->monsterImage[$monsSpec['kind']];
		$naviTitle = '怪獣';

		// 硬化中?
		if((($special == 3) && (($this->islandTurn % 2) == 1)) ||
		 (($special == 4) && (($this->islandTurn % 2) == 0))) {
		// 硬化中
		$image = $init->monsterImage2[$monsSpec['kind']];
		}
		$naviText = "怪獣{$monsSpec['name']}(体力{$monsSpec['hp']})";
	}

	if($mode == 1 || $mode == 2) {
		print "<span onclick=\"ps($x,$y)\" style=\"cursor: crosshair;\">";
		$naviText = "{$comStr}{$naviText}";
	} else {
		print "<span style=\"cursor: crosshair;\">";
	}
	print "<img id=\"hakoimg{$point}\" src=\"{$init->imgDir}/{$image}\" width=\"32\" height=\"32\" alt=\"{$point} {$naviTitle} {$comStr}\" onMouseOver=\"Navi('{$image}', '{$naviTitle}', '{$point}', '{$naviText}', {$naviExp}, {$y});\" onMouseOut=\"NaviClose(); return false\" /></span>";

	// 座標設定閉じ
	//if($mode == 1)
	//	print "</span>";
	}
}
//--------------------------------------------------------------------
class HakoIO {
	var $islandTurn;	// ターン数
	var $islandLastTime;	// 最終更新時刻
	var $islandNumber;	// 島の総数
	var $islandNextID;	// 次に割り当てる島ID
	var $islands;		// 全島の情報を格納
	var $idToNumber;
	var $idToName;

	//---------------------------------------------------
	// 全島データを読み込む
	// 'mode'が変わる可能性があるので$cgiを参照で受け取る
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
		
	// ターン処理判定
	$now = time();
	if((DEBUG && (strcmp($cgi->dataSet['mode'], 'debugTurn') == 0)) ||
		 (($now - $this->islandLastTime) >= $init->unitTime)) {
		
		//MEMO ここでターン進行中を示すロックファイルを作成(turn.lock)
		//MEMO １分以上経過しているファイルは、削除
		//MEMO ロックファイルが存在していたらターン処理中止
		$now_turning = false;
		//ロックファイルの存在確認
		if (file_exists("turn.lock")){
			//ファイルのタイムスタンプ確認
			$lock_time = filemtime("turn.lock");
			if ($lock_time < time() - 60) {
				//ロックファイルが古い";
				//ファイル削除
				unlink("turn.lock");
				//ファイル作成
				$fno = fopen("turn.lock", 'w');
				fclose($fno);
			} else {
				//現在ターン処理中
				$now_turning = true;
			}
		} else {
			//ファイル作成
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
	// 島ひとつ読み込む
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

		// 地形
		$offset = 4; // 一対のデータが何文字か
		for($y = 0; $y < $init->islandSize; $y++) {
		$line = chop(fgets($fp_i, READ_LINE));
		for($x = 0; $x < $init->islandSize; $x++) {
			$l = substr($line, $x * $offset	 , 2);
			$v = substr($line, $x * $offset + 2, 2);
			$land[$x][$y]		 = hexdec($l);
			$landValue[$x][$y] = hexdec($v);
		}
		}
		
		// コマンド
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
		// ローカル掲示板
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
	// 全島データを書き込む
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
	// 島ひとつ書き込む
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
	// 地形
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

		// コマンド
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

		// ローカル掲示板
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
	// データのバックアップ
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

	// ログファイルだけコピーする
	for($i = 0; $i <= $init->logMax; $i++) {
		if(is_file("{$init->dirName}.bak0/hakojima.log{$i}"))
		copy("{$init->dirName}.bak0/hakojima.log{$i}", "{$init->dirName}/hakojima.log{$i}");
	}
	if(is_file("{$init->dirName}.bak0/hakojima.his"))
		copy("{$init->dirName}.bak0/hakojima.his", "{$init->dirName}/hakojima.his");
	}
	//---------------------------------------------------
	// 不要なディレクトリとファイルを削除
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
	// ログファイルを後ろにずらす
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
	// 最近の出来事を出力
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
		$m = "<strong>(機密)</strong>";
		} else {
		$m = "";
		}
		if($id != 0) {
		if(($id != $id1) && ($id != $id2)) {
			continue;
		} else {
			if ($island_name){
				$message = preg_replace("/".$island_name."島(\([0-9])/","地点$1",$message);
				$message = preg_replace("/".$island_name."/","本",$message);
			}
		}
		}
		if($message) $isdata = true;
		if ($turn != $bef_turn){
		print "<tr><tr><td valign=\"top\"nowrap>{$init->tagNumber_}ターン{$turn}{$init->_tagNumber}：</td><td>{$init->tagDisaster_}{$m}{$init->_tagDisaster}{$message}</td></tr>\n";
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
	// 発見の記録を出力
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
			print "<tr><td valign=\"top\"nowrap>{$init->tagNumber_}ターン{$turn}{$init->_tagNumber}：</td><td>$his</td></tr>\n";
		} else {
			print "<tr><td></td><td>$his</td></tr>\n";
		}
		$bef_turn=$turn;
	}
	print "</table>";
	}
	//---------------------------------------------------
	// 発見の記録を保存
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
	// 発見の記録ログ調整
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
	// ログ
	//---------------------------------------------------
	function out($str, $id = "", $tid = "") {
	array_push($this->logPool, "0,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}");
	}
	//---------------------------------------------------
	// 機密ログ
	//---------------------------------------------------
	function secret($str, $id = "", $tid = "") {
	array_push($this->secretLogPool,"1,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}");
	}
	//---------------------------------------------------
	// 遅延ログ
	//---------------------------------------------------
	function late($str, $id = "", $tid = "") {
	array_push($this->lateLogPool,"0,{$GLOBALS['ISLAND_TURN']},{$id},{$tid},{$str}");
	}
	//---------------------------------------------------
	// ログ書き出し
	//---------------------------------------------------
	function flush() {
	global $init;
	
	$fileName = "{$init->dirName}/hakojima.log0";

	if(!is_file($fileName))
		touch($fileName);
		
	$fp = fopen($fileName, "w");
	flock($fp, LOCK_EX);

	// 全部逆順にして書き出す<- 正順で記録に変更 nao-pon
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
	// メール送信
	//---------------------------------------------------
	function SendMail($emails){
		global $init,$xoopsConfig;
		$email="";
		if ((!empty($this->lateLogPool)) || (!empty($this->logPool))){
			if ($emails) $email = implode(",", $emails);
			$to = $xoopsConfig['adminmail'];
			$subject = strip_tags($xoopsConfig['sitename'])."-".$init->title."-最新ニュース";
			$mailBody = strip_tags($xoopsConfig['sitename'])." 地方の ".$init->title." の最新ニュースをお知らせします。\n\n";
			$mailBody .= "-----------------------------------------------------------------------------\n";
			if(!empty($this->lateLogPool)) {
				for($i = 0; $i < count($this->lateLogPool); $i++) {
				$log_text = explode(",",$this->lateLogPool[$i],5);
				$mailBody .= "ターン:".$log_text[1]." - ".strip_tags($log_text[4])."\n";
				}
			}
			if(!empty($this->logPool)) {
				for($i = 0; $i < count($this->logPool); $i++) {
				$log_text = explode(",",$this->logPool[$i],5);
				$mailBody .= "ターン:".$log_text[1]." - ".strip_tags($log_text[4])."\n";
				}
			}
			$mailBody .= "-----------------------------------------------------------------------------\n\n";
			$mailBody .= "　以上最新ニュースをお知らせしました。\n\n";
			$mailBody .= strip_tags($xoopsConfig['sitename'])."-".$init->title."\n(URL)".$init->mainFileUrl;
			
			//echo $mailBody;
			/* 追加ヘッダ */
			$headers .= "From: ".Util::make_header($xoopsConfig['sitename'])." <".$to.">\n";
			if ($email) $headers .= "Bcc: $email\n";

			/* ここでメールを送信する */
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
	// 資金の表示
	//---------------------------------------------------
	function aboutMoney($money = 0) {
	global $init;
	if($init->moneyMode) {
		if($money < 500) {
		return "推定500{$init->unitMoney}未満";
		} else {
		return "推定" . round($money / 1000) . "000" . $init->unitMoney;
		}
	} else {
		return $money . $init->unitMoney;
	}
	}
	//---------------------------------------------------
	// 経験地からミサイル基地レベルを算出
	//---------------------------------------------------
	function expToLevel($kind, $exp) {
	global $init;
	if($kind == $init->landBase) {
		// ミサイル基地
		for($i = $init->maxBaseLevel; $i > 1; $i--) {
		if($exp >= $init->baseLevelUp[$i - 2]) {
			return $i;
		}
		}
		return 1;
	} else {
		// 海底基地
		for($i = $init->maxSBaseLevel; $i > 1; $i--) {
		if($exp >= $init->sBaseLevelUp[$i - 2]) {
			return $i;
		}
		}
		return 1;
	}
	}
	//---------------------------------------------------
	// 怪獣の種類・名前・体力を算出
	//---------------------------------------------------
	function monsterSpec($lv) {
	global $init;
	// 種類
	$kind = (int)($lv / 10);
	// 名前
	$name = $init->monsterName[$kind];
	// 体力
	$hp = $lv - ($kind * 10);
	return array ( 'kind' => $kind, 'name' => $name, 'hp' => $hp );
	}
	//---------------------------------------------------
	// 島の名前から番号を算出
	//---------------------------------------------------
	function	nameToNumber($hako, $name) {
	// 全島から探す
	for($i = 0; $i < $hako->islandNumber; $i++) {
		if(strcmp($name, "{$hako->islands[$i]['name']}") == 0) {
		return $i;
		}
	}
	// 見つからなかった場合
	return -1;
	}
	//---------------------------------------------------
	// オーナーEmailから番号を算出
	//---------------------------------------------------
	function	emailToNumber($hako, $email) {
	// 全島から探す
	for($i = 0; $i < $hako->islandNumber; $i++) {
		if(strcmp($email, "{$hako->islands[$i]['email']}") == 0) {
		return $i;
		}
	}
	// 見つからなかった場合
	return -1;
	}
	//---------------------------------------------------
	// パスワードチェック
	//---------------------------------------------------
	function checkPassword($p1 = "", $p2 = "") {
	global $init,$xoopsUser;

	// nullチェック
	if(empty($p2))
		return false;
	// XOOPS管理者はTrueを返す
	if ( $xoopsUser ) {
		$xoopsModule = XoopsModule::getByDirname("hako");
		if ($xoopsUser->isAdmin($xoopsModule->mid())) { 
			return true;
		}
	}
	// マスターパスワードチェック
	//if(strcmp($init->masterPassword, $p2) == 0)
		//return true;

	if(strcmp($p1, Util::encode($p2)) == 0)
		return true;
	
	return false;
	}
	//---------------------------------------------------
	// パスワードのエンコード
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
	// 0 0 num -1 の乱数生成
	//---------------------------------------------------
	function random($num = 0) {
	if($num <= 1) return 0;
	// マイクロ秒で表した時間によりシード
	list($usec, $sec) = explode(' ', microtime());
	mt_srand((float) $sec + ((float) $usec * 100000));
	return mt_rand(0, $num - 1);
	}
	//---------------------------------------------------
	// ローカル掲示板のメッセージを一つ前にずらす
	//---------------------------------------------------
	function slideBackLbbsMessage(&$lbbs, $num) {
	global $init;
	array_splice($lbbs, $num, 1);
	$lbbs[$init->lbbsMax - 1] = '0>>';
	}
	//---------------------------------------------------
	// ローカル掲示板のメッセージを一つ後ろにずらす
	//---------------------------------------------------
	function slideLbbsMessage(&$lbbs) {
	array_pop($lbbs);
	array_unshift($lbbs, $lbbs[0]);
	}
	//---------------------------------------------------
	// ランダムな座標を生成
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
	// ランダムな島の順序を生成
	//---------------------------------------------------
	function randomArray($n = 1) {
	// 初期値
	for($i = 0; $i < $n; $i++) {
		$list[$i] = $i;
	}

	// シャッフル
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
	// コマンドを前にずらす
	//---------------------------------------------------
	function slideFront(&$command, $number = 0) {
	global $init;
	// それぞれずらす
	array_splice($command, $number, 1);

	// 最後に資金繰り
	$command[$init->commandMax - 1] = array (
		'kind'	 => $init->comDoNothing,
		'target' => 0,
		'x'		 => 0,
		'y'		 => 0,
		'arg'		 => 0
		);
	}
	//---------------------------------------------------
	// コマンドを後にずらす
	//---------------------------------------------------
	function slideBack(&$command, $number = 0) {
	global $init;
	// それぞれずらす
	if($number == count($command) - 1)
		return;

	for($i = $init->commandMax - 1; $i >= $number; $i--) {
		$command[$i] = $command[$i - 1];
	}
	}

	function euc_convert($arg) {
	// 文字コードをEUC-JPに変換して返す
	// 文字列の文字コードを判別
	$code = i18n_discover_encoding("$arg");
	// 非EUC-JPの場合のみEUC-JPに変換
	if ( $code != "EUC-JP" ) {
		$arg = i18n_convert("$arg","EUC-JP");
	}
	return $arg;
	}

	function sjis_convert($arg) {
	// 文字コードをSHIFT_JISに変換して返す
	// 文字列の文字コードを判別
	$code = i18n_discover_encoding("$arg");
	// 非SHIFT_JISの場合のみSHIFT_JISに変換
	if ( $code != "SJIS" ) {
		$arg = i18n_convert("$arg","SJIS");
	}
	return $arg;
	}
	
	//---------------------------------------------------
	// 船なのかのチェック
	//---------------------------------------------------
	function checkShip($kind,$lv) {
		global $init;
		$shiplev = $init->shipKind + 2;
		if(($kind == $init->landSea) && ((($lv > 1) && ($lv < $shiplev)) || ($lv == 255))){
		return true;
		}
		return false; # 船以外
	}

	/////////////////////////////////////////////////
	// メール送信用
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
	// POST、GETのデータを取得
	//---------------------------------------------------
	function parseInputData() {
	global $xoopsUser;
	$this->mode = $_POST['mode'];
	if(!empty($_POST)) {

		// Xoops Protector 対策
		$toupper = array("islandname","developemode","ownername","email","islandid","skin","lbbsname","lbbsmessage","lbbsname","lbbsmessage","password","number","command","pointx","amount","targetid","commandmode","message","comary","from_js","tomail");

		foreach($_POST as $name=>$value) {
//			$value = Util::sjis_convert($value);
		// 半角カナがあれば全角に変換して返す
//			$value = i18n_ja_jp_hantozen($value,"KHV");
			$value = str_replace(",", "", $value);
			$value = JcodeConvert($value, 0, 1);

			// Xoops Protector 対策
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
		// XOOPSユーザー名をパスワードにセット
		$this->dataSet["PASSWORD"] = $xoopsUser->uname();
		$this->dataSet["PASSWORD2"] = $xoopsUser->uname();
		$this->dataSet["OLDPASS"] = $xoopsUser->uname();
	}
	}
	function lastModified() {
	global $init;

	// Last Modifiedヘッダを出力
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
	// COOKIEを取得
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
	//パスワードにxoopsユーザー名をセット
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
	// COOKIEを生成
	//---------------------------------------------------
	function setCookies() {

	$time = time() + 30 * 86400; // 現在 + 30日有効

	// Cookieの設定 & POSTで入力されたデータで、Cookieから取得したデータを更新
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
		//ロックファイル削除
		unlink("turn.lock");
		Error::turndMessage();
		break;
	case "turn":
		$turn = new Turn;
		$html = new HtmlTop;
		$html->header($cgi->dataSet);
		$turn->turnMain($hako, $cgi->dataSet);
		//ロックファイル削除
		unlink("turn.lock");
		$html->main($hako, $cgi->dataSet); // ターン処理後、TOPページopen
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