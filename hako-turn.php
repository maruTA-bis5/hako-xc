<?php
/*******************************************************************

	Ȣ����磲 for PHP

	$Id$

*******************************************************************/

require 'hako-log.php';

class Make {
	//---------------------------------------------------
	// ��ο��������⡼��
	//---------------------------------------------------
	function newIsland($hako, $data) {
	global $init;
	$log = new Log;
	if($hako->islandNumber >= $init->maxIsland) {
		Error::newIslandFull();
		return;
	}
	if(empty($data['ISLANDNAME'])) {
		Error::newIslandNoName();
		return;
	}
	// �᡼�륢�ɥ쥹�ν�ʣ�����å�
	if(Util::emailToNumber($hako, $data['EMAIL']) != -1) {
		Error::newIslandLimit();
		return;
	}
	// ̾���������������å�
	if(ereg("[,\?\(\)\<\>\$]", $data['ISLANDNAME']) || strcmp($data['ISLANDNAME'], "̵��") == 0) {
		Error::newIslandBadName();
		return;
	}
	// ̾���ν�ʣ�����å�
	if(Util::nameToNumber($hako, $data['ISLANDNAME']) != -1) {
		Error::newIslandAlready();
		return;
	}
	// �ѥ���ɤ�¸��Ƚ��
	if(empty($data['PASSWORD'])) {
		Error::newIslandNoPassword();
		return;
	}
	if(strcmp($data['PASSWORD'], $data['PASSWORD2']) != 0) {
		Error::wrongPassword();
		return;
	}
	// ����������ֹ�����
	$newNumber = $hako->islandNumber;
	$hako->islandNumber++;
	$island = $this->makeNewIsland();

	// �Ƽ���ͤ�����
	$island['name']	 = htmlspecialchars($data['ISLANDNAME']);
	$island['owner'] = htmlspecialchars($data['OWNERNAME']);
	$island['email'] = htmlspecialchars($data['EMAIL']);
	$island['tomail'] = htmlspecialchars($data['TOMAIL']);
	$island['id']	 = $hako->islandNextID;
	$hako->islandNextID++;
	$island['absent'] = $init->giveupTurn - 3;
	$island['comment'] = '(̤����)';
	$island['comment_turn'] = $hako->islandTurn;
	$island['password'] = Util::encode($data['PASSWORD']);

	Turn::estimate($island);
	$hako->islands[$newNumber] = $island;
	$hako->writeIslandsFile($island['id']);

	$log->discover($island['name']);

	$htmlMap = new HtmlMap;
	$htmlMap->newIslandHead($island['name']);
	$htmlMap->islandInfo($island, $newNumber);
	$htmlMap->islandMap($hako, $island, 1, $data);
	print<<<END
<div align="center">
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="ISLANDID" value="{$island['id']}">
<input type="hidden" name="mode" value="owner">
<script>
<!--
document.write('<input type=\"hidden\" name=\"DEVELOPEMODE\" value=\"java\">');
-->
</script>
<noscript><input type="hidden" name="DEVELOPEMODE" value="cgi"></noscript>
<input type="submit" value="���ä�����ȯ���˹Ԥ�">
</form>
</div>
END;
	}
	//---------------------------------------------------
	// ����������������
	//---------------------------------------------------
	function makeNewIsland() {
	global $init;
	$command = array();
	// ������ޥ������
	for($i = 0; $i < $init->commandMax; $i++) {
		$command[$i] = array (
		'kind'	 => $init->comDoNothing,
		'target' => 0,
		'x'		 => 0,
		'y'		 => 0,
		'arg'	 => 0,
		);
	}
	$lbbs = "";
	// ����Ǽ�������
	for($i = 0; $i < $init->lbbsMax; $i++) {
		$lbbs[$i] = "0>>";
	}
	$land = array();
	$landValue = array();
	// ���ܷ������
	for($y = 0; $y < $init->islandSize; $y++) {
		for($x = 0; $x < $init->islandSize; $x++) {
		$land[$x][$y]		 = $init->landSea;
		$landValue[$x][$y] = 0;
		}
	}
	
	// 4*4�˹��Ϥ�����
	$center = $init->islandSize / 2 - 1;
	for($y = $center -1; $y < $center + 3; $y++) {
		for($x = $center - 1; $x < $center + 3; $x++) {
		$land[$x][$y] = $init->landWaste;
		}
	}
	// 8*8�ϰ����Φ�Ϥ�����
	for($i = 0; $i < 120; $i++) {
		$x = Util::random(8) + $center - 3;
		$y = Util::random(8) + $center - 3;
		if(Turn::countAround($land, $x, $y, $init->landSea, 7) != 7) {
		// �����Φ�Ϥ������硢�����ˤ���
		// �����Ϲ��Ϥˤ���
		// ���Ϥ�ʿ�Ϥˤ���
		if($land[$x][$y] == $init->landWaste) {
			$land[$x][$y] = $init->landPlains;
			$landValue[$x][$y] = 0;
		} else {
			if($landValue[$x][$y] == 1) {
			$land[$x][$y] = $init->landWaste;
			$landValue[$x][$y] = 0;
			} else {
			$landValue[$x][$y] = 1;
			}
		}
		}
	}
	// ������
	$count = 0;
	while($count < 4) {
		// �������ɸ
		$x = Util::random(4) + $center - 1;
		$y = Util::random(4) + $center - 1;

		// ���������Ǥ˿��Ǥʤ���С�������
		if($land[$x][$y] != $init->landForest) {
		$land[$x][$y] = $init->landForest;
		$landValue[$x][$y] = 5; // �ǽ��500��
		$count++;
		}
	}
	$count = 0;
	while($count < 2) {
		// �������ɸ
		$x = Util::random(4) + $center - 1;
		$y = Util::random(4) + $center - 1;

		// ����������Į�Ǥʤ���С�Į����
		if(($land[$x][$y] != $init->landTown) &&
		 ($land[$x][$y] != $init->landForest)) {
		$land[$x][$y] = $init->landTown;
		$landValue[$x][$y] = 5; // �ǽ��500��
		$count++;
		}
	}

	// ������
	$count = 0;
	while($count < 1) {
		// �������ɸ
		$x = Util::random(4) + $center - 1;
		$y = Util::random(4) + $center - 1;

		// ����������Į�Ǥʤ���С�Į����
		if(($land[$x][$y] != $init->landTown) &&
		 ($land[$x][$y] != $init->landForest)) {
		$land[$x][$y] = $init->landMountain;
		$landValue[$x][$y] = 0; // �ǽ�Ϻη���ʤ�
		$count++;
		}
	}

	// ���Ϥ���
	$count = 0;
	while($count < 1) {
		// �������ɸ
		$x = Util::random(4) + $center - 1;
		$y = Util::random(4) + $center - 1;

		// ����������Į�����Ǥʤ���С�����
		if(($land[$x][$y] != $init->landTown) &&
		 ($land[$x][$y] != $init->landForest) &&
		 ($land[$x][$y] != $init->landMountain)) {
		$land[$x][$y] = $init->landBase;
		$landValue[$x][$y] = 0;
		$count++;
		}
	}

	return array (
		'money'		=> $init->initialMoney,
		'food'		=> $init->initialFood,
		'land'		=> $land,
		'landValue' => $landValue,
		'command'		=> $command,
		'lbbs'		=> $lbbs,
		'prize'		=> '0,0,',
		);	
	}
	//---------------------------------------------------
	// �����ȹ���
	//---------------------------------------------------
	function commentMain($hako, $data) {
	$id	 = $data['ISLANDID'];
	$num = $hako->idToNumber[$id];
	$island = $hako->islands[$num];
	$name = $island['name'];

	// �ѥ����
	if(!Util::checkPassword($island['password'], $data['PASSWORD'])) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
	}
	// ��å������򹹿�
	$island['comment'] = htmlspecialchars($data['MESSAGE']);
	$island['tomail'] = $data['TOMAIL'];
	$island['comment_turn'] = $hako->islandTurn;
	$hako->islands[$num] = $island;

	// �ǡ����ν񤭽Ф�
	$hako->writeIslandsFile();

	// �����ȹ�����å�����
	HtmlSetted::Comment();

	
	// owner mode��
	if($data['DEVELOPEMODE'] == "cgi") {
		$html = new HtmlMap;
	} else {
		$html = new HtmlJS;
	}
	$html->owner($hako, $data);
	}
	//---------------------------------------------------
	// ������Ǽ��ĥ⡼��
	//---------------------------------------------------
	function localBbsMain($hako, $data) {
		global $init,$xoopsConfig;
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$Xname = $xoopsUser->uname();
	} else {
		$Xname = "������";
	}
	// ---- //
	$id	 = $data['ISLANDID'];
	$num = $hako->idToNumber[$id];
	$island = $hako->islands[$num];
	$name = $island['name'];
	$email = $island['email'];
	$tomail = $island['tomail'];

	// �ʤ��������礬�ʤ����
	if(empty($data['ISLANDID'])) {
		Error::problem();
		return;
	}

	// ����⡼�ɤ���ʤ���̾������å��������ʤ����
	if($data['lbbsMode'] != 2) {
		if(empty($data['LBBSNAME']) || (empty($data['LBBSMESSAGE']))) {
		Error::lbbsNoMessage();
		return;
		}
	}

	// �Ѹ��ԥ⡼�ɤ���ʤ����ϥѥ���ɥ����å�
	if($data['lbbsMode'] != 0) {
		if(!Util::checkPassword($island['password'], $data['PASSWORD'])) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
		}
	}

	$lbbs = $island['lbbs'];

	// �⡼�ɤ�ʬ��
	if($data['lbbsMode'] == 2) {
		// ����⡼��
		// ��å����������ˤ��餹
		Util::slideBackLbbsMessage($lbbs, $data['NUMBER']);
		HtmlSetted::lbbsDelete();
	} else {
		// ��Ģ�⡼��
		Util::slideLbbsMessage($lbbs);

		// ��å������񤭹���
		if($data['lbbsMode'] == 0) {
		$message = '0';
		} else {
		$message = '1';
		}
		$bbs_name = "{$hako->islandTurn}��" . htmlspecialchars($data['LBBSNAME'])."@".$Xname;
		$bbs_message = htmlspecialchars($data['LBBSMESSAGE']);
		$lbbs[0] = "{$message}>{$bbs_name}>{$bbs_message}";

		HtmlSetted::lbbsAdd();
	}
	$island['lbbs'] = $lbbs;
	$hako->islands[$num] = $island;

	// �ǡ����񤭽Ф�
	$hako->writeIslandsFile($id);

	//�᡼������
		if ($init->mailUse){
			if ((!$tomail) || (!$email)) $email="";
			$headers = "";
			if ($email) {
				$to = $email;
			} else {
				$to = $xoopsConfig['adminmail'];
			}
			$subject = strip_tags($xoopsConfig['sitename'])."-".$init->title."-".$name."��Ѹ����̿�";
			$mailBody = strip_tags($xoopsConfig['sitename'])." ������ ".$init->title." ".$name."��Ѹ����̿��Ǥ���\n\n";
			$mailBody .= "-----------------------------------------------------------------------------\n";
			$mailBody .= "������:{$hako->islandTurn} �˰ʲ�����Ƥ����ä����ͤǤ���\n";
			$mailBody .= "��Ƽ�:{$data['LBBSNAME']}@{$Xname}����\n";
			$mailBody .= "����:{$data['LBBSMESSAGE']}\n";
			$mailBody .= "-----------------------------------------------------------------------------\n\n";
			$mailBody .= "���ʾ�".$name."��Ѹ����̿����Τ餻���ޤ�����\n\n";
			$mailBody .= strip_tags($xoopsConfig['sitename'])."-".$init->title."\n(URL)".$init->mainFileUrl;

			/* �ɲåإå� */
			$headers .= "From: ".Util::make_header($xoopsConfig['sitename'])." <".$xoopsConfig['adminmail'].">\n";
			if ($email) $headers .= "Cc: ".$xoopsConfig['adminmail']."\n";

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

	if($data['DEVELOPEMODE'] == "cgi") {
		$html = new HtmlMap;
	} else {
		$html = new HtmlJS;
	}
	// ��ȤΥ⡼�ɤ�
	if($data['lbbsMode'] == 0) {
		$html->visitor($hako, $data);
	} else {
		$html->owner($hako, $data);
	}
	}
	//---------------------------------------------------
	// �����ѹ��⡼��
	//---------------------------------------------------
	function changeMain($hako, $data) {
	global $init;
	$log = new Log;
	
	$id	 = $data['ISLANDID'];
	$num = $hako->idToNumber[$id];
	$island = $hako->islands[$num];
	$name = $island['name'];

	// �ѥ���ɥ����å�
	if(strcmp($data['OLDPASS'], $init->specialPassword) == 0) {
		// �ü�ѥ����
		$island['money'] = $init->maxMoney;
		$island['food']	 = $init->maxFood;
	} elseif(!Util::checkPassword($island['password'], $data['OLDPASS'])) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
	}

	// ��ǧ�ѥѥ����
	if(strcmp($data['PASSWORD'], $data['PASSWORD2']) != 0) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
	}

	if(!empty($data['ISLANDNAME'])) {
		// ̾���ѹ��ξ��
		// ̾���������������å�
		if(ereg("[,\?\(\)\<\>\$]", $data['ISLANDNAME']) || strcmp($data['ISLANDNAME'], "̵��") == 0) {
		Error::newIslandBadName();
		return;
		}

		// ̾���ν�ʣ�����å�
		if(Util::nameToNumber($hako, $data['ISLANDNAME']) != -1) {
		Error::newIslandAlready();
		return;
		}

		if($island['money'] < $init->costChangeName) {
		// �⤬­��ʤ�
		Error::changeNoMoney();
		return;
		}

		// ���
		if(strcmp($data['OLDPASS'], $init->specialPassword) != 0) {
		$island['money'] -= $init->costChangeName;
		}

		// ̾�����ѹ�
		$log->changeName($island['name'], $data['ISLANDNAME']);
		$island['name'] = $data['ISLANDNAME'];
		$flag = 1;
	}

	// password�ѹ��ξ��
	if(!empty($data['PASSWORD'])) {
		// �ѥ���ɤ��ѹ�
		$island['password'] = Util::encode($data['PASSWORD']);
		$flag = 1;
	}

	if(($flag == 0) && (strcmp($data['PASSWORD'], $data['PASSWORD2']) != 0)) {
		// �ɤ�����ѹ�����Ƥ��ʤ�
		Error::changeNothing();
		return;
	}

	$hako->islands[$num] = $island;
	// �ǡ����񤭽Ф�
	$hako->writeIslandsFile($id);

	// �ѹ�����
	HtmlSetted::change();
	}
	//---------------------------------------------------
	// ������̾�ѹ��⡼��
	//---------------------------------------------------
	function changeOwnerName($hako, $data) {
	global $init;

	$id	 = $data['ISLANDID'];
	$num = $hako->idToNumber[$id];
	$island = $hako->islands[$num];

	// �ѥ���ɥ����å�
	if(strcmp($data['OLDPASS'], $init->specialPassword) == 0) {
		// �ü�ѥ����
		$island['money'] = $init->maxMoney;
		$island['food']	 = $init->maxFood;
	} elseif(!Util::checkPassword($island['password'], $data['OLDPASS'])) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
	}
	$island['owner'] = htmlspecialchars($data['OWNERNAME']);
	$hako->islands[$num] = $island;
	// �ǡ����񤭽Ф�
	$hako->writeIslandsFile($id);

	// �ѹ�����
	HtmlSetted::change();
	}
	//---------------------------------------------------
	// ���ޥ�ɥ⡼��
	//---------------------------------------------------
	function commandMain($hako, $data) {
	global $init;
	$id	 = $data['ISLANDID'];
	$num = $hako->idToNumber[$id];
	$island = $hako->islands[$num];
	$name = $island['name'];

	// �ѥ����
	if(!Util::checkPassword($island['password'], $data['PASSWORD'])) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
	}

	// �⡼�ɤ�ʬ��
	$command = $island['command'];

	if(strcmp($data['COMMANDMODE'], 'delete') == 0) {
		Util::slideFront($command, $data['NUMBER']);
		HtmlSetted::commandDelete();
	} elseif(($data['COMMAND'] == $init->comAutoPrepare) ||
			 ($data['COMMAND'] == $init->comAutoPrepare2)) {
		// �ե����ϡ��ե��Ϥʤ餷
		// ��ɸ�������
		$r = Util::makeRandomPointArray();
		$rpx = $r[0];
		$rpy = $r[1];
		$land = $island['land'];
		// ���ޥ�ɤμ������
		$kind = $init->comPrepare;
		if($data['COMMAND'] == $init->comAutoPrepare2) {
		$kind = $init->comPrepare2;
		}

		$i = $data['NUMBER'];
		$j = 0;
		while(($j < $init->pointNumber) && ($i < $init->commandMax)) {
		$x = $rpx[$j];
		$y = $rpy[$j];
		if($land[$x][$y] == $init->landWaste) {
			Util::slideBack($command, $data['NUMBER']);
			$command[$data['NUMBER']] = array (
			'kind'	=> $kind,
			'target'	=> 0,
			'x'		=> $x,
			'y'		=> $y,
			'arg'	=> 0,
			);
			$i++;
		}
		$j++;
		}
		HtmlSetted::commandAdd();
	} elseif($data['COMMAND'] == $init->comAutoDelete) {
		// ���ä�
		for($i = 0; $i < $init->commandMax; $i++) {
		Util::slideFront($command, 0);
		}
		HtmlSetted::commandDelete();
	} else {
		if(strcmp($data['COMMANDMODE'], 'insert') == 0) {
		Util::slideBack($command, $data['NUMBER']);
		}
		HtmlSetted::commandAdd();
		// ���ޥ�ɤ���Ͽ
		$command[$data['NUMBER']] = array (
		'kind'	 => $data['COMMAND'],
		'target' => $data['TARGETID'],
		'x'		 => $data['POINTX'],
		'y'		 => $data['POINTY'],
		'arg'	 => $data['AMOUNT'],
		);
	}

	// �ǡ����ν񤭽Ф�
	$island['command'] = $command;
	$hako->islands[$num] = $island;
	$hako->writeIslandsFile($island['id']);

	// owner mode��
	if ($data['FROM_JS']=="java") {
		$html = new HtmlJS;
	} else {
		$html = new HtmlMap;
	}
	$html->owner($hako, $data);
	}
}
class MakeJS extends Make {
	//---------------------------------------------------
	// ���ޥ�ɥ⡼��
	//---------------------------------------------------
	function commandMain($hako, $data) {
	global $init;
	$id	 = $data['ISLANDID'];
	$num = $hako->idToNumber[$id];
	$island = $hako->islands[$num];
	$name = $island['name'];

	// �ѥ����
	if(!Util::checkPassword($island['password'], $data['PASSWORD'])) {
		// password�ְ㤤
		Error::wrongPassword();
		return;
	}
	// �⡼�ɤ�ʬ��
	$command = $island['command'];
	$comary = split(" " , $data['COMARY']);
	
	for($i = 0; $i < $init->commandMax; $i++) {
		$pos = $i * 5;
		$kind		= $comary[$pos];
		$x		= $comary[$pos + 1];
		$y		= $comary[$pos + 2];
		$arg		= $comary[$pos + 3];
		$target = $comary[$pos + 4];
		// ���ޥ����Ͽ
		if($kind == 0) {
		$kind = $init->comDoNothing;
		}
		$command[$i] = array (
		'kind'	 => $kind,
		'x'		 => $x,
		'y'		 => $y,
		'arg'	 => $arg,
		'target' => $target
		);
	}
	HtmlSetted::commandAdd();

	// �ǡ����ν񤭽Ф�
	$island['command'] = $command;
	$hako->islands[$num] = $island;
	$hako->writeIslandsFile($island['id']);

	// owner mode��
	$html = new HtmlJS;
	$html->owner($hako, $data);
	}
	
}
//--------------------------------------------------------------------
class Turn {
	var $log;
	var $rpx;
	var $rpy;
	//---------------------------------------------------
	// ������ʹԥ⡼��
	//---------------------------------------------------
	function turnMain(&$hako, $data) {
	global $init;
	$this->log = new Log;
	
	//Ĺ���ٲ������
	$rest_islands = $init->rest_islands;
	
	// �ǽ��������֤򹹿�
	$hako->islandLastTime += $init->unitTime;
	// ���ե��������ˤ��餹
	$this->log->slideBackLogFile();

	// �������ֹ�
	$hako->islandTurn++;
	$GLOBALS['ISLAND_TURN'] = $hako->islandTurn;
	if($hako->islandNumber == 0) {
		// �礬�ʤ���Х����������¸���ưʹߤν����Ͼʤ�
		// �ե�����˽񤭽Ф�
		$hako->writeIslandsFile();
		return;
	}

	// ��ɸ�������
	$randomPoint = Util::makeRandomPointArray();
	$this->rpx = $randomPoint[0];
	$this->rpy = $randomPoint[1];
	// ���ַ��
	$order = Util::randomArray($hako->islandNumber);

	// ����������
	for($i = 0; $i < $hako->islandNumber; $i++) {
		//Ĺ���ٲ�����硩
		if (in_array($hako->islands[$order[$i]]['name'],$rest_islands))
		continue;
		
		$this->estimate($hako->islands[$order[$i]]);
		$this->income($hako->islands[$order[$i]]);

		// �͸����⤹��
		$hako->islands[$order[$i]]['oldPop'] = $hako->islands[$order[$i]]['pop'];
	}
	// ���ޥ�ɽ���
	for($i = 0; $i < $hako->islandNumber; $i++) {
		//Ĺ���ٲ�����硩
		if (in_array($hako->islands[$order[$i]]['name'],$rest_islands))
		continue;
		
		// �����1�ˤʤ�ޤǷ����֤�
		while($this->doCommand($hako, $hako->islands[$order[$i]]) == 0);
	}
	// ��Ĺ�����ñ�إå����ҳ�
	for($i = 0; $i < $hako->islandNumber; $i++) {
		//Ĺ���ٲ�����硩
		if (in_array($hako->islands[$order[$i]]['name'],$rest_islands))
		continue;
		
		$this->doEachHex($hako, $hako->islands[$order[$i]]);
	}
	// �����ν���
	$emails = array(); //������᡼�륢�ɥ쥹����
	$remainNumber = $hako->islandNumber;

	for($i = 0; $i < $hako->islandNumber; $i++) {
		//Ĺ���ٲ�����硩
		if (in_array($hako->islands[$order[$i]]['name'],$rest_islands))
		continue;
		
		$island = $hako->islands[$order[$i]];

		if (($island['email']) && ($island['tomail'])){
		$emails[] = $island['email'];
		}
		
		$this->doIslandProcess($hako, $island);

		// ����Ƚ��
		if($island['dead'] == 1) {
		$island['pop'] = 0;
		$remainNumber--;
		} elseif($island['pop'] == 0) {
		$island['dead'] = 1;
		$remainNumber--;
		// ���ǥ�å�����
		$tmpid = $island['id'];
		$this->log->dead($tmpid, $island['name']);
		if(is_file("island.{$tmpid}")) {
			unlink("island.{$tmpid}");
		}
		}
		$hako->islands[$order[$i]] = $island;
	}
	// �͸���˥�����
	$this->islandSort($hako);
	// ���������оݥ�������ä��顢���ν���
	if(($hako->islandTurn % $init->turnPrizeUnit) == 0) {
		$island = $hako->islands[0];
		$this->log->prize($island['id'], $island['name'], "{$hako->islandTurn}{$init->prizeName[0]}");
		$hako->islands[0]['prize'] .= "{$hako->islandTurn},";
	}
	// ������å�
	$hako->islandNumber = $remainNumber;

	// �Хå����åץ�����Ǥ���С�������rename
	if(($hako->islandTurn % $init->backupTurn) == 0) {
		$hako->backUp();
	}
	// �ե�����˽񤭽Ф�
	$hako->writeIslandsFile(-1);

	// ���񤭽Ф�
	$this->log->flush();

	// �᡼������
	if ($init->mailUse) $this->log->SendMail($emails);

	// ��Ͽ��Ĵ��
	$this->log->historyTrim();

	}
	//---------------------------------------------------
	// ���ޥ�ɥե�����
	//---------------------------------------------------
	function doCommand(&$hako, &$island) {
	global $init;

	$comArray = &$island['command'];
	$command	= $comArray[0];
	Util::slideFront(&$comArray, 0);
	$island['command'] = $comArray;
	
	$kind	= $command['kind'];
	$target = $command['target'];
	$x		= $command['x'];
	$y		= $command['y'];
	$arg	= $command['arg'];

	$name = $island['name'];
	$id		= $island['id'];
	$land = $island['land'];
	$landValue = &$island['landValue'];
	$landKind = &$land[$x][$y];
	$lv		= $landValue[$x][$y];
	$cost = $init->comCost[$kind];
	$comName = $init->comName[$kind];
	$point = "({$x},{$y})";
	$landName = $this->landName($landKind, $lv);

	$prize = &$island['prize'];

	if($kind == $init->comDoNothing) {
		//$this->log->doNothing($id, $name, $comName);
		$island['money'] += 10;
		$island['absent']++;
		// ��ư����
		if($island['absent'] >= $init->giveupTurn) {
		$comArray[0] = array (
			'kind'	 => $init->comGiveup,
			'target' => 0,
			'x'		 => 0,
			'y'		 => 0,
			'arg'		 => 0
			);
		$island['command'] = $comArray;
		}
		return 1;
	}
	$island['command'] = $comArray;
	$island['absent']	 = 0;
	// �����ȥ����å�
	if($cost > 0) {
		// ��ξ��
		if($island['money'] < $cost) {
		$this->log->noMoney($id, $name, $comName);
		echo "$this->log->noMoney($id, $name, $comName);";
		return 0;
		}
	} elseif($cost < 0) {
		// �����ξ��
		if($island['food'] < (-$cost)) {
		$this->log->noFood($id, $name, $comName);
		return 0;
		}
	}

	$returnMode = 1;
	switch($kind) {
	case $init->comPrepare:
	case $init->comPrepare2:
		// ���ϡ��Ϥʤ餷
		if(($landKind == $init->landSea) ||
		 ($landKind == $init->landSbase) ||
		 ($landKind == $init->landSeaSide) ||
		 ($landKind == $init->landOil) ||
		 ($landKind == $init->landMountain) ||
		 ($landKind == $init->landMonster)) {
		// �������͡�������ϡ����ġ��������ä����ϤǤ��ʤ�
		$this->log->landFail($id, $name, $comName, $landName, $point);

		$returnMode = 0;
		break;
		}
		// ��Ū�ξ���ʿ�Ϥˤ���
		$land[$x][$y] = $init->landPlains;
		$landValue[$x][$y] = 0;
		$this->log->landSuc($id, $name, '����', $point);

		// ��򺹤�����
		$island['money'] -= $cost;

		if($kind == $init->comPrepare2) {
		// �Ϥʤ餷
		$island['prepare2']++;

		// ��������񤻤�
		$returnMode = 0;
		} else {
		// ���Ϥʤ顢��¢��β�ǽ������
		if(Util::random(1000) < $init->disMaizo) {
			$v = 100 + Util::random(901);
			$island['money'] += $v;
			$this->log->maizo($id, $name, $comName, $v);
		}
		$returnMode = 1;
		}
		break;
	case $init->comReclaim:
		// ���Ω��
		if(($landKind != $init->landSea) &&
		 ($landKind != $init->landOil) &&
		 ($landKind != $init->landSeaSide) &&
		 ($landKind != $init->landSbase)) {
		// �������͡�������ϡ����Ĥ������Ω�ƤǤ��ʤ�
		$this->log->landFail($id, $name, $comName, $landName, $point);

		$returnMode = 0;
		break;
		}

		// �����Φ�����뤫�����å�
		$seaCount =
		Turn::countAround($land, $x, $y, $init->landSea, 7) +
		 Turn::countAround($land, $x, $y, $init->landSeaSide, 7) +
			Turn::countAround($land, $x, $y, $init->landOil, 7) +
			Turn::countAround($land, $x, $y, $init->landSbase, 7);

		if($seaCount == 7) {
		// ���������������Ω����ǽ
		$this->log->noLandAround($id, $name, $comName, $point);

		$returnMode = 0;
		break;
		}

		if((($landKind == $init->landSea) && ($lv == 1)) || ($landKind == $init->landSeaSide)) {
		// ���������ͤξ��
		// ��Ū�ξ�����Ϥˤ���
		$land[$x][$y] = $init->landWaste;
		$landValue[$x][$y] = 0;
		$this->log->landSuc($id, $name, $comName, $point);
		if ($landKind != $init->landSeaSide) $island['area']++;

		if($seaCount <= 4) {
			// ����γ���3�إå�������ʤΤǡ������ˤ���

			for($i = 1; $i < 7; $i++) {
			$sx = $x + $init->ax[$i];
			$sy = $y + $init->ay[$i];

			// �Ԥˤ�����Ĵ��
			if((($sy % 2) == 0) && (($y % 2) == 1)) {
				$sx--;
			}

			if(($sx < 0) || ($sx >= $init->islandSize) ||
				 ($sy < 0) || ($sy >= $init->islandSize)) {
			} else {
				// �ϰ���ξ��
				if($land[$sx][$sy] == $init->landSea) {
				$landValue[$sx][$sy] = 1;
				}
			}
			}
		}
		} else {
		// ���ʤ顢��Ū�ξ��������ˤ���
		$land[$x][$y] = $init->landSea;
		$landValue[$x][$y] = 1;
		$this->log->landSuc($id, $name, $comName, $point);
		}

		// ��򺹤�����
		$island['money'] -= $cost;
		$returnMode =	 1;
		break;

	case $init->comDestroy:
		// ����
		if(($landKind == $init->landSbase) ||
		 ($landKind == $init->landOil) ||
		 ($landKind == $init->landMonster)) {
		// ������ϡ����ġ����äϷ���Ǥ��ʤ�
		$this->log->landFail($id, $name, $comName, $landName, $point);

		$returnMode = 0;
		break;
		}

		if(($landKind == $init->landSea) && ($lv == 0)) {
		// ���ʤ顢����õ��
		// ���۷���
		if($arg == 0) { $arg = 1; }

		$value = min($arg * ($cost), $island['money']);
		$str = "{$value}{$init->unitMoney}";
		$p = round($value / $cost);
		$island['money'] -= $value;

		// ���Ĥ��뤫Ƚ��
		if($p > Util::random(100)) {
			// ���ĸ��Ĥ���
			$this->log->oilFound($id, $name, $point, $comName, $str);
			$land[$x][$y] = $init->landOil;
			$landValue[$x][$y] = 0;
		} else {
			// ̵�̷���˽����
			$this->log->oilFail($id, $name, $point, $comName, $str);
		}
		$returnMode = 1;
		break;
		}

		// ��Ū�ξ��򳤤ˤ��롣���ʤ���Ϥˡ������ʤ鳤�ˡ�
		if($landKind == $init->landMountain) {
		$land[$x][$y] = $init->landWaste;
		$landValue[$x][$y] = 0;
		} elseif($landKind == $init->landSea) {
		$landValue[$x][$y] = 0;
		} else {
		$land[$x][$y] = $init->landSea;
		$landValue[$x][$y] = 1;
		$island['area']--;
		}
		$this->log->landSuc($id, $name, $comName, $point);

		// ��򺹤�����
		$island['money'] -= $cost;

		$returnMode = 1;
		break;

	case $init->comSellTree:
		// Ȳ��
		if($landKind != $init->landForest) {
		// ���ʳ���Ȳ�ΤǤ��ʤ�
		$this->log->landFail($id, $name, $comName, $landName, $point);

		$returnMode = 0;
		break;
		}

		// ��Ū�ξ���ʿ�Ϥˤ���
		$land[$x][$y] = $init->landPlains;
		$landValue[$x][$y] = 0;
		$this->log->landSuc($id, $name, $comName, $point);

		// ��Ѷ������
		$island['money'] += $init->treeValue * $lv;

		$returnMode = 1;
		break;
		
		case $init->comSeaSide:
			# ��������
			if((($landKind == $init->landSea) && ($lv == 1)) || ($landKind == $init->landSeaSide)) {
				# �����Φ�����뤫�����å�
				$seaCount =
					Turn::countAround($land, $x, $y, $init->landSea, 7) +
					 Turn::countAround($land, $x, $y, $init->landSeaSide, 7) +
						Turn::countAround($land, $x, $y, $init->landOil, 7) +
							Turn::countAround($land, $x, $y, $init->landSbase, 7);

				if($seaCount == 7) {
					$this->log->noLandAround($id, $name, $comName, $point);

					$returnMode = 0;
					break;
				}
				if($landKind == $init->landSeaSide) {
					// ���Ǥ˺��ͤξ��
					$landValue[$x][$y] += 25; // ���� + 2500��
					if($landValue[$x][$y] > 200) {
						$landValue[$x][$y] = 200; // ���� 20000��
					}
				} else {
					# ��Ū�ξ����ͤˤ���
					$land[$x][$y] = $init->landSeaSide;
					$landValue[$x][$y] = 50; //����ε���5000��
				}
				$this->log->LandSuc($id, $name, $comName, $point);
				# ��򺹤�����
				$island['money'] -= $cost;
				// ����դ��ʤ顢���ޥ�ɤ��᤹
				if($arg > 1) {
					$arg--;
					Util::slideBack($comArray, 0);
					$comArray[0] = array (
						'kind'	 => $kind,
						'target' => $target,
						'x'			 => $x,
						'y'			 => $y,
						'arg'	 => $arg
						);
				}
				$returnMode =	 1;
				break;
			} else {
				# ���������Ͱʳ��������Ǥ��ʤ�
				$this->log->LandFail($id, $name, $comName, $landName, $point);
				$returnMode = 0;
				break;
			}


	case $init->comPort:
		# ��
		if(!($landKind == $init->landSea && $lv == 1)){
			# �����ʳ��ˤϷ����Բ�
			$this->log->LandFail($id, $name, $comName, $landName, $point);
			$returnMode = 0;
			break;
		}
		$seaCount = Turn::countAround($land, $x, $y, $init->landSea, 7);
		if($seaCount <= 1){
			# ���Ϥ˺���1Hex�γ���̵����������Բ�
			$this->log->NoSeaAround($id, $name, $comName, $point);
			$returnMode = 0;
			break;
		}
		if($seaCount == 7){
			# ���꤬�������ʤΤǹ��Ϸ��ߤǤ��ʤ�
			$this->log->NoLandAround($id, $name, $comName, $point);
			$returnMode = 0;
			break;
		}
		$land[$x][$y] = $init->landPort;
		$landValue[$x][$y] = 0;
		$this->log->LandSuc($id, $name, $comName, $point);
		# ��򺹤�����
		$island['money'] -= $cost;
		$returnMode = 1;
		break;
		
	case $init->comMakeShip:
		# ¤��
		if($island['port'] <= 0){
			# �����ʤ��ȼ���
			$this->log->NoPort($id, $name, $comName, $point);
			$returnMode = 0;
			break;
		}
		if(!($landKind == $init->landSea &&	 $lv == 0)){
			# �������֤����꤬����̵�����ϼ���
			$this->log->NoSea($id, $name, $comName, $point);
			$returnMode = 0;
			break;
		}
		$arg += 2;
		if(!Util::checkShip($landKind, $arg)) $arg = 2; # ���ǻ��ѤǤ�������ϰϤ���

		$land[$x][$y] = $init->landSea;
		$landValue[$x][$y] = $arg;
		$this->log->LandSuc($id, $name, $init->shipName[$arg-2]."��".$comName, $point);

		# ��򺹤�����
		$island['money'] -= $cost;
		$returnMode = 1;
		break;

	case $init->comPlant:
	case $init->comFarm:
	case $init->comFactory:
	case $init->comBase:
	case $init->comMonument:
	case $init->comHaribote:
	case $init->comDbase:
	case $init->comSeaResort:
	case $init->comPark:
	
		// �Ͼ���߷�
		if(!
		 (($landKind == $init->landPlains) ||
			($landKind == $init->landTown)	 ||
			(($landKind == $init->landMonument) && ($kind == $init->comMonument)) ||
			(($landKind == $init->landFarm)		&& ($kind == $init->comFarm))		||
			(($landKind == $init->landFactory)	&& ($kind == $init->comFactory))	||
			(($landKind == $init->landSeaResort)	&& ($kind == $init->comSeaResort))	||
			(($landKind == $init->landPark)	 && ($kind == $init->comPark))	||
			(($landKind == $init->landDefence)	&& ($kind == $init->comDbase)))) {
		// ��Ŭ�����Ϸ�
		$this->log->landFail($id, $name, $comName, $landName, $point);

		$returnMode = 0;
		break;
		}

		// �����ʬ��
		switch($kind) {
		case $init->comPlant:
		// ��Ū�ξ��򿹤ˤ��롣
		$land[$x][$y] = $init->landForest;
		$landValue[$x][$y] = 1; // �ڤϺ���ñ��
		$this->log->PBSuc($id, $name, $comName, $point);
		break;

		case $init->comBase:
		// ��Ū�ξ���ߥ�������Ϥˤ��롣
		$land[$x][$y] = $init->landBase;
		$landValue[$x][$y] = 0; // �и���0
		$this->log->PBSuc($id, $name, $comName, $point);
		break;

		case $init->comHaribote:
		// ��Ū�ξ���ϥ�ܥƤˤ���
		$land[$x][$y] = $init->landHaribote;
		$landValue[$x][$y] = 0;
		$this->log->hariSuc($id, $name, $comName, $init->comName[$init->comDbase], $point);
		break;
		
		case $init->comPark:
		// ��Ū�ξ���ͷ���Ϥˤ���
		$land[$x][$y] = $init->landPark;
		$landValue[$x][$y] = 0;
		$this->log->LandSuc($id, $name, $comName, $point);
		break;
		
		case $init->comFarm:
		// ����
		if($landKind == $init->landFarm) {
			// ���Ǥ�����ξ��
			$landValue[$x][$y] += 2; // ���� + 2000��
			if($landValue[$x][$y] > 50) {
			$landValue[$x][$y] = 50; // ���� 50000��
			}
		} else {
			// ��Ū�ξ��������
			$land[$x][$y] = $init->landFarm;
			$landValue[$x][$y] = 10; // ���� = 10000��
		}
		$this->log->landSuc($id, $name, $comName, $point);
		break;

		case $init->comFactory:
		// ����
		if($landKind == $init->landFactory) {
			// ���Ǥ˹���ξ��
			$landValue[$x][$y] += 10; // ���� + 10000��
			if($landValue[$x][$y] > 100) {
			$landValue[$x][$y] = 100; // ���� 100000��
			}
		} else {
			// ��Ū�ξ��򹩾��
			$land[$x][$y] = $init->landFactory;
			$landValue[$x][$y] = 30; // ���� = 10000��
		}
		$this->log->landSuc($id, $name, $comName, $point);
		break;
		
		case $init->comSeaResort:
		// ���β�
		if (Turn::countAround($land, $x, $y, $init->landSeaResort, 19)) {
			# ���ϣ��إå����˳��βȤ�����
			$this->log->LandFail($id, $name, $comName, '���βȤζ᤯', $point);
			$returnMode = 0;
			break;
		} else {
			# ���ϣ��إå����˳��βȤ��ʤ�
			$land[$x][$y] = $init->landSeaResort;
			$landValue[$x][$y] = 0;
			$this->log->LandSuc($id, $name, $comName, $point);
		}
		break;
		
		case $init->comDbase:
		// �ɱһ���
		if($landKind == $init->landDefence) {
			// ���Ǥ��ɱһ��ߤξ��
			$landValue[$x][$y] = 1; // �������֥��å�
			$this->log->bombSet($id, $name, $landName, $point);
		} else {
			// ��Ū�ξ����ɱһ��ߤ�
			$land[$x][$y] = $init->landDefence;
			$landValue[$x][$y] = 0;
			$this->log->landSuc($id, $name, $comName, $point);
		}
		break;
		
		case $init->comMonument:
		// ��ǰ��
		if($landKind == $init->landMonument) {
			// ���Ǥ˵�ǰ��ξ��
			// �������åȼ���
			$tn = $hako->idToNumber[$target];
			if($tn != 0 && empty($tn)) {
			// �������åȤ����Ǥˤʤ�
			// ������鷺�����

			$returnMode = 0;
			break;
			}

			$hako->islands[$tn]['bigmissile']++;

			// ���ξ��Ϲ��Ϥ�
			$land[$x][$y] = $init->landWaste;
			$landValue[$x][$y] = 0;
			$this->log->monFly($id, $name, $landName, $point);
		} else {
			// ��Ū�ξ���ǰ���
			$land[$x][$y] = $init->landMonument;
			if($arg >= $init->monumentNumber) {
			$arg = 0;
			}
			$landValue[$x][$y] = $arg;
			$this->log->landSuc($id, $name, $comName, $point);
		}
		break;
		}

		// ��򺹤�����
		$island['money'] -= $cost;

		// ����դ��ʤ顢���ޥ�ɤ��᤹
		if(($kind == $init->comFarm) ||
		 ($kind == $init->comFactory)) {
		if($arg > 1) {
			$arg--;
			Util::slideBack($comArray, 0);
			$comArray[0] = array (
			'kind'	 => $kind,
			'target' => $target,
			'x'		 => $x,
			'y'		 => $y,
			'arg'	 => $arg
			);
		}
		}

		$returnMode = 1;
		break;
		// �����ޤ��Ͼ���߷�
	case $init->comMountain:
		// �η���
		if($landKind != $init->landMountain) {
		// ���ʳ��ˤϺ��ʤ�
		$this->log->landFail($id, $name, $comName, $landName, $point);

		$returnMode = 0;
		break;
		}

		$landValue[$x][$y] += 5; // ���� + 5000��
		if($landValue[$x][$y] > 200) {
		$landValue[$x][$y] = 200; // ���� 200000��
		}
		$this->log->landSuc($id, $name, $comName, $point);

		// ��򺹤�����
		$island['money'] -= $cost;
		if($arg > 1) {
		$arg--;
		Util::slideBack(&$comArray, 0);
		$comArray[0] = array (
			'kind'	 => $kind,
			'target' => $target,
			'x'		 => $x,
			'y'		 => $y,
			'arg'		 => $arg,
			);
		}
		$returnMode = 1;
		break;

	case $init->comSbase:
		// �������
		if(($landKind != $init->landSea) || ($lv != 0)){
		// ���ʳ��ˤϺ��ʤ�
		$this->log->landFail($id, $name, $comName, $landName, $point);
		$returnMode = 0;
		break;
		}

		$land[$x][$y] = $init->landSbase;
		$landValue[$x][$y] = 0; // �и���0
		$this->log->landSuc($id, $name, $comName, '(?, ?)');

		// ��򺹤�����
		$island['money'] -= $cost;
		$returnMode = 1;
		break;

	case $init->comMissileNM:
	case $init->comMissilePP:
	case $init->comMissileST:
	case $init->comMissileLD:
		// �ߥ������
		// �������åȼ���
		$tn = $hako->idToNumber[$target];
		if($tn != 0 && empty($tn)) {
		// �������åȤ����Ǥˤʤ�
		$this->log->msNoTarget($id, $name, $comName);

		$returnMode = 0;
		break;
		}

		$flag = 0;
		if($arg == 0) {
		// 0�ξ��Ϸ�Ƥ����
		$arg = 10000;
		}

		// ��������
		$tIsland = &$hako->islands[$tn];
		$tName	 = &$tIsland['name'];
		$tLand	 = &$tIsland['land'];
		$tLandValue = &$tIsland['landValue'];
		// ��̱�ο�
		$boat = 0;

		// ��
		if($kind == $init->comMissileNM) {
		$err = $init->ErrMissileNM;
		} elseif($kind == $init->comMissilePP) {
		$err = $init->ErrMissilePP;
		} elseif($kind == $init->comMissileST) {
		$err = $init->ErrMissileST;
		} elseif($kind == $init->comMissileLD) {
		$err = $init->ErrMissileLD;
		} else {
		$err = $init->ErrMissileNM;
		}
		
		//�桼����������ְ�äƤ����齤��
		if ($err < 0) $err = 0;
		if ($err > 19) $err = 19;

	// ������������
	if (!empty($init->ErrMissileOwn)) {
		if ($err < 8) {
			$err = 0;
		}elseif($err < 20){
			$err = 7;
		}
	}
	
	//���������ʤ���Ψ
	if (isset($init->ErrMissilePer)){
		if (Util::random(100) < $init->ErrMissilePer){
			if ($err < 8) {
				$err = 0;
			}elseif($err < 20){
				$err = 7;
			}
		}
	}

		$bx = $by = 0;
		// �⤬�Ԥ��뤫�������­��뤫������������Ĥޤǥ롼��
		while(($arg > 0) &&
			($island['money'] >= $cost)) {
		// ���Ϥ򸫤Ĥ���ޤǥ롼��
		while($count < $init->pointNumber) {
			$bx = $this->rpx[$count];
			$by = $this->rpy[$count];
			if(($land[$bx][$by] == $init->landBase) ||
			 ($land[$bx][$by] == $init->landSbase)) {
			break;
			}
			$count++;
		}
		if($count >= $init->pointNumber) {
			// ���Ĥ���ʤ��ä��餽���ޤ�
			break;
		}
		// �����Ĵ��Ϥ����ä��Τǡ�flag��Ω�Ƥ�
		$flag = 1;
		// ���ϤΥ�٥�򻻽�
		$level = Util::expToLevel($land[$bx][$by], $landValue[$bx][$by]);
		// ������ǥ롼��
		while(($level > 0) &&
				($arg > 0) &&
				($island['money'] > $cost)) {
			// ��ä��Τ�����ʤΤǡ����ͤ���פ�����
			$level--;
			$arg--;
			$island['money'] -= $cost;

			// ����������
			$r = Util::random($err);
			$tx = $x + $init->ax[$r];
			$ty = $y + $init->ay[$r];
			if((($ty % 2) == 0) && (($y % 2) == 1)) {
			$tx--;
			}

			// �������ϰ��⳰�����å�
			if(($tx < 0) || ($tx >= $init->islandSize) ||
			 ($ty < 0) || ($ty >= $init->islandSize)) {
			// �ϰϳ�
			if($kind == $init->comMissileST) {
				// ���ƥ륹
				$this->log->msOutS($id, $target, $name, $tName, $comName, $point);
			} else {
				// �̾��
				$this->log->msOut($id, $target, $name, $tName, $comName, $point);
			}
			continue;
			}

			// ���������Ϸ�������
			$tL	 = $tLand[$tx][$ty];
			$tLv = $tLandValue[$tx][$ty];
			$tLname = $this->landName($tL, $tLv);
			$tPoint = "({$tx}, {$ty})";

			// �ɱһ���Ƚ��
			$defence = 0;
			if($defenceHex[$id][$tx][$ty] == 1) {
			$defence = 1;
			} elseif($defenceHex[$id][$tx][$ty] == -1) {
			$defence = 0;
			} else {
			if($tL == $init->landDefence) {
				// �ɱһ��ߤ�̿��
				// �ե饰�򥯥ꥢ
				for($i = 0; $i < 19; $i++) {
				$sx = $tx + $init->ax[$i];
				$sy = $ty + $init->ay[$i];

				// �Ԥˤ�����Ĵ��
				if((($sy % 2) == 0) && (($ty % 2) == 1)) {
					$sx--;
				}

				if(($sx < 0) || ($sx >= $init->islandSize) ||
					 ($sy < 0) || ($sy >= $init->islandSize)) {
					// �ϰϳ��ξ�粿�⤷�ʤ�
				} else {
					// �ϰ���ξ��
					$defenceHex[$id][$sx][$sy] = 0;
				}
				}
			} elseif(Turn::countAround($tLand, $tx, $ty, $init->landDefence, 19)) {
				$defenceHex[$id][$tx][$ty] = 1;
				$defence = 1;
			} else {
				$defenceHex[$id][$tx][$ty] = -1;
				$defence = 0;
			}
			}

			if($defence == 1) {
			// ��������
			if($kind == $init->comMissileST) {
				// ���ƥ륹
				$this->log->msCaughtS($id, $target, $name, $tName,$comName, $point, $tPoint);
			} else {
				// �̾��
				$this->log->msCaught($id, $target, $name, $tName, $comName, $point, $tPoint);
			}
			continue;
			}

			// �ָ��̤ʤ���hex��ǽ��Ƚ��
			if((($tL == $init->landSea) && ($tLv == 0))|| // ������
			 (((($tL == $init->landSea) && ($tLv < 2)) ||	 // ���ޤ��ϡ�����
				 ($tL == $init->landSbase) ||	 // ������Ϥޤ��ϡ�����
				 ($tL == $init->landMountain)) // ���ǡ�����
				&& ($kind != $init->comMissileLD))) { // Φ���ưʳ�
			// ������Ϥξ�硢���Υե�
			if($tL == $init->landSbase) {
				$tL = $init->landSea;
			}
			$tLname = $this->landName($tL, $tLv);

			// ̵����
			if($kind == $init->comMissileST) {
				// ���ƥ륹
				$this->log->msNoDamageS($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
			} else {
				// �̾��
				$this->log->msNoDamage($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
			}
			continue;
			}

			// �Ƥμ����ʬ��
			if($kind == $init->comMissileLD) {
			// Φ���˲���
			switch($tL) {
			case $init->landMountain:
				// ��(���Ϥˤʤ�)
				$this->log->msLDMountain($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				// ���Ϥˤʤ�
				$tLand[$tx][$ty] = $init->landWaste;
				$tLandValue[$tx][$ty] = 0;
				continue;

			case $init->landSbase:
				// �������
				$this->log->msLDSbase($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				break;
				
			case $init->landMonster:
				// ����
				$this->log->msLDMonster($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				break;
				
			case $init->landSea:
				// ����
				$this->log->msLDSea1($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				break;
				
			case $init->landSeaSide:
				// ���ͤʤ����
				$this->log->msLDSea1($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				$tLand[$tx][$ty] = $init->landSea;
				$tIsland['area']--;
				$tLandValue[$tx][$ty] = 1;
				break;
				
			default:
				// ����¾
				$this->log->msLDLand($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
			}

			// �и���
			if($tL == $init->landTown) {
				if(($land[$bx][$by] == $init->landBase) ||
				 ($land[$bx][$by] == $init->landSbase)) {
				// �ޤ����Ϥξ��Τ�
				$landValue[$bx][$by] += round($tLv / 20);
				if($landValue[$bx][$by] > $init->maxExpPoint) {
					$landValue[$bx][$by] = $init->maxExpPoint;
				}
				}
			}

			// �����ˤʤ�
			$tLand[$tx][$ty] = $init->landSea;
			$tIsland['area']--;
			$tLandValue[$tx][$ty] = 1;

			// �Ǥ����ġ�������������Ϥ��ä��鳤
			if(($tL == $init->landOil) ||
				 ($tL == $init->landSea) ||
				 ($tL == $init->landSbase)) {
				$tLandValue[$tx][$ty] = 0;
			}
			} else {
			// ����¾�ߥ�����
			if($tL == $init->landWaste) {
				// ����(�ﳲ�ʤ�)
				if($kind == $init->comMissileST) {
				// ���ƥ륹
				$this->log->msWasteS($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				} else {
				// �̾�
				$this->log->msWaste($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				}
			} elseif($tL == $init->landMonster) {
				// ����
				$monsSpec = Util::monsterSpec($tLv);
				$special = $init->monsterSpecial[$monsSpec['kind']];

				// �Ų���?
				if((($special == 3) && (($hako->islandTurn % 2) == 1)) ||
				 (($special == 4) && (($hako->islandTurn % 2) == 0))) {
				// �Ų���
				if($kind == $init->comMissileST) {
					// ���ƥ륹
					$this->log->msMonNoDamageS($id, $target, $name, $tName, $comName, $mName, $point, $tPoint);
				} else {
					// �̾���
					$this->log->msMonNoDamage($id, $target, $name, $tName, $comName, $mName, $point, $tPoint);
				}
				continue;
				} else {
				// �Ų��椸��ʤ�
				if($monsSpec['hp'] == 1) {
					// ���ä��Ȥ᤿
					if(($land[$bx][$by] == $init->landBase) ||
					 ($land[$bx][$by] == $init->landSbase)) {
					// �и���
					$landValue[$bx][$by] += $init->monsterExp[$monsSpec['kind']];
					if($landValue[$bx][$by] > $init->maxExpPoint) {
						$landValue[$bx][$by] = $init->maxExpPoint;
					}
					}

					if($kind == $init->comMissileST) {
					// ���ƥ륹
					$this->log->msMonKillS($id, $target, $name, $tName, $comName, $mName, $point, $tPoint);
					} else {
					// �̾�
					$this->log->msMonKill($id, $target, $name, $tName, $comName, $mName, $point, $tPoint);
					}

					// ����
					$value = $init->monsterValue[$monsSpec['kind']];
					if($value > 0) {
					$tIsland['money'] += $value;
					$this->log->msMonMoney($target, $mName, $value);
					}

					// �޴ط�
//					$prize = $island['prize'];
					list($flags, $monsters, $turns) = split(",", $prize, 3);
					$v = 1 << $monsSpec['kind'];
					$monsters |= $v;

					$prize = "{$flags},{$monsters},{$turns}";
//					$island['prize'] = "{$flags},{$monsters},{$turns}";
				} else {
					// ���������Ƥ�
					if($kind == $init->comMissileST) {
					// ���ƥ륹
					$this->log->msMonsterS($id, $target, $name, $tName, $comName, $mName, $point, $tPoint);
					} else {
					// �̾�
					$this->log->msMonster($id, $target, $name, $tName, $comName, $mName, $point, $tPoint);
					}
					// HP��1����
					$tLandValue[$tx][$ty]--;
					continue;
				}

				}
			} else {
				// �̾��Ϸ�
				if($kind == $init->comMissileST) {
				// ���ƥ륹
				$this->log->msNormalS($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				} else {
				// �̾�
				$this->log->msNormal($id, $target, $name, $tName, $comName, $tLname, $point, $tPoint);
				}
			}
			// �и���
			if($tL == $init->landTown) {
				if(($land[$bx][$by] == $init->landBase) ||
				 ($land[$bx][$by] == $init->landSbase)) {
				$landValue[$bx][$by] += round($tLv / 20);
				$boat += $tLv; // �̾�ߥ�����ʤΤ���̱�˥ץ饹
				if($landValue[$bx][$by] > $init->maxExpPoint) {
					$landValue[$bx][$by] = $init->maxExpPoint;
				}
				}
			}
			if(Util::checkShip($tLand[$tx][$ty], $tLv)){
				# �����ä��鳤�ˤʤ�
				$tLand[$tx][$ty] = $init->landSea;
				$tLandValue[$tx][$ty] = 0;
			} else {
				// ���Ϥˤʤ�
				$tLand[$tx][$ty] = $init->landWaste;
				$tLandValue[$tx][$ty] = 1; // ������
			}
			// �Ǥ����Ĥ��ä��鳤
			if($tL == $init->landOil) {
				$tLand[$tx][$ty] = $init->landSea;
				$tLandValue[$tx][$ty] = 0;
			}
			}
		}

		// ����������䤷�Ȥ�
		$count++;
		}


		if($flag == 0) {
		// ���Ϥ���Ĥ�̵���ä����
		$this->log->msNoBase($id, $name, $comName);

		$returnMode = 0;
		break;
		}
		
		$tIsland['land'] = $tLand;
		$tIsland['landValue'] = $tLandValue;
		unset($hako->islands[$tn]);
		$hako->islands[$tn] = $tIsland;
		
		
		// ��̱Ƚ��
		$boat = round($boat / 2);
		if(($boat > 0) && ($id != $target) && ($kind != $init->comMissileST)) {
		// ��̱ɺ��
		$achive = 0; // ��ã��̱
		for($i = 0; ($i < $init->pointNumber && $boat > 0); $i++) {
			$bx = $this->rpx[$i];
			$by = $this->rpy[$i];
			if($land[$bx][$by] == $init->landTown) {
			// Į�ξ��
			$lv = $landValue[$bx][$by];
			if($boat > 50) {
				$lv += 50;
				$boat -= 50;
				$achive += 50;
			} else {
				$lv += $boat;
				$achive += $boat;
				$boat = 0;
			}
			if($lv > 200) {
				$boat += ($lv - 200);
				$achive -= ($lv - 200);
				$lv = 200;
			}
			$landValue[$bx][$by] = $lv;
			} elseif($land[$bx][$by] == $init->landPlains) {
			// ʿ�Ϥξ��
			$land[$bx][$by] = $init->landTown;;
			if($boat > 10) {
				$landValue[$bx][$by] = 5;
				$boat -= 10;
				$achive += 10;
			} elseif($boat > 5) {
				$landValue[$bx][$by] = $boat - 5;
				$achive += $boat;
				$boat = 0;
			}
			}
			if($boat <= 0) {
			break;
			}
		}
		if($achive > 0) {
			// �����Ǥ����夷����硢�����Ǥ�
			$this->log->msBoatPeople($id, $name, $achive);

			// ��̱�ο���������ʾ�ʤ顢ʿ�¾ޤβ�ǽ������
			if($achive >= 200) {
			$prize = $island['prize'];
			list($flags, $monsters, $turns) = split(",", $prize, 3);

			if((!($flags & 8)) &&	 $achive >= 200){
				$flags |= 8;
				$this->log->prize($id, $name, $init->prizeName[4]);
			} elseif((!($flags & 16)) &&	$achive > 500){
				$flags |= 16;
				$this->log->prize($id, $name, $init->prizeName[5]);
			} elseif((!($flags & 32)) &&	$achive > 800){
				$flags |= 32;
				$this->log->prize($id, $name, $init->prizeName[6]);
			}
			$island['prize'] = "{$flags},{$monsters},{$turns}";
			}
		}
		}
		
		$returnMode = 1;
		break;

	case $init->comSendMonster:
		// �����ɸ�
		// �������åȼ���
		$tn = $hako->idToNumber[$target];
		$tIsland = $hako->islands[$tn];
		$tName = $tIsland['name'];
		
		if($tn != 0 && empty($tn)) {
		// �������åȤ����Ǥˤʤ�
		$this->log->msNoTarget($id, $name, $comName);

		$returnMode = 0;
		break;
		}

		// ��å�����
		$this->log->monsSend($id, $target, $name, $tName);
		$tIsland['monstersend']++;
		$hako->islands[$tn] = $tIsland;

		$island['money'] -= $cost;
		$returnMode = 1;
		break;
	case $init->comSell:
		// ͢���̷���
		if($arg == 0) { $arg = 1; }
		$value = min($arg * (-$cost), $island['food']);

		// ͢�Х�
		$this->log->sell($id, $name, $comName, $value);
		$island['food'] -=	$value;
		$island['money'] += ($value / 10);

		$returnMode = 0;
		break;
		
	case $init->comFood:
	case $init->comMoney:
		// �����
		// �������åȼ���
		$tn = $hako->idToNumber[$target];
		$tIsland = $hako->islands[$tn];
		$tName = $tIsland['name'];

		// ����̷���
		if($arg == 0) { $arg = 1; }

		if($cost < 0) {
		$value = min($arg * (-$cost), $island['food']);
		$str = "{$value}{$init->unitFood}";
		} else {
		$value = min($arg * ($cost), $island['money']);
		$str = "{$value}{$init->unitMoney}";
		}

		// �����
		$this->log->aid($id, $target, $name, $tName, $comName, $str);
		
		// ���ʱ��Ƚ��
		if ($tName !== $island['name']) {
			if($cost < 0) {
			$island['food'] -= $value;
			$tIsland['food'] += $value;
			} else {
			$island['money'] -= $value;
			$tIsland['money'] += $value;
			}
			$hako->islands[$tn] = $tIsland;
		}

		$returnMode = 0;
		break;
		
	case $init->comPropaganda:
		// Ͷ�׳�ư
		$this->log->propaganda($id, $name, $comName);
		$island['propaganda'] = 1;
		$island['money'] -= $cost;

		$returnMode = 1;
		break;

	case $init->comGiveup:
		// ����
		$this->log->giveup($id, $name);
		$island['dead'] = 1;
		unlink("{$init->dirName}/island.{$id}");

		$returnMode = 1;
		break;
	}
		
	// �ѹ����줿��ǽ���Τ����ѿ�����᤹
	// �������
	unset($island['prize']);
	unset($island['land']);
	unset($island['landValue']);
	unset($island['command']);
	$island['prize'] = $prize;		
	$island['land'] = $land;
	$island['landValue'] = $landValue;
	$island['command'] = $comArray;
	return $returnMode;
	}
	//---------------------------------------------------
	// ��Ĺ�����ñ�إå����ҳ�
	//---------------------------------------------------
	function doEachHex($hako, &$island) {
	global $init;
	// Ƴ����
	$name = $island['name'];
	$id = $island['id'];
	$land = $island['land'];
	$landValue = $island['landValue'];

	// ������͸��Υ�����
	$addpop	 = 10;	// ¼��Į
	$addpop2 = 0; // �Ի�
	if($island['food'] < 0) {
		// ������­
		$addpop = -30;
	} elseif($island['ship']['viking'] > 0) {
		# ��±�������������Ĺ���ʤ�
		$addpop = 0;
	} elseif($island['propaganda'] == 1) {
		// Ͷ�׳�ư��
		$addpop = 30;
		$addpop2 = 3;
	}
	$monsterMove = array();
	// �롼��
	for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];

		switch($landKind) {
		case $init->landTown:
		// Į��
		if($addpop < 0) {
			// ��­
			$lv -= (Util::random(-$addpop) + 1);
			if($lv <= 0) {
			// ʿ�Ϥ��᤹
			$land[$x][$y] = $init->landPlains;
			$landValue[$x][$y] = 0;
			continue;
			}
		} else {
			// ��Ĺ
			if($lv < 100) {
			$lv += Util::random($addpop) + 1;
			if($lv > 100) {
				$lv = 100;
			}
			} else {
			// �ԻԤˤʤ����Ĺ�٤�
			if($addpop2 > 0) {
				$lv += Util::random($addpop2) + 1;
			}
			}
		}
		if($lv > 200) {
			$lv = 200;
		}
		$landValue[$x][$y] = $lv;
		break;
		
		case $init->landPlains:
		// ʿ��
		if(Util::random(5) == 0) {
			// ��������졢Į������С�������Į�ˤʤ�
			if($this->countGrow($land, $landValue, $x, $y)){
			$land[$x][$y] = $init->landTown;
			$landValue[$x][$y] = 1;
			}
		}
		break;
		
		case $init->landForest:
		// ��
		if($lv < 200) {
			// �ڤ����䤹
			$landValue[$x][$y]++;
		}
		break;

		 case $init->landSeaResort:
			// ���β�
			$nt = Turn::countAroundLevel($island, $x, $y, $init->landTown, 19);//����2�إå����ο͸�
			$ns = Turn::countAroundLevel($island, $x, $y, $init->landSeaSide, 19);//����2�إå����κ��ͼ��ƿͿ�
			// ���פη׻�
			if ($lv > $ns) {
				$value = round($ns * .5 * ((Util::random(10)+5)/10));
			} else {
				$value = round($lv * .5 * ((Util::random(10)+5)/10));
			}
			if ($value > 0) {
				$island['money'] += $value;
				# ������
				$str = "{$value}{$init->unitMoney}";
				$this->log->oilMoney($id, $name, $this->landName($landKind, $lv), "($x,$y)", $str);
			}
			// ��ĹΨ�η׻�
			if ($ns) {
				$rash = $nt / $ns - 1; //���ͤκ��߶��
				$grow = round(($rash * $rash) * -1 + 5);// ��ĹΨ2���ؿ� y=-y^2+5
												 // ���߶�礬334%��Ķ����ȥޥ��ʥ���Ĺ
			} else {
				$grow = 0; // ���ͤ��ʤ������ĹΨ��
			}
			if($lv < 30) {
				# ���β�
				$n = 1;
			} else if($lv < 100) {
				# ̱��
				$n = 2;
			} else {
				# �꥾���ȥۥƥ�
				$n = 4;
			}
			if ($lv > 0) {
				$lv += round($grow / $n); //��٥�⤤����ĹΨ�㤤
			} else {
				$lv += round($grow * $n); //�ޥ��ʥ���Ĺ�Ϥ��ε�
			}
			//echo "<br />nt:ns:grow:value=$nt:$ns:$grow:$value";
			//echo "<br />lv+:".round($grow / $n);
			if ($lv < 1) { $lv = 1; } else if ($lv > 200) { $lv = 200; }
			$landValue[$x][$y] = $lv;
			break;

		case $init->landDefence:
		if($lv == 1) {
			// �ɱһ��߼���
			$lName = $this->landName($landKind, $lv);
			$this->log->bombFire($id, $name, $lName, "($x,$y)");

			// �����ﳲ�롼����
			$this->wideDamage($id, $name, &$land, &$landValue, $x, $y);
		}
		break;
		
		case $init->landOil:
		// ��������
		$lName = $this->landName($landKind, $lv);
		$value = $init->oilMoney;
		$island['money'] += $value;
		$str = "{$value}{$init->unitMoney}";

		// ������
		$this->log->oilMoney($id, $name, $lName, "($x,$y)", $str);

		// �ϳ�Ƚ��
		if(Util::random(1000) < $init->oilRatio) {
			// �ϳ�
			$this->log->oilEnd($id, $name, $lName, "($x,$y)");
			$land[$x][$y] = $init->landSea;
			$landValue[$x][$y] = 0;
		}
		break;
		
			case $init->landPark:
				// ͷ����
				$lName = $this->landName($landKind, $lv);
				//$value = floor($island['pop'] / 50); // �͸�����ͤ��Ȥˣ����ߤμ���
				//���פϿ͸����äȤȤ�˲��Ф�����
				//�͸���ʿ������1���2�� ex 1��=10020���� 100��=1000200����
				$value = floor(sqrt($island['pop'])*((Util::random(100)/100)+1));
				$island['money'] += $value;
				$str = "{$value}{$init->unitMoney}";

				//������
				if ($value > 0)
					$this->log->ParkMoney($id, $name, $lName, "($x,$y)", $str);

				//���٥��Ƚ��
				if(Util::random(100) < 30) {
					// �西���� 30% �γ�Ψ�ǥ��٥�Ȥ�ȯ������
					//ͷ���ϤΥ��٥��
					$value2=$value;
					//��������
					$value = floor($island['pop'] * $init->eatenFood / 2); // ���꿩�������Ⱦʬ����
					$island['food'] -= $value;
					$str = "{$value}{$init->unitFood}";
					if ($value > 0)
					$this->log->ParkEvent($id, $name, $lName, "($x,$y)", $str);
					//���٥�Ȥμ���
					$value = floor((Util::random(200) - 100)/100 * $value2);//�ޥ��ʥ�100%����ץ饹100%
					$island['money'] += $value;
					$str = "{$value}{$init->unitMoney}";
					if ($value > 0) $this->log->ParkEventLuck($id, $name, $lName, "($x,$y)", $str);
					if ($value < 0) $this->log->ParkEventLoss($id, $name, $lName, "($x,$y)", $str);
				}

				// Ϸ�۲�Ƚ��
				if(Util::random(100) < 5) {
					// ���ߤ�Ϸ�۲����������ı�
					$this->log->ParkEnd($id, $name, $lName, "($x,$y)");
					$land[$x][$y] = $init->landPlains;
					$landValue[$x][$y] = 0;
				}
				break;
				
			case $init->landPort:
				# ��
				$lName = $this->landName($landKind, $lv);
				$seaCount = Turn::countAround($land, $x, $y, $init->landSea, 7);
				if(!$seaCount){
					# ���Ϥ˺���1Hex�γ���̵����硢�ĺ�
					$this->log->ClosedPort($id, $name, $lName, "($x,$y)");
					$land[$x][$y] = $init->landSea;
					$landValue[$x][$y] = 1;
				}
				if($seaCount == 6){
					# ���Ϥ˺���1Hex��Φ�Ϥ�̵����硢�ĺ�
					$this->log->ClosedPort($id, $name, $lName, "($x,$y)");
					$land[$x][$y] = $init->landSea;
					$landValue[$x][$y] = 1;
				}
				break;
				
			case $init->landMonster:
		// ����
		if($monsterMove[$x][$y] == 2) {
			// ���Ǥ�ư������
			break;
		}

		// �����Ǥμ��Ф�
		$monsSpec = Util::monsterSpec($landValue[$x][$y]);
		$special	= $init->monsterSpecial[$monsSpec['kind']];

		// �Ų���?
		if((($special == 3) && (($hako->islandTurn % 2) == 1)) ||
			 (($special == 4) && (($hako->islandTurn % 2) == 0))) {
			// �Ų���
			break;
		}

		// ư�����������
		for($j = 0; $j < 3; $j++) {
			$d = Util::random(6) + 1;
			$sx = $x + $init->ax[$d];
			$sy = $y + $init->ay[$d];

			// �Ԥˤ�����Ĵ��
			if((($sy % 2) == 0) && (($y % 2) == 1)) {
			$sx--;
			}

			// �ϰϳ�Ƚ��
			if(($sx < 0) || ($sx >= $init->islandSize) ||
			 ($sy < 0) || ($sy >= $init->islandSize)) {
			continue;
			}
			// �����������ġ����á�������ǰ��ʳ�
			if(($land[$sx][$sy] != $init->landSea) &&
			 ($land[$sx][$sy] != $init->landSbase) &&
			 ($land[$sx][$sy] != $init->landOil) &&
			 ($land[$sx][$sy] != $init->landMountain) &&
			 ($land[$sx][$sy] != $init->landMonument) &&
			 ($land[$sx][$sy] != $init->landMonster)) {
			break;
			}
		}

		if($j == 3) {
			// ư���ʤ��ä�
			break;
		}

		// ư��������Ϸ��ˤ���å�����
		$l = $land[$sx][$sy];
		$lv = $landValue[$sx][$sy];
		$lName = $this->landName($l, $lv);
		$point = "({$sx},{$sy})";

		// ��ư
		$land[$sx][$sy] = $land[$x][$y];
		$landValue[$sx][$sy] = $landValue[$x][$y];

		// ��ȵ錄���֤���Ϥ�
		$land[$x][$y] = $init->landWaste;
		$landValue[$x][$y] = 0;
		
		// ��ư�Ѥߥե饰
		if($init->monsterSpecial[$monsSpec['kind']] == 2) {
			// ��ư�Ѥߥե饰��Ω�Ƥʤ�
		} elseif($init->monsterSpecial[$monsSpec['kind']] == 1) {
			// ®������
			$monsterMove[$sx][$sy] = $monsterMove[$x][$y] + 1;
		} else {
			// ���̤β���
			$monsterMove[$sx][$sy] = 2;
		}
		if(($l == $init->landDefence) && ($init->dBaseAuto == 1)) {
			// �ɱһ��ߤ�Ƨ���
			$this->log->monsMoveDefence($id, $name, $lName, $point, $mName);

			// �����ﳲ�롼����
			$this->wideDamage($id, $name, &$land, &$landValue, $sx, $sy);
		} else {
			// �Ԥ��褬���Ϥˤʤ�
			$this->log->monsMove($id, $name, $lName, $point, $mName);
		}
		break;
		}
		// ���Ǥ�$init->landTown��caseʸ�ǻȤ��Ƥ���Τ�switch���̤��Ѱ�
		switch($landKind) {
		case $init->landTown:
		case $init->landHaribote:
		case $init->landFactory:
		case $init->landSeaResort:
		case $init->landPark:
		// �к�Ƚ��
		if((($landKind == $init->landSeaResort) && ($lv <= 30)) ||
			($landKind == $init->landTown && ($lv <= 30)))
			break;
		
		if(Util::random(1000) < $init->disFire) {
			// ���Ϥο��ȵ�ǰ��������
			if((Turn::countAround($land, $x, $y, $init->landForest, 7) +
				Turn::countAround($land, $x, $y, $init->landMonument, 7)) == 0) {
			// ̵���ä���硢�кҤǲ���
			$l = $land[$x][$y];
			$lv = $landValue[$x][$y];
			$point = "({$x},{$y})";
			$lName = $this->landName($l, $lv);
			$this->log->fire($id, $name, $lName, $point);
			$land[$x][$y] = $init->landWaste;
			$landValue[$x][$y] = 0;
			}
		}
		break;
		}
		// ���ΰ�ư
		if(Util::checkShip($landKind, $lv)){
			//��ɸ�����λ�
			if($shipMove[$x][$y] != 1){
				//�����ޤ�ư���Ƥ��ʤ���
				if($island['ship']['viking'] > 0 && $landValue[$x][$y] != 255){
					//��±���������оݤ���±���Ǥʤ��Ȥ�
					$cntViking = Turn::countAroundValue($island, $x, $y, $init->landSea, 255, 19);
					if($cntViking){
						//����2�إå�������˳�±��������
						if(($cntViking) && (Util::random(1000) < $init->disVikingAttack)){
							# ��±�������פ�������
							$this->log->VikingAttack($id, $name, $this->landName($landKind, $lv), "($x,$y)");
							$land[$x][$y] = $init->landSea;
							$landValue[$x][$y] = 0;
						}
					}
				} elseif(($landValue[$x][$y] == 255) && (Util::random(1000) < $init->disVikingAway)){
					# ��±�� ���
					$this->log->VikingAway($id, $name, "($x,$y)");
					$island['ship']['viking']--;
					$landValue[$x][$y] = 0;
				}
				if ($landValue[$x][$y] != 0){
					//�����ޤ�¸�ߤ��Ƥ�����
					# ư�����������
					for($j = 0; $j < 3; $j++) {
						$d = Util::random(6) + 1;
						$sx = $x + $init->ax[$d];
						$sy = $y + $init->ay[$d];

						# �Ԥˤ�����Ĵ��
						if((($sy % 2) == 0) && (($y % 2) == 1)) {
							$sx--;
						}

						# �ϰϳ�Ƚ��
						if(($sx < 0) || ($sx >= $init->islandSize) ||
							 ($sy < 0) || ($sy >= $init->islandSize)) {
							continue;
						}

						# ���Ǥ���С�ư�����������
						if(($land[$sx][$sy] == $init->landSea) && ($landValue[$sx][$sy] == 0)){
							break;
						}
					}

					if($j == 3) {
						# ư���ʤ��ä�
					} else {
						# ��ư
						$land[$sx][$sy] = $land[$x][$y];
						$landValue[$sx][$sy] = $landValue[$x][$y];

						# ��ȵ錄���֤򳤤�
						$land[$x][$y] = $init->landSea;
						$landValue[$x][$y] = 0;

						# ��ư�Ѥߥե饰
						if(Util::random(2)){
							$shipMove[$sx][$sy] = 1;
						}
					}
				}
			}
		}
		//���ΰ�ư�����ޤ�
	}
	// �ѹ����줿��ǽ���Τ����ѿ�����᤹
	$island['land'] = $land;
	$island['landValue'] = $landValue;
	}

	//---------------------------------------------------
	// ������
	//---------------------------------------------------
	function doIslandProcess($hako, &$island) {
	global $init;
	
	// Ƴ����
	$name = $island['name'];
	$id = $island['id'];
	$land = $island['land'];
	$landValue = $island['landValue'];

	// �Ͽ�Ƚ��
	if(Util::random(1000) < (($island['prepare2'] + 1) * $init->disEarthquake)) {
		// �Ͽ�ȯ��
		$this->log->earthquake($id, $name);
		// �Ϥʤ餷�ˤ��ȯ��Ψ������ʬ�򸺤餹
		$island['prepare2'] = $island['prepare2'] - Util::random(4);
		if ($island['prepare2'] < 0) $island['prepare2'] = 0;

		for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];

		if((($landKind == $init->landTown) && ($lv >= 100)) ||
			 ($landKind == $init->landHaribote) ||
			 ($landKind == $init->landSeaResort) ||
			 ($landKind == $init->landSeaSide) ||
			 ($landKind == $init->landFactory)) {
			// 1/4�ǲ���
			if(Util::random(4) == 0) {
			$this->log->eQDamage($id, $name, $this->landName($landKind, $lv), "({$x},{$y})");
			$land[$x][$y] = $init->landWaste;
			$landValue[$x][$y] = 0;
			}
		}
		}
	}

	// ������­
	if($island['food'] <= 0) {
		// ��­��å�����
		$this->log->starve($id, $name);
		$island['food'] = 0;

		for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];

		if(($landKind == $init->landFarm) ||
			 ($landKind == $init->landFactory) ||
			 ($landKind == $init->landBase) ||
			 ($landKind == $init->landDefence)) {
			// 1/4�ǲ���
			if(Util::random(4) == 0) {
			$this->log->svDamage($id, $name, $this->landName($landKind, $lv), "({$x},{$y})");
			$land[$x][$y] = $init->landWaste;
			$landValue[$x][$y] = 0;
			}
		}
		}
	}

		// �¾�Ƚ��
		if(Util::random(1000) < $init->disRunAground1){
		for($i = 0; $i < $init->pointNumber; $i++) {
			$x = $this->rpx[$i];
			$y = $this->rpy[$i];
			$landKind = $land[$x][$y];
			$lv = $landValue[$x][$y];
			if((Util::checkShip($landKind, $lv)) && (Util::random(1000) < $init->disRunAground2)){
			$this->log->RunAground($id, $name, landName($landKind, $lv), "($x,$y)");
			$land[$x][$y] = $init->landSea;
			$landValue[$x][$y] = 0;
			}
		}
		}

		// ��±��Ƚ��
		if(Util::random(1000) < $init->disViking){
		# �ɤ��˸���뤫����
		for($i = 0; $i < $init->pointNumber; $i++) {
			$x = $this->rpx[$i];
			$y = $this->rpy[$i];
			$landKind = $land[$x][$y];
			$lv = $landValue[$x][$y];

			if(($landKind == $init->landSea) && ($lv == 0)) {
			# ��±���о�
			$landValue[$x][$y] = 255; //lv 255 ����±��

			# ��å�����
			$this->log->VikingCome($id, $name, "($x,$y)");
			break;
			}
		}
		}

	// ����Ƚ��
	if(Util::random(1000) < $init->disTsunami) {
		// ����ȯ��
		$this->log->tsunami($id, $name);

		for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];

		if(($landKind == $init->landTown)		||
			 ($landKind == $init->landFarm)		||
			 ($landKind == $init->landFactory)	||
			 ($landKind == $init->landBase)		||
			 ($landKind == $init->landDefence)	||
			 ($landKind == $init->landSeaSide)	||
			 ($landKind == $init->landSeaResort)||
			 ($landKind == $init->landSeaSide)	||
			 ($landKind == $init->landPort)		||
			 (Util::checkShip($landKind,$lv))		||
			 ($landKind == $init->landHaribote)) {
			// 1d12 <= (���Ϥγ� - 1) ������
			if(Util::random(12) <
			 (Turn::countAround($land, $x, $y, $init->landOil, 7) +
				Turn::countAround($land, $x, $y, $init->landSbase, 7) +
				Turn::countAround($land, $x, $y, $init->landSea, 7) - 1)) {
			$this->log->tsunamiDamage($id, $name, $this->landName($landKind, $lv), "({$x},{$y})");
			if (($landKind == $init->landSeaSide)||($landKind == $init->landPort)){
				//���ͤ����ʤ���ס�������
				$land[$x][$y] = $init->landSea;
				$landValue[$x][$y] = 1;
			} elseif(Util::checkShip($landKind,$lv)){
				//���ʤ���ס䳤��
				$land[$x][$y] = $init->landSea;
				$landValue[$x][$y] = 0;
			} else {
				$land[$x][$y] = $init->landWaste;
				$landValue[$x][$y] = 0;
			}
			}
		}

		}
	}

	// ����Ƚ��
	$r = Util::random(10000);
	$pop = $island['pop'];
	do{
		if((($r < ($init->disMonster * $island['area'])) &&
			($pop >= $init->disMonsBorder1)) ||
		 ($island['monstersend'] > 0)) {
		// ���ýи�
		// ��������
		if($island['monstersend'] > 0) {
			// ��¤
			$kind = 0;
			$island['monstersend']--;
		} elseif($pop >= $init->disMonsBorder3) {
			// level3�ޤ�
			$kind = Util::random($init->monsterLevel3) + 1;
		} elseif($pop >= $init->disMonsBorder2) {
			// level2�ޤ�
			$kind = Util::random($init->monsterLevel2) + 1;
		} else {
			// level1�Τ�
			$kind = Util::random($init->monsterLevel1) + 1;
		}

		// lv���ͤ����
		$lv = $kind * 10
			+ $init->monsterBHP[$kind] + Util::random($init->monsterDHP[$kind]);

		// �ɤ��˸���뤫����
		for($i = 0; $i < $init->pointNumber; $i++) {
			$bx = $this->rpx[$i];
			$by = $this->rpy[$i];
			if($land[$bx][$by] == $init->landTown) {

			// �Ϸ�̾
			$lName = $this->landName($init->landTown, $landValue[$bx][$by]);

			// ���Υإå�������ä�
			$land[$bx][$by] = $init->landMonster;
			$landValue[$bx][$by] = $lv;

			// ���þ���
			$monsSpec = Util::monsterSpec($lv);

			// ��å�����
			$this->log->monsCome($id, $name, $mName, "({$bx}, {$by})", $lName);
			break;
			}
		}
		}
	} while($island['monstersend'] > 0);

	// ��������Ƚ��
	if(($island['area'] > $init->disFallBorder) &&
		 (Util::random(1000) < $init->disFalldown)) {
		// ��������ȯ��
		$this->log->falldown($id, $name);

		for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];

		if(($landKind != $init->landSea) &&
			 ($landKind != $init->landSbase) &&
			 ($landKind != $init->landOil) &&
			 ($landKind != $init->landMountain)) {

			// ���Ϥ˳�������С��ͤ�-1��
			if(Turn::countAround($land, $x, $y, $init->landSea, 7) +
			 Turn::countAround($land, $x, $y, $init->landSbase, 7)) {
			$this->log->falldownLand($id, $name, $this->landName($landKind, $lv), "({$x},{$y})");
			$land[$x][$y] = -1;
			$landValue[$x][$y] = 0;
			}
		}
		}

		for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];

		if($landKind == -1) {
			// -1�ˤʤäƤ�����������
			$land[$x][$y] = $init->landSea;
			$landValue[$x][$y] = 1;
		} elseif ($landKind == $init->landSea) {
			// �����ϳ���
			$landValue[$x][$y] = 0;
		}

		}
	}

	// ����Ƚ��
	if(Util::random(1000) < $init->disTyphoon) {
		// ����ȯ��
		$this->log->typhoon($id, $name);

		for($i = 0; $i < $init->pointNumber; $i++) {
		$x = $this->rpx[$i];
		$y = $this->rpy[$i];
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];

		if(($landKind == $init->landFarm) ||
			 ($landKind == $init->landSeaSide) ||
			 ($landKind == $init->landHaribote)) {

			// 1d12 <= (6 - ���Ϥο�) ������
			if(Util::random(12) <
			 (6
				- Turn::countAround($land, $x, $y, $init->landForest, 7)
				- Turn::countAround($land, $x, $y, $init->landMonument, 7))) {
			$this->log->typhoonDamage($id, $name, $this->landName($landKind, $lv), "({$x},{$y})");
			if ($landKind == $init->landSeaSide){
				//���ͤ�������
				$land[$x][$y] = $init->landSea;
				$landValue[$x][$y] = 1;
			} else {
				//����¾��ʿ�Ϥ�
				$land[$x][$y] = $init->landPlains;
				$landValue[$x][$y] = 0;
			}
			}
		}
		}
	}

	// �������Ƚ��
	if(Util::random(1000) < $init->disHugeMeteo) {

		// �
		$x = Util::random($init->islandSize);
		$y = Util::random($init->islandSize);
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];
		$point = "({$x},{$y})";

		// ��å�����
		$this->log->hugeMeteo($id, $name, $point);

		// �����ﳲ�롼����
		$this->wideDamage($id, $name, &$land, &$landValue, $x, $y);
	}

	// ����ߥ�����Ƚ��
	while($island['bigmissile'] > 0) {
		$island['bigmissile']--;

		// �
		$x = Util::random($init->islandSize);
		$y = Util::random($init->islandSize);
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];
		$point = "({$x},{$y})";

		// ��å�����
		$this->log->monDamage($id, $name, $point);

		// �����ﳲ�롼����
		$this->wideDamage($id, $name, &$land, &$landValue, $x, $y);
	}

	// ���Ƚ��
	if(Util::random(1000) < $init->disMeteo) {
		$first = 1;
		while((Util::random(2) == 0) || ($first == 1)) {
		$first = 0;

		// �
		$x = Util::random($init->islandSize);
		$y = Util::random($init->islandSize);
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];
		$point = "({$x},{$y})";

		if(($landKind == $init->landSea) && ($lv == 0)){
			// ���ݥ���
			$this->log->meteoSea($id, $name, $this->landName($landKind, $lv), $point);
		} elseif($landKind == $init->landMountain) {
			// ���˲�
			$this->log->meteoMountain($id, $name, $this->landName($landKind, $lv), $point);
			$land[$x][$y] = $init->landWaste;
			$landValue[$x][$y] = 0;
			continue;
		} elseif($landKind == $init->landSbase) {
			$this->log->meteoSbase($id, $name, $this->landName($landKind, $lv), $point);
		} elseif($landKind == $init->landMonster) {
			$this->log->meteoMonster($id, $name, $this->landName($landKind, $lv), $point);
		} elseif($landKind == $init->landSea) {
			// ����
			$this->log->meteoSea1($id, $name, $this->landName($landKind, $lv), $point);
		} else {
			$this->log->meteoNormal($id, $name, $this->landName($landKind, $lv), $point);
		}
		$land[$x][$y] = $init->landSea;
		$landValue[$x][$y] = 0;
		}
	}

	// ʮ��Ƚ��
	if(Util::random(1000) < $init->disEruption) {
		$x = Util::random($init->islandSize);
		$y = Util::random($init->islandSize);
		$landKind = $land[$x][$y];
		$lv = $landValue[$x][$y];
		$point = "({$x},{$y})";
		$this->log->eruption($id, $name, $this->landName($landKind, $lv), $point);
		$land[$x][$y] = $init->landMountain;
		$landValue[$x][$y] = 0;

		for($i = 1; $i < 7; $i++) {
		$sx = $x + $init->ax[$i];
		$sy = $y + $init->ay[$i];

		// �Ԥˤ�����Ĵ��
		if((($sy % 2) == 0) && (($y % 2) == 1)) {
			$sx--;
		}

		$landKind = $land[$sx][$sy];
		$lv = $landValue[$sx][$sy];
		$point = "({$sx},{$sy})";

		if(($sx < 0) || ($sx >= $init->islandSize) ||
			 ($sy < 0) || ($sy >= $init->islandSize)) {
		} else {
			// �ϰ���ξ��
			$landKind = $land[$sx][$sy];
			$lv = $landValue[$sx][$sy];
			$point = "({$sx},{$sy})";
			if(($landKind == $init->landSea) ||
			 ($landKind == $init->landOil) ||
			 ($landKind == $init->landSbase)) {
			// ���ξ��
			if($lv == 1) {
				// ����
				$this->log->eruptionSea1($id, $name, $this->landName($landKind, $lv), $point);
			} else {
				$this->log->eruptionSea($id, $name, $this->landName($landKind, $lv), $point);
				$land[$sx][$sy] = $init->landSea;
				$landValue[$sx][$sy] = 1;
				continue;
			}
			} elseif(($landKind == $init->landMountain) ||
					($landKind == $init->landMonster) ||
					($landKind == $init->landWaste)) {
			continue;
			} else {
			// ����ʳ��ξ��
			$this->log->eruptionNormal($id, $name, $this->landName($landKind, $lv), $point);
			}
			$land[$sx][$sy] = $init->landWaste;
			$landValue[$sx][$sy] = 0;
		}
		}
	}
	// �ѹ����줿��ǽ���Τ����ѿ�����᤹
	$island['land'] = $land;
	$island['landValue'] = $landValue;

	// ���������դ�Ƥ��鴹��
	if($island['food'] > $init->maxFood) {
		$island['money'] += round(($island['food'] - $init->maxFood) / 10);
		$island['food'] = $init->maxFood;
	}

	// �⤬���դ�Ƥ����ڤ�Τ�
	if($island['money'] > $init->maxMoney) {
		$island['money'] = $init->maxMoney;
	}

	// �Ƽ���ͤ�׻�
	Turn::estimate($island);

	// �˱ɡ������
	$pop = $island['pop'];
	$damage = $island['oldPop'] - $pop;
	$prize = $island['prize'];
	list($flags, $monsters, $turns) = split(",", $prize, 3);


	// �˱ɾ�
	if((!($flags & 1)) &&	 $pop >= 3000){
		$flags |= 1;
		$this->log->prize($id, $name, $init->prizeName[1]);
	} elseif((!($flags & 2)) &&	 $pop >= 5000){
		$flags |= 2;
		$this->log->prize($id, $name, $init->prizeName[2]);
	} elseif((!($flags & 4)) &&	 $pop >= 10000){
		$flags |= 4;
		$this->log->prize($id, $name, $init->prizeName[3]);
	}

	// �����
	if((!($flags & 64)) &&	$damage >= 500){
		$flags |= 64;
		$this->log->prize($id, $name, $init->prizeName[7]);
	} elseif((!($flags & 128)) &&	 $damage >= 1000){
		$flags |= 128;
		$this->log->prize($id, $name, $init->prizeName[8]);
	} elseif((!($flags & 256)) &&	 $damage >= 2000){
		$flags |= 256;
		$this->log->prize($id, $name, $init->prizeName[9]);
	}

	$island['prize'] = "{$flags},{$monsters},{$turns}";

	}

	//---------------------------------------------------
	// ���Ϥ�Į�����줬���뤫Ƚ��
	//---------------------------------------------------
	function countGrow($land, $landValue, $x, $y) {
	global $init;

	for($i = 1; $i < 7; $i++) {
		$sx = $x + $init->ax[$i];
		$sy = $y + $init->ay[$i];

		// �Ԥˤ�����Ĵ��
		if((($sy % 2) == 0) && (($y % 2) == 1)) {
		$sx--;
		}

		if(($sx < 0) || ($sx >= $init->islandSize) ||
		 ($sy < 0) || ($sy >= $init->islandSize)) {
		} else {
		// �ϰ���ξ��
		if(($land[$sx][$sy] == $init->landTown) ||
			 ($land[$sx][$sy] == $init->landFarm)) {
			if($landValue[$sx][$sy] != 1) {
			return true;
			}
		}
		}
	}
	return false;
	}
	//---------------------------------------------------
	// �����ﳲ�롼����
	//---------------------------------------------------
	function wideDamage($id, $name, $land, $landValue, $x, $y) {
	global $init;

	for($i = 0; $i < 19; $i++) {
		$sx = $x + $init->ax[$i];
		$sy = $y + $init->ay[$i];

		// �Ԥˤ�����Ĵ��
		if((($sy % 2) == 0) && (($y % 2) == 1)) {
		$sx--;
		}

		$landKind = $land[$sx][$sy];
		$lv = $landValue[$sx][$sy];
		$landName = $this->landName($landKind, $lv);
		$point = "({$sx},{$sy})";

		// �ϰϳ�Ƚ��
		if(($sx < 0) || ($sx >= $init->islandSize) ||
		 ($sy < 0) || ($sy >= $init->islandSize)) {
		continue;
		}

		// �ϰϤˤ��ʬ��
		if($i < 7) {
		// �濴�������1�إå���
		if($landKind == $init->landSea) {
			$landValue[$sx][$sy] = 0;
			continue;
		} elseif(($landKind == $init->landSbase) ||
				 ($landKind == $init->landSeaSide) ||
				 ($landKind == $init->landOil)) {
			$this->log->wideDamageSea2($id, $name, $landName, $point);
			$land[$sx][$sy] = $init->landSea;
			$landValue[$sx][$sy] = 0;
		} else {
			if($landKind == $init->landMonster) {
			$this->log->wideDamageMonsterSea($id, $name, $landName, $point);
			} else {
			$this->log->wideDamageSea($id, $name, $landName, $point);
			}
			$land[$sx][$sy] = $init->landSea;
			if($i == 0) {
			// ��
			$landValue[$sx][$sy] = 0;
			} else {
			// ����
			$landValue[$sx][$sy] = 1;
			}
		}
		} else {
		// 2�إå���
		if(($landKind == $init->landSea) ||
			 ($landKind == $init->landSeaSide) ||
			 ($landKind == $init->landOil) ||
			 ($landKind == $init->landWaste) ||
			 ($landKind == $init->landMountain) ||
			 ($landKind == $init->landSbase)) {
			continue;
		} elseif($landKind == $init->landMonster) {
			$this->log->wideDamageMonster($id, $name, $landName, $point);
			$land[$sx][$sy] = $init->landWaste;
			$landValue[$sx][$sy] = 0;
		} else {
			$this->log->wideDamageWaste($id, $name, $landName, $point);
			$land[$sx][$sy] = $init->landWaste;
			$landValue[$sx][$sy] = 0;
		}
		}
	}
	}

	//---------------------------------------------------
	// �͸���ǥ�����
	//---------------------------------------------------
	function islandSort(&$hako) {
	global $init;
	usort($hako->islands, 'popComp');
	}
	//---------------------------------------------------
	// ����������ե�����
	//---------------------------------------------------
	function income(&$island) {
	global $init;
	
	$pop = $island['pop'];
	$farm = $island['farm'] * 10;
	$factory = $island['factory'];
	$mountain =$island['mountain'];

	// ����
	if($pop > $farm) {
		// ���Ȥ�������꤬;����
		$island['food'] += $farm; // ����ե��Ư
		$island['money'] +=
		min(round(($pop - $farm) / 10),
				$factory + $mountain);
	} else {
		// ���Ȥ����Ǽ���դξ��
		$island['food'] += $pop; // �������ɻŻ�
	}

	// ��������
	$island['food'] = round($island['food'] - $pop * $init->eatenFood);
		# ��
		$island['money'] -= $init->shipMentenanceCost[0] * $island['ship']['passenger'] + $init->shipMentenanceCost[1] * $island['ship']['fishingboat'];
		if($island['port'] > 0){
			//echo "<br />money:".$init->shipIncom * $island['ship']['passenger']."<br />food:".$init->shipFood	 * $island['ship']['fishingboat'];
			$island['money'] += $init->shipIncom * $island['ship']['passenger'];
			$island['food']	 += $init->shipFood	 * $island['ship']['fishingboat'];
		}
		if(($island['ship']['viking'] > 0) && (Util::random(1000) < $init->disRobViking)){
			if(($island['money'] < $init->disVikingMinMoney) || ($island['food'] < $init->disVikingMinFood)){
				
			} else {
				$vMoney = round(Util::random($island['money'])/2);
				$vFood	= round(Util::random($island['food'])/2);
				$this->log->RobViking($island['id'], $island['name'], $vMoney, $vFood);
				$island['money'] -= $vMoney;
				$island['food'] -= $vFood;
				if($island['money'] < 0) $island['money'] = 0;
				if($island['food'] < 0) $island['food']	 = 0 ;
			}
		}
	}
	//---------------------------------------------------
	// �͸�����¾���ͤ򻻽�
	//---------------------------------------------------
	function estimate(&$island) {
	// estimate(&$island) �Τ褦�˻���
	
	global $init;
	$land = $island['land'];
	$landValue = $island['landValue'];

	$are = 0;
	$pop = 0;
	$farm = 0;
	$factory = 0;
	$mountain = 0;
	$monster = 0;
	$port = 0;
	$passenger = $fishingboat = $viking = 0;

	// ������
	for($y = 0; $y < $init->islandSize; $y++) {
		for($x = 0; $x < $init->islandSize; $x++) {
		$kind = $land[$x][$y];
		$value = $landValue[$x][$y];
		if(Util::checkShip($kind, $value)){
			if($value == 2)$passenger++		;
			if($value == 3)$fishingboat++ ;
			if($value == 255)$viking++		;
		}
		if(($kind != $init->landSea) &&
			 ($kind != $init->landSbase) &&
			 ($kind != $init->landOil)){
			$area++;
			switch($kind) {
			case $init->landTown:
			// Į
			$pop += $value;
			break;
			case $init->landFarm:
			// ����
			$farm += $value;
			break;
			case $init->landFactory:
			// ����
			$factory += $value;
			break;
			case $init->landMountain:
			// ��
			$mountain += $value;
			break;
			case $init->landMonster:
			// ����
			$monster++;
			break;
			case $init->landPort:
			// ��
			$port++;
			break;
			}
		}
		}
	}
	// ����
	$island['pop']		= $pop;
	$island['area']		= $area;
	$island['farm']		= $farm;
	$island['factory']	= $factory;
	$island['mountain'] = $mountain;
	$island['monster']	= $monster;
	$island['port']		= $port;
	$island['ship']['passenger'] = $passenger;
	$island['ship']['fishingboat'] = $fishingboat;
	$island['ship']['viking'] = $viking;
	}
	//---------------------------------------------------
	// �ϰ�����Ϸ��������
	//---------------------------------------------------
	function countAround($land, $x, $y, $kind, $range) {
	global $init;
	// �ϰ�����Ϸ��������
	$count = 0;
	for($i = 0; $i < $range; $i++) {
		$sx = $x + $init->ax[$i];
		$sy = $y + $init->ay[$i];

		// �Ԥˤ�����Ĵ��
		if((($sy % 2) == 0) && (($y % 2) == 1)) {
		$sx--;
		}

		if(($sx < 0) || ($sx >= $init->islandSize) ||
		 ($sy < 0) || ($sy >= $init->islandSize)) {
		// �ϰϳ��ξ��
		if($kind == $init->landSea) {
			// ���ʤ�û�
			$count++;
		}
		} else {
		// �ϰ���ξ��
		if($land[$sx][$sy] == $kind) {
			$count++;
		}
		}
	}
	return $count;
	}
	//---------------------------------------------------
	// �ϰ���Υ�٥�������
	//---------------------------------------------------
	function countAroundLevel($island, $x, $y, $kind, $range) {
	global $init;
	// �ϰ�����Ϸ��������
		$land = $island['land'];
		$landValue = $island['landValue'];
	$count = 0;
	for($i = 0; $i < $range; $i++) {
		$sx = $x + $init->ax[$i];
		$sy = $y + $init->ay[$i];

		// �Ԥˤ�����Ĵ��
		if((($sy % 2) == 0) && (($y % 2) == 1)) {
		$sx--;
		}

		if(($sx < 0) || ($sx >= $init->islandSize) ||
		 ($sy < 0) || ($sy >= $init->islandSize)) {
		// �ϰϳ��ξ��

		} else {
		// �ϰ���ξ��
		if($land[$sx][$sy] == $kind) {
			$count += $landValue[$sx][$sy];
		}
		}
	}
	return $count;
	}
	//---------------------------------------------------
	// �ϰ�����Ϸ����ͤǥ������
	//---------------------------------------------------
	function countAroundValue($island, $x, $y, $kind, $lv, $range) {
	global $init;

	$land = $island['land'];
	$landValue = $island['landValue'];
	$count = 0;

	for($i = 0; $i < $range; $i++) {
		$sx = $x + $init->ax[$i];
		$sy = $y + $init->ay[$i];

		# �Ԥˤ�����Ĵ��
		if((($sy % 2) == 0) && (($y % 2) == 1)) {
			$sx--;
		}

		if(($sx < 0) || ($sx >= $init->islandSize) ||
			 ($sy < 0) || ($sy >= $init->islandSize)) {
			# �ϰϳ��ξ��
		} else {
			# �ϰ���ξ��
			if($land[$sx][$sy] == $kind && $landValue[$sx][$sy] == $lv) {
				$count++;
			}
		}
	}
	return $count;
	}
	//---------------------------------------------------
	// �Ϸ��θƤ���
	//---------------------------------------------------
	function landName($land, $lv) {
	global $init;
	switch($land) {
	case $init->landSea:
		if($lv == 1) {
			return '����';
		} elseif($lv == 2) {
			return $init->shipName[0];
		} elseif($lv == 3) {
			return $init->shipName[1];
		} elseif($lv == 255) {
			return '��±��';
		} else {
			return '��';
		}
		break;
	case $init->landPort:
		return '��';
	case $init->landSeaSide:
		return '����';
	case $init->landSeaResort:
		// ���β�
		$n;
		if($lv < 30) {
			$n = '���β�';
		} elseif($lv < 100) {
			$n = '̱��';
		} else {
			$n = '�꥾���ȥۥƥ�';
		}
		return $n;
	case $init->landWaste:
		return '����';
	case $init->landPlains:
		return 'ʿ��';
	case $init->landTown:
		if($lv < 30) {
		return '¼';
		} elseif($lv < 100) {
		return 'Į';
		} else {
		return '�Ի�';
		}
	case $init->landForest:
		return '��';
	case $init->landFarm:
		return '����';
	case $init->landFactory:
		return '����';
	case $init->landBase:
		return '�ߥ��������';
	case $init->landDefence:
		return '�ɱһ���';
	case $init->landMountain:
		return '��';
	case $init->landMonster:
		$monsSpec = Util::monsterSpec($lv);
		return $monsSpec['name'];
	case $init->landSbase:
		return '�������';
	case $init->landOil:
		return '��������';
	case $init->landMonument:
		return $init->monumentName[$lv];
	case $init->landHaribote:
		return '�ϥ�ܥ�';
	case $init->landPark:
		return 'ͷ����';

	}
	}
}
// �͸������
function popComp($x, $y) {
	if($x['pop'] == $y['pop']) return 0;
	return ($x['pop'] > $y['pop']) ? -1 : 1;
}


?>