<?php
/*******************************************************************

  Ȣ����磲 for PHP

  
  $Id$

*******************************************************************/
//error_reporting(E_ALL);
if(GZIP == true) {
  // gzip����ž����
  require_once "HTTP/Compress.php";
  $http = new HTTP_Compress;
}

//--------------------------------------------------------------------
class HTML {

  //---------------------------------------------------
  // HTML �إå�����
  //---------------------------------------------------
  function header($data = "", $viewmode = '') {
    global $init,$mode,$hako;

    // ����ž��
    if(GZIP == true) {
      global $http;
      $http->start();
    }
    $css = (empty($data['defaultSkin'])) ? $init->cssList[0] : $data['defaultSkin'];
    if (($mode == "conf") || ($data['admin_mente'] == "yes")){
			$navi_tag = "[<a href=\"{$init->mainFileUrl}\">�ȥå�</a>]";
		} else {
			$navi_tag = "[<a href=\"{$init->mainFileUrl}?mode=conf\">�����Ͽ�������ѹ�</a>]";
    }
    if ($init->manual) $navi_tag .= " [<a href=\"{$init->manual}\">�ޥ˥奢��</a>]";
    $xoops_url = XOOPS_URL;
    print <<<END
<div class="hako_body">
<link rel="stylesheet" type="text/css" href="{$init->cssDir}/{$css}">
END;

	if ($viewmode !== 'targetView') {
		print <<<END
<div id="LinkHeader">
<table style="border:none;width:100%;"><tr>
<td align="left">{$navi_tag}</td>
<td align="right">
<a href="http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html" target="_blank">Ȣ����祹����ץ����۸�</a>
-
<a href="http://scrlab.g-7.ne.jp/" target="_blank">[PHP]</a>
-
<a href="http://xoops.hypweb.net/" target="_blank">[XOOPS]</a>
</td>
</tr></table>
</div>
<hr />
END;
		if (!empty($init->optionHeader)) {
			echo $init->optionHeader;
		}
	}
	
	print <<<END
<table class='hako' style="border:none;width:100%;">
<tr>
<td>
END;
	include_once("hako_js.php");
  }
  //---------------------------------------------------
  // HTML �եå�����
  //---------------------------------------------------
  function footer() {
    global $init;
    print <<<END
<hr />
</td>
</tr>
</table>
</div>
END;

    if(GZIP == true) {
      global $http;
      $http->output();
    }
  }
  //---------------------------------------------------
  // �ǽ��������� �� �������󹹿��������
  //---------------------------------------------------
  function lastModified($hako) {
    global $init;
    $timeString = date("Yǯm��d����H��", $hako->islandLastTime);
    $server_time = time();
    print <<<END
<h2 class="lastModified">�ǽ�������{$hako->islandTurn} : $timeString
<span style="font-weight: normal;">
<span id="remain_time"><layer name="remain_time"></layer></span>
<script type="text/javascript"> <!--
	var nextTime = $hako->islandLastTime + $init->unitTime;
	var ClientTime = new Date();
	ClientTime = Math.floor(ClientTime / 1000);
	nextTime = nextTime + (ClientTime-{$server_time});
	remainTime(nextTime);
//-->
</script>
</span>
</h2>

END;
   }
}
//--------------------------------------------------------------------
class HtmlTop extends HTML {
  //---------------------------------------------------
  // �ԣϣХڡ���
  //---------------------------------------------------
  function main($hako, $data) {
    global $init;

	$find_island = ($hako->islandNumber < $init->maxIsland)? "<a href=\"{$GLOBALS['THIS_FILE']}?mode=conf\">��ʬ�����õ���Ƥߤ�(����)</a>" : "��������ζ����Ϥ���ޤ���";
	print "<table class='hako' style=\"width:100%;\" border=\"0\" cellspacing=\"5\"><tr><td style=\"width:40%;\" nowrap>";
    print "<h1>{$init->title}</h1>";
    print "<h2 class=\"Turn\">������{$hako->islandTurn}����$find_island</h2>";
    print "</td><td style=\"width:40%;\" nowrap>".nl2br($init->topMessege)."</td></table>";
    
    if(DEBUG == true) {
      print <<<END
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="debugTurn">
<input type="submit" value="�������ʤ��">
</form>

END;
	}
	
	print "<hr />";

    // �ǽ��������� �� �������󹹿��������
    $this->lastModified($hako);
	// ���å����ǡ�����������å��ܥå����ε����ͤ����
    if(empty($data['defaultDevelopeMode']) || $data['defaultDevelopeMode'] == "cgi") {
      $radio = "checked"; $radio2 = "";
    } else {
      $radio = ""; $radio2 = "checked";
    }

    // xoops //
    global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "";
	}
	// ---- //

	if ($name) {
    print <<<END
<div id="MyIsland">
<h2>��ʬ�����</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
���ʤ������̾���ϡ�<br />
<select name="islandid">
$hako->islandList
</select>
<input type="hidden" name="mode" value="owner">
<script>
<!--
document.write('<input type=\"hidden\" name=\"developemode\" value=\"java\">');
-->
</script>
<noscript><input type="hidden" name="developemode" value="cgi"></noscript>
�� <input type="submit" value="��ȯ���˹Ԥ�">
</form>
</div>
<hr />
END;
	}
	print <<<END
<div id="IslandView">
<h2>����ξ���</h2>
<p>
���̾���򥯥�å�����ȡ�<strong>�Ѹ�</strong>���뤳�Ȥ��Ǥ��ޤ���
��[ <a href="{$GLOBALS['THIS_FILE']}#resent">�Ƕ�ν����</a> ]
��[ <a href="{$GLOBALS['THIS_FILE']}#found">ȯ���ε�Ͽ</a> ]
</p>
<table class='hako' border="1" style="width:100%;">
<tr>
<th {$init->bgTitleCell}>{$init->tagTH_}���{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}��{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}�͸�{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}����{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}���{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}����{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}���쵬��{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}���쵬��{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}�η��쵬��{$init->_tagTH}</th>
</tr>

END;
    for($i = 0; $i < $hako->islandNumber; $i++) {
      $island = $hako->islands[$i];
      $j = $i + 1;
      $id    = $island['id'];
      $pop   = $island['pop'] . $init->unitPop;
      $area  = $island['area'] . $init->unitArea;
      $money = Util::aboutMoney($island['money']);
      $food  = $island['food'] . $init->unitFood;
      $farm  = ($island['farm'] <= 0) ? "��ͭ����" : $island['farm'] * 10 . $init->unitPop;
      $factory  = ($island['factory'] <= 0) ? "��ͭ����" : $island['factory'] * 10 . $init->unitPop;
      $mountain = ($island['mountain'] <= 0) ? "��ͭ����" : $island['mountain'] * 10 . $init->unitPop;
      $comment  = $island['comment'];
      $comment_turn = $island['comment_turn'];
      $monster = '';
      if($island['monster'] > 0) {
        $monster = "<strong class=\"monster\">[����{$island['monster']}��]</strong>";
      }
      
      $name = "";
      if($island['absent']  == 0) {
        $name = "{$init->tagName_}{$island['name']}��{$init->_tagName}";
      } else {
        $name = "{$init->tagName2_}{$island['name']}��({$island['absent']}){$init->_tagName2}";
      }
      if(!empty($island['owner'])) {
        $owner = $island['owner'];
      } else {
        $owner = "������";
      }
      
      $prize = $island['prize'];
      $prize = $hako->getPrizeList($prize);

      if($init->commentNew > 0 && ($comment_turn + $init->commentNew) > $hako->islandTurn) {
        $comment .= " <span class=\"new\">New</span>";
      }
      
      print "<tr>\n";
      print "<th {$init->bgNumberCell} rowspan=\"2\">{$init->tagNumber_}$j{$init->_tagNumber}</th>\n";
      print "<td {$init->bgNameCell} rowspan=\"2\"><a href=\"{$GLOBALS['THIS_FILE']}?Sight={$id}\">{$name}</a> {$monster}<br />\n{$prize}</td>\n";

      print "<td {$init->bgInfoCell}>$pop</td>\n";
      print "<td {$init->bgInfoCell}>$area</td>\n";
      print "<td {$init->bgInfoCell}>$money</td>\n";
      print "<td {$init->bgInfoCell}>$food</td>\n";
      print "<td {$init->bgInfoCell}>$farm</td>\n";
      print "<td {$init->bgInfoCell}>$factory</td>\n";
      print "<td {$init->bgInfoCell}>$mountain</td>\n";
      print "</tr>\n";
      print "<tr>\n";
      print "<td {$init->bgCommentCell} colspan=\"7\">{$init->tagTH_}{$owner}��{$init->_tagTH}$comment</td>\n";
      print "</tr>\n";
    }
    print "</table>\n</div>\n";
    print "<hr />\n";
    $this->logprintTop();
    $this->historyprint();
  }
  //---------------------------------------------------
  // �����Ͽ������
  //---------------------------------------------------
  function regist(&$hako) {
    global $init;
    $this->newDiscovery($hako->islandNumber);
    $this->changeIslandInfo($hako->islandList);
    $this->changeOwnerName($hako->islandList);
    $this->setStyleSheet();
  }
  //---------------------------------------------------
  // ���������õ��
  //---------------------------------------------------
  function newDiscovery($number) {
    global $init;
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
		$email = $xoopsUser->email();
	} else {
		$name = "";
	}
	if ($init->mailUse) {
		$mailTo_tag="<input id=\"hako_tomail\" type=\"checkbox\" name=\"tomail\" value=\"1\"><label for=\"hako_tomail\"> {$email} �إ᡼�����Τ��롣</label><br />
	���� �����å�����ȥ����󤴤ȤΥ��٥�Ȥ�᡼������Τ��ޤ���<br /><br />";
	} else {
		$mailTo_tag='';
	}
	// ---- //
    print "<div id=\"NewIsland\">\n";
    print "<h2>���������õ��</h2>\n";
    if ($name){
	    if($number < $init->maxIsland) {
	      print <<<END
	<form action="{$GLOBALS['THIS_FILE']}" method="post">
	�ɤ��̾����Ĥ���ͽ�ꡩ<br />
	<input type="text" name="islandname" size="32" maxlength="32">��<br />
	���ʤ��Τ�̾���ϡ�(��ά��)<br />
	<input type="text" name="ownername" size="32" maxlength="32" value="{$name}"><br /><br />
	{$mailTo_tag}
	<input type="hidden" name="email" size="32" maxlength="32" value="{$email}">
	<input type="hidden" name="mode" value="new">
	������<input type="submit" value="õ���˹Ԥ�">
	</form>
END;
	    } else {
			print "��ο���������Ǥ�������������Ͽ�Ǥ��ޤ���\n";
	    }
	} else {
		print "�����󤹤�����õ���ޤ���<br /<br />[ <a href=\"../../../register.php\">�����桼������Ͽ</a> ]\n";
	}
    print "</div>\n";
    print "<hr />\n";
  }
  //---------------------------------------------------
  // ���̾���ȥѥ���ɤ��ѹ�
  //---------------------------------------------------
  function changeIslandInfo($islandList = "") {
    global $init;
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "";
	}
	// ---- //
	if ($name) {
    print <<<END
<div id="ChangeInfo">
<h2>���̾�����ѹ�</h2>
<p>
(���)̾�����ѹ��ˤ�500���ߤ�����ޤ���
</p>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
�ɤ���Ǥ�����<br />
<select NAME="islandid">
$islandList
</select>
<br />
�ɤ��̾�����Ѥ��ޤ�����(�ѹ�������Τ�)<br />
<input type="text" name="islandname" size="32" maxlength="32">��<br />
<input type="hidden" name="mode" value="change">
<input type="submit" value="�ѹ�����">
</form>
</div>
<hr />

END;
	}
  }
  //---------------------------------------------------
  // �����ʡ�̾���ѹ�
  //---------------------------------------------------
  function changeOwnerName($islandList = "") {
    global $init;
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "";
	}
	// ---- //
	if ($name){
    print <<<END
<div id="ChangeOwnerName">
<h2>�����ʡ�̾���ѹ�</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
�ɤ���Ǥ�����<br />
<select name="islandid">
{$islandList}
</select>
<br />
�����������ʡ�̾�ϡ�<br />
<input type="text" name="ownername" size="32" maxlength="32"><br />
<input type="hidden" name="mode" value="ChangeOwnerName">
<input type="submit" value="�ѹ�����">
</form>
</div>
END;
	}
  }
  //---------------------------------------------------
  // �������륷���Ȥ�����
  //---------------------------------------------------
  function setStyleSheet() {
    global $init;
    $styleSheet;
    for($i = 0; $i < count($init->cssList); $i++) {
      $styleSheet .= "<option value=\"{$init->cssList[$i]}\">{$init->cssList[$i]}</option>\n";
    }
    print <<<END
<div id="HakoSkin">
<h2>�������륷���Ȥ�����</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<select name="skin">
$styleSheet
</select>
<input type="hidden" name="mode" value="skin">
<input type="submit" value="����">
</form>
</div>
<hr />

END;
  }
  //---------------------------------------------------
  // �Ƕ�ν����
  //---------------------------------------------------
  function logprintTop() {
    global $init;
    print "<div id=\"RecentlyLog\"><a name=\"resent\"></a>\n";
    print "<h2>�Ƕ�ν����</h2>\n";
    for($i = 0; $i < $init->logTopTurn; $i++) {
      LogIO::logFileprint($i, 0, 0);
    }
    print "</div>\n";
  }
  //---------------------------------------------------
  // ȯ���ε�Ͽ
  //---------------------------------------------------
  function historyprint() {
    print "<div id=\"HistoryLog\"><a name=\"found\"></a>\n";
    print "<h2>ȯ���ε�Ͽ</h2>";
    LogIO::historyprint();
    print "</div>\n";
  }
}
//------------------------------------------------------------------
class HtmlMap extends HTML {
  //---------------------------------------------------
  // ��ȯ����
  //---------------------------------------------------
  function owner($hako, $data) {
    global $init;
    $id     = $data['ISLANDID'];
    $number = $hako->idToNumber[$id];
    $island = $hako->islands[$number];

    // �ѥ���ɥ����å�
    if(!Util::checkPassword($island['password'], $data['PASSWORD'])){
      Error::wrongPassword();
      return;
    }
    $this->tempOwer($hako, $data, $number);
    
    if($init->useBbs) {
      print "<div id=\"localBBS\">\n";
      $this->lbbsHead($island);
      $this->lbbsInputOW($island, $data);
      $this->lbbsContents($island);
      print "</div>\n";
    }
    $this->islandRecent($island, 1);
  }

  //---------------------------------------------------
  // �Ѹ�����
  //---------------------------------------------------
  function visitor($hako, $data) {
    global $init;
    $id     = $data['ISLANDID'];
    $number = $hako->idToNumber[$id];
    $island = $hako->islands[$number];
    
    // xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "";
	}
	// ---- //
	
    print <<<END
<div align="center">
{$init->tagBig_}{$init->tagName_}��{$island['name']}���{$init->_tagName}�ؤ褦��������{$init->_tagBig}<br />
{$GLOBALS['BACK_TO_TOP']}<br />
</div>
END;
    //��ȯ�ܥ���
    if(Util::checkPassword($island['password'], $name)){
		// ���å����ǡ�����������å��ܥå����ε����ͤ����
		if(empty($data['defaultDevelopeMode']) || $data['defaultDevelopeMode'] == "cgi") {
			$radio = "checked"; $radio2 = "";
		} else {
			$radio = ""; $radio2 = "checked";
		}
    	$kaihatu_b_tag=<<<END
<div align="center">
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="mode" value="owner">
<script>
<!--
document.write('<input type=\"hidden\" name=\"developemode\" value=\"java\">');
-->
</script>
<noscript><input type="hidden" name="developemode" value="cgi"></noscript>
<input type="submit" value="��ȯ���˹Ԥ�">
</form>
</div>
END;
    } else {
		$kaihatu_b_tag = "";
	}
    // �ǽ��������� �� �������󹹿��������
    $this->lastModified($hako);
	// ����ե��᡼�����
    $this->islandInfo($island, $number, 0);
    print "<hr />";
	// �ޥåפȥ˥塼��
    print "<table class='hako'  style=\"width:100%;\"><tr><td valign=\"top\">";
    $this->islandMap($hako, $island, 0);
	// ��ȯ�ܥ���
    print $kaihatu_b_tag;
    print "</td><td valign=\"top\" style=\"width:100%;\">";
    $this->islandRecent_XTurn($island, 0);
    // ¾�����
    $otherList = preg_replace("/<option value=(.*?)".$island['name']."��<\/option>/m","",$hako->islandList);
	print <<<END
<h2>���ץ���ʥ�ĥ���</h2>
<div align="center"><form action="{$GLOBALS['THIS_FILE']}" method="get">
�̤��硧<select name="Sight">$otherList</select><input type="submit" value="�Ѹ���">
</form></div>
END;
    print "</td></tr></table>";

    if($init->useBbs) {
      print "<div id=\"localBBS\">\n";
      $this->lbbsHead($island);
      $this->lbbsInput($island, $data);
      $this->lbbsContents($island);
      print "</div>\n";
    }
    $this->islandRecent($island, 0);
  }
  //---------------------------------------------------
  // ��ξ���
  //---------------------------------------------------
  function islandInfo($island, $number = 0, $mode = 0) {
    global $init;
    $rank = $number + 1;
    $pop   = $island['pop'] . $init->unitPop;
    $area  = $island['area'] . $init->unitArea;
    $money = ($mode == 0) ? Util::aboutMoney($island['money']) : "{$island['money']}{$init->unitMoney}";
    $food  = $island['food'] . $init->unitFood;
    $farm  = ($island['farm'] <= 0) ? "��ͭ����" : $island['farm'] * 10 . $init->unitPop;
    $factory  = ($island['factory'] <= 0) ? "��ͭ����" : $island['factory'] * 10 . $init->unitPop;
    $mountain = ($island['mountain'] <= 0) ? "��ͭ����" : $island['mountain'] * 10 . $init->unitPop;
    $comment  = $island['comment'];

    print <<<END
<div id="islandInfo">
<table class='hako' border="1">
<tr>
<th {$init->bgTitleCell}>{$init->tagTH_}���{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}�͸�{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}����{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}���{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}����{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}���쵬��{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}���쵬��{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}�η��쵬��{$init->_tagTH}</th>
</tr>
<tr>
<th {$init->bgNumberCell}>{$init->tagNumber_}$rank{$init->_tagNumber}</th>
<td {$init->bgInfoCell}>$pop</td>
<td {$init->bgInfoCell}>$area</td>
<td {$init->bgInfoCell}>$money</td>
<td {$init->bgInfoCell}>$food</td>
<td {$init->bgInfoCell}>$farm</td>
<td {$init->bgInfoCell}>$factory</td>
<td {$init->bgInfoCell}>$mountain</td>
</tr>
<tr>
<td colspan="8" {$init->bgCommentCell}>$comment</td>
</tr>
</table>
</div>

END;
  }
  //---------------------------------------------------
  // �Ϸ�����
  // $mode = 1 -- �ߥ�������Ϥʤɤ�ɽ��
  //---------------------------------------------------
  function islandMap($hako, $island, $mode = 0) {
    global $init;
    $land      = $island['land'];
    $landValue = $island['landValue'];
    $command   = $island['command'];
    if($mode === 1) {
      for($i = 0; $i < $init->commandMax; $i++) {
        $j = $i + 1;
        $com = $command[$i];
        if($com['kind'] < $init->lastCom) {
          $comStr[$com['x']][$com['y']] .=
            "[{$j}]{$init->comName[$com['kind']]} ";
        }
      }
    }

    print "<div id=\"islandMap\" align=\"center\">";
	print <<<EOD
<style type=text/css>
/* ������δ������� */
.infwin  { position:absolute; visibility:hidden; filter: alpha(opacity=75)}
/* �������ɽ������ */
.inftd   { font-size:10pt; text-align:center; }
</style>
<div id="NaviView" class=infwin><layer name="NaviView" class=infwin></layer></div>
EOD;
    print "<table class='hako' border=\"1\"><tr><td style=\"background-color:blue;\">\n";
    print "<img src=\"{$init->imgDir}/xbar.gif\" width=\"400\" height=\"16\" alt=\"\" /><br />\n";
    for($y = 0; $y < $init->islandSize; $y++) {
      if($y % 2 == 0) { print "<img src=\"{$init->imgDir}/space{$y}.gif\" width=\"16\" height=\"32\" alt=\"{$y}\" />"; }

      for($x = 0; $x < $init->islandSize; $x++) {
        $hako->landString($island, $land[$x][$y], $landValue[$x][$y], $x, $y, $mode, $comStr[$x][$y]);
      }
      
      if($y % 2 == 1) { print "<img src=\"{$init->imgDir}/space{$y}.gif\" width=\"16\" height=\"32\" alt=\"{$y}\" />"; }

      print "<br />";
    }
    print "</td></tr></table></div>\n";
    //print "<div id=\"NaviView\"></div>";
  }
  //---------------------------------------------------
  // �Ѹ����̿�
  //---------------------------------------------------
  function lbbsHead($island) {
    global $init;
    print <<<END
<hr />
<h2>{$island['name']}��{$init->_tagName}�Ѹ����̿�</h2>

END;
  }
  //---------------------------------------------------
  // �Ѹ����̿� ������ʬ
  //---------------------------------------------------
  function lbbsInput($island, $data) {
    global $init;
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "������";
	}
	// ---- //
	//if(!$data['defaultName']) $data['defaultName']=$name;
    print <<<END
<div align="center">
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<table class='hako' border="1" style="width:100%;">
<TR>
<th>̾��</th>
<th>����</th>
<th>ư��</th>
</tr>
<tr>
<td style="width:20%;" nowrap><input type="text" size="20" maxlength="32" name="lbbsname" value="{$data['defaultName']}">@$name</td>
<td style="width:80%;"><input style="width:100%" type="text" size="80" name="lbbsmessage"></td>
<td>
<input type="hidden" name="mode" value="lbbs">
<input type="hidden" name="lbbsMode" value="0">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="developemode" value="{$data['DEVELOPEMODE']}">
<input type="submit" value="��Ģ����"></TD>
</tr>
</table>
</form>
</div>

END;
  }
  //---------------------------------------------------
  // �Ѹ����̿� ������ʬ ��������
  //---------------------------------------------------
  function lbbsInputOW($island, $data) {
    global $init;
	// xoops //
	global $xoopsUser;
	if ($xoopsUser){
		$name = $xoopsUser->uname();
	} else {
		$name = "������";
	}
	// ---- //
	//if(!$data['defaultName']) $data['defaultName']=$name;
	
    print <<<END
<div align="center">
<table class='hako' border="1" style="width:100%;">
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<tr>
<th>̾��</th>
<th colspan="2">����</th>
</tr>
<tr>
<td style="width:20%" nowrap><input type="text" size="20" maxlength="32" name="lbbsname" VALUE="{$data['defaultName']}">@$name</TD>
<td  style=\"text-align:left;\" colspan="2"><input style="width:100%" type="text" size="80" name="lbbsmessage"></td>
</tr>
<tr>
<th colspan="2">ư��</th>
</tr>
<tr>
<td align="right">
<input type="hidden" name="mode" value="lbbs">
<input type="hidden" name="lbbsMode" value="1">
<input type="hidden" name="password" value="{$data['defaultPassword']}">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="developemode" value="{$data['DEVELOPEMODE']}">
<input type="submit" value="��Ģ����">
</form>
</td>
<td align="right">
<form action="{$GLOBALS['THIS_FILE']}" method="post">
�ֹ�
<select name="number">

END;
    
    // ȯ���ֹ�
    for($i = 0; $i < $init->lbbsMax; $i++) {
      $j = $i + 1;
      print "<option value=\"{$i}\">{$j}</option>\n";
    }
    print <<<END
</select>
<input type="hidden" name="mode" value="lbbs">
<input type="hidden" name="lbbsMode" value="2">
<input type="hidden" name="password" value="{$data['defaultPassword']}">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="developemode" value="{$data['DEVELOPEMODE']}">
<input type="submit" value="�������">
</form>
</td>
</tr>
</table>
</div>

END;
  }
  //---------------------------------------------------
  // �Ѹ����̿� �񤭹��ޤ줿���Ƥ����
  //---------------------------------------------------
  function lbbsContents($island) {
    global $init;
    $lbbs = $island['lbbs'];
    print <<<END
<div align="center">
<table class='hako' border="1" style="width:100%;">
<tr>
<th style="width:3em;">�ֹ�</th>
<th>��Ģ����</th>
</tr>

END;
    for($i = 0; $i < $init->lbbsMax; $i++) {
      $j = $i + 1;
      $line = $lbbs[$i];
      list($mode, $turn, $message) = split(">", $line);
      print "<tr><th>{$init->tagNumber_}{$j}{$init->_tagNumber}</th>";
      if($mode == 0) {
        // �Ѹ���
        print "<td style=\"text-align:left;\">{$init->tagLbbsSS_}{$turn} &gt; {$message}{$init->_tagLbbsSS}</td></tr>\n";
      } else {
        // ���
        print "<td style=\"text-align:left;\">{$init->tagLbbsOW_}{$turn} &gt; {$message}{$init->_tagLbbsOW}</td></tr>\n";
      }
      
    }
    print "</table></div>\n";
  }
  //---------------------------------------------------
  // ��ζᶷ
  //---------------------------------------------------
  function islandRecent($island, $mode = 0) {
    global $init;
    print "<hr />\n";
    print "<div id=\"RecentlyLog\">\n";
    print "<h2>{$island['name']}��{$init->_tagName}�ζᶷ</h2>\n";
    for($i = 0; $i < $init->logMax; $i++) {
      LogIO::logFileprint($i, $island['id'], $mode, $island['name']);
    }
    print "</div>\n";
  }
  //---------------------------------------------------
  // ��Υ˥塼��®��ʺǶ��X�����󡡥ǥե����8�������
  //---------------------------------------------------
  function islandRecent_XTurn($island, $mode = 0, $x_turn = 8) {
    global $init;
    print "<div id=\"RecentlyLog\">\n";
    print "<h2>{$island['name']}��{$init->_tagName}�˥塼��®��</h2>\n";
    for($i = 0; $i < $x_turn+1; $i++) {
      LogIO::logFileprint($i, $island['id'], $mode, $island['name']);
    }
    print "</div>\n";
  }
  //---------------------------------------------------
  // ��ȯ����
  //---------------------------------------------------
  function tempOwer($hako, $data, $number = 0) {
    global $init;
    $island = $hako->islands[$number];

    // �ǽ��������� �� �������󹹿��������
    $this->lastModified($hako);

    $width  = $init->islandSize * 32 + 50;
    $height = $init->islandSize * 32 + 100;
    $defaultTarget = ($init->targetIsland == 1) ? $island['id'] : $hako->defaultTarget;
    print <<<END
<script type="text/javascript">
<!--
var w;
var p = $defaultTarget;
function ps(x, y) {
  document.InputPlan.pointx.options[x].selected = true;
  document.InputPlan.pointy.options[y].selected = true;
  return true;
}

function ns(x) {
  document.InputPlan.number.options[x].selected = true;
  return true;
}

function settarget(part){
  p = part.options[part.selectedIndex].value;
}
function targetopen() {
  w = window.open("{$GLOBALS['THIS_FILE']}?mode=targetView&target=" + p, "","width={$width},height={$height},scrollbars=1,resizable=1,toolbar=1,menubar=1,location=1,directories=0,status=1");
}


//-->
</script>
<div align="center"><a name="hako_top"></a>
{$init->tagBig_}{$init->tagName_}{$island['name']}��{$init->_tagName}��ȯ�ײ�{$init->_tagBig}<br />
{$GLOBALS['BACK_TO_TOP']}<br />
</div>

END;
    $this->islandInfo($island, $number, 1);
    print <<<END
<div align="center">
<table class='hako' border="1">
<tr>
<td {$init->bgInputCell}>
<div align="center">
<form action="{$GLOBALS['THIS_FILE']}" method="post" name="InputPlan">
<input type="hidden" name="mode" value="command">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="password" value="{$data['defaultPassword']}">
<input type="submit" value="�ײ�����">
<hr />
<strong>�ײ��ֹ�</strong>
<select name="number">

END;
    // �ײ��ֹ�
    for($i = 0; $i < $init->commandMax; $i++) {
      $j = $i + 1;
      print "<option value=\"{$i}\">{$j}</option>";
    }
    print <<<END
</select><br />
<hr />
<strong>��ȯ�ײ�</strong><br />
<select name="command">

END;
    // ���ޥ��
    for($i = 0; $i < $init->commandTotal; $i++) {
      $kind = $init->comList[$i];
      $cost = $init->comCost[$kind];
      if($cost == 0) {
        $cost = '̵��';
      } elseif($cost < 0) {
        $cost  = - $cost;
        $cost .= $init->unitFood;
      } else {
        $cost .= $init->unitMoney;
      }
      if($kind == $data['defaultKind']) {
        $s = 'selected';
      } else {
        $s = '';
      }
      print "<option value=\"{$kind}\" {$s}>{$init->comName[$kind]}({$cost})</option>\n";
    }
    print <<<END
</select>
<hr />
<strong>��ɸ(</strong>
<select name="pointx">

END;
    for($i = 0; $i < $init->islandSize; $i++) {
      if($i == $data['defaultX']) {
        print "<option value=\"{$i}\" selected>{$i}</option>\n";
      } else {
        print "<option value=\"{$i}\">{$i}</option>\n";
      }
    }
    print "</select>, <select name=\"pointy\">";
    for($i = 0; $i < $init->islandSize; $i++) {
      if($i == $data['defaultY']) {
        print "<option value=\"{$i}\" selected>{$i}</option>\n";
      } else {
        print "<option value=\"{$i}\">{$i}</option>\n";
      }
    }
    print <<<END
</select><strong>)</strong>
<hr />
<strong>����</strong>
<select name="amount">

END;
     for($i = 0; $i < 100; $i++)
       print "<option value=\"{$i}\">{$i}</option>\n";

     print <<<END
</select>
<hr />
<strong>��ɸ����</strong><br />
<select name="targetid" onchange="settarget(this);">
$hako->targetList
</select>
<input type="button" value="��ɸ��ª" onClick="javascript: targetopen();">
<hr />
<strong>ư��</strong><br />
<input type="radio" name="commandmode" id="insert" value="insert" checked><label for="insert">����</label>
<input type="radio" name="commandmode" id="write" value="write"><label for="write">���</label><BR>
<input type="radio" name="commandmode" id="delete" value="delete"><label for="delete">���</label>
<hr />
<input type="hidden" name="developemode" value="cgi">
<input type="submit" value="�ײ�����">
</form>
</div>
</td>
<td {$init->bgMapCell}>

END;
    $this->islandMap($hako, $island, 1);    // ����Ͽޡ���ͭ�ԥ⡼��
    print <<<END
</td>
<td {$init->bgCommandCell} nowrap>
END;
    $command = $island['command'];
    for($i = 0; $i < $init->commandMax; $i++) {
      $this->tempCommand($i, $command[$i], $hako);
    }
    if ($init->mailUse){
	    if ($island['tomail']){
			$ToMailBoxTag="<input id=\"hako_tomail\" type=\"checkbox\" name=\"tomail\" value=\"1\" checked>";
		} else {
			$ToMailBoxTag="<input id=\"hako_tomail\" type=\"checkbox\" name=\"tomail\" value=\"1\">";
		}
		$mailTo_tag = "<label for=\"hako_tomail\">�᡼�����Ρ�{$ToMailBoxTag}�ʥ����󤴤Ȥ˥��٥�Ȥ�᡼������Τ��ޤ�����</label><br />";
	} else {
		$mailTo_tag = "";
	}
	
    print <<<END
</td>
</tr>
</table>
</div>
<hr />
<div id='CommentBox'>
<h2>�����ȹ���</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="password" value="{$data['defaultPassword']}">
<input type="hidden" name="mode" value="comment">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="developemode" value="cgi">
{$mailTo_tag}
������<input type="text" name="message" size="80" value="{$island['comment']}">
<input type="submit" value="�����ȹ���">
</form>
</div>

END;

  }
  //---------------------------------------------------
  // ���ϺѤߥ��ޥ��ɽ��
  //---------------------------------------------------
  function tempCommand($number, $command, $hako) {
    global $init;

    $kind   = $command['kind'];
    $target = $command['target'];
    $x      = $command['x'];
    $y      = $command['y'];
    $arg    = $command['arg'];

    $comName = "{$init->tagComName_}{$init->comName[$kind]}{$init->_tagComName}";
    $point   = "{$init->tagName_}({$x},{$y}){$init->_tagName}";
    $target  = $hako->idToName[$target];
    if(empty($target)) {
      $target = "̵��";
    }
    $target = "{$init->tagName_}{$target}��{$init->_tagName}";
    $value = $arg * $init->comCost[$kind];
    if($value == 0) {
      $value = $init->comCost[$kind];
    }
    if($value < 0) {
      $value = -$value;
      $value = "{$value}{$init->unitFood}";
    } else {
      $value = "{$value}{$init->unitMoney}";
    }
    $value = "{$init->tagName_}{$value}{$init->_tagName}";

    $j = sprintf("%02d��", $number + 1);

    print "<a href=\"javascript:void(0);\" onclick=\"ns({$number})\">{$init->tagNumber_}{$j}{$init->_tagNumber}";

    switch($kind) {
    case $init->comDoNothing:
    case $init->comGiveup:
    case $init->comPropaganda:
      $str = "{$comName}";
      break;
    case $init->comMissileNM:
    case $init->comMissilePP:
    case $init->comMissileST:
    case $init->comMissileLD:
      // �ߥ������
      $n = ($arg == 0) ? '̵����' : "{$arg}ȯ";
      $str = "{$target}{$point}��{$comName}({$init->tagName_}{$n}{$init->_tagName})";
      break;
    case $init->comSendMonster:
      // �����ɸ�
      $str = "{$target}��{$comName}";
      break;
    case $init->comSell:
      // ����͢��
      $str ="{$comName}{$value}";
      break;
    case $init->comMoney:
    case $init->comFood:
      // ���
      $str = "{$target}��{$comName}{$value}";
      break;
    case $init->comDestroy:
      // ����
      if($arg != 0) {
        $str = "{$point}��{$comName}(ͽ��{$value})";
      } else {
        $str = "{$point}��{$comName}";
      }
      break;
    case $init->comFarm:
    case $init->comFactory:
    case $init->comMountain:
      // ����դ�
      if($arg == 0) {
        $str = "{$point}��{$comName}";
      } else {
        $str = "{$point}��{$comName}({$arg}��)";
      }      
      break;
    default:
      // ��ɸ�դ�
      $str = "{$point}��{$comName}";
    }

    print "{$str}</a><br />";
  }
  //---------------------------------------------------
  // ������ȯ��������
  //---------------------------------------------------
  function newIslandHead($name) {
    global $init;
    print <<<END
<div align="center">
{$init->tagBig_}���ȯ�����ޤ�������{$init_tagBig}<br />
{$init->tagBig_}{$init->tagName_}��{$name}���{$init->_tagName}��̿̾���ޤ���{$init->_tagBig}<br />
{$GLOBALS['BACK_TO_TOP']}<br />
</div>
END;
  }
  //---------------------------------------------------
  // ��ɸ��ª�⡼��
  //---------------------------------------------------
  function printTarget($hako, $data) {
    global $init;
    // id�������ֹ�����
    $id     = $data['ISLANDID'];
    $number = $hako->idToNumber[$id];
    // �ʤ��������礬�ʤ����
    if($number < 0 || $number > $hako->islandNumber) {
      Error::problem();
      return;
    }
    $island = $hako->islands[$number];

print <<<END
<script type="text/javascript">
<!--
function ps(x, y) {
  window.opener.document.InputPlan.pointx.options[x].selected = true;
  window.opener.document.InputPlan.pointy.options[y].selected = true;
  return true;
}
//-->
</script>

<div align="center">
{$init->tagBig_}{$init->tagName_}{$island['name']}��{$init->_tagName}{$init->_tagBig}<br />
</div>

END;

    //����Ͽ�
    $this->islandMap($hako, $island, 2);

  }
}
//------------------------------------------------------------------
class HtmlJS extends HtmlMap {
  function header($data = "") {
    global $init;

    // ����ž��
    if(GZIP == true) {
      global $http;
      $http->start();
    }
    $css = (empty($data['defaultSkin'])) ? $init->cssList[0] : $data['defaultSkin'];
	$navi_tag = "[<a href=\"{$GLOBALS['THIS_FILE']}\">�ȥå�</a>]";
    print <<<END
<table class='hako' style="width:100%;"><tr><td class="hako_body">
<link rel="stylesheet" type="text/css" href="{$init->cssDir}/{$css}">
<div id="LinkHeader">
<table class='hako' style="border:none;width:100%"><tr>
<td align="left">{$navi_tag}</td>
<td align="right">
	<a href="http://www.bekkoame.ne.jp/~tokuoka/hakoniwa.html" target="_blank">Ȣ����祹����ץ����۸�</a>
	<a href="http://scrlab.g-7.ne.jp/" target="_blank">[PHP]</a>��
</td>
</tr></table>
</div>
<hr />
<base href="{$init->imgDir}/">
END;
include_once("hako_js.php");
  }
  //---------------------------------------------------
  // ��ȯ����
  //---------------------------------------------------
  function tempOwer($hako, $data, $number = 0) {
    global $init;
    $island = $hako->islands[$number];

    $width  = $init->islandSize * 32 + 50;
    $height = $init->islandSize * 32 + 100;

    // ���ޥ�ɥ��å�
    $set_com = "";
    $com_max = "";
    for($i = 0; $i < $init->commandMax; $i++) {
      // �����Ǥμ��Ф�
      $command  = $island['command'][$i];
      $s_kind   = $command['kind'];
      $s_target = $command['target'];
      $s_x      = $command['x'];
      $s_y      = $command['y'];
      $s_arg    = $command['arg'];
	  //��Ͽ�Ѥߥ��ޥ�ɤκǽ��Ԥ�����
	  if (($s_kind != 41)) $last_com = -1;
	  if (($last_com == -1) && ($s_kind == 41)) $last_com = $i;
      // ���ޥ����Ͽ
      if($i == $init->commandMax - 1){
        $set_com .= "[$s_kind, $s_x, $s_y, $s_arg, $s_target]\n";
        $com_max .= "0";
      } else {
        $set_com .= "[$s_kind, $s_x, $s_y, $s_arg, $s_target],\n";
        $com_max .= "0,";
      }
    }

    //���ޥ�ɥꥹ�ȥ��å�
    $l_kind = '';
    $set_listcom = "";
    $click_com = "";
    $click_com2 = "";
    $All_listCom = 0;
    $com_count = count($init->commandDivido);
    for($m = 0; $m < $com_count; $m++) {
      list($aa,$dd,$ff) = split(",", $init->commandDivido[$m]);
      $set_listcom .= "[ ";
      for($i = 0; $i < $init->commandTotal; $i++) {
        $l_kind = $init->comList[$i];
        $l_cost = $init->comCost[$l_kind];
        if($l_cost == 0) {
          $l_cost = '̵��';
        } elseif($l_cost < 0) {
          $l_cost = - $l_cost; $l_cost .= $init->unitFood;
        } else {
          $l_cost .= $init->unitMoney;
        }
        if($l_kind > $dd-1 && $l_kind < $ff+1) {
          $set_listcom .= "[$l_kind, '{$init->comName[$l_kind]}', '{$l_cost}'],\n";
          if($m == 0){
            $click_com .= "<a href='javascript:void(0);' onclick='cominput(InputPlan, 6, {$l_kind})' style='text-decoration:none'>{$init->comName[$l_kind]}({$l_cost})</a><br />\n";
          } elseif($m == 1) {
            $click_com2 .= "<a href='javascript:void(0);' onclick='cominput(InputPlan, 6, {$l_kind})' style='text-decoration:none'>{$init->comName[$l_kind]}({$l_cost})</a><br />\n";
          }
          $All_listCom++;
        }
        if($l_kind < $ff+1) { next; }
      }
      $bai = strlen($set_listcom);
      $set_listcom = substr($set_listcom, 0, $bai - 2);
      $set_listcom .= " ],\n";
    }
    $bai = strlen($set_listcom);
    $set_listcom = substr($set_listcom, 0, $bai - 2);
    if(empty($data['defaultKind'])) {
      $default_Kind = 1;
    } else {
      $default_Kind = $data['defaultKind'];
    }

    // ���ꥹ�ȥ��å�
    //$set_ships = implode("," , $init->shipName);
    for($i = 0; $i < count($init->shipName); $i++) {
		$set_ships .= "'".$init->shipName[$i]."',";
	}

    // ��ꥹ�ȥ��å�
    $set_island = "";
    for($i = 0; $i < $hako->islandNumber; $i++) {
      $l_name = $hako->islands[$i]['name'];
      $l_name = str_replace("'","\'",$l_name);
      // preg_replace("'", "\\'", $l_name);
      $l_id = $hako->islands[$i]['id'];
      if($i == $hako->islandNumber - 1){
        $set_island .= "[$l_id, '$l_name']\n";
      }else{
        $set_island .= "[$l_id, '$l_name'],\n";
      }
    }
    $defaultTarget = ($init->targetIsland == 1) ? $island['id'] : $hako->defaultTarget;

    print <<<END
<center>
{$init->tagBig_}{$init->tagName_}{$island['name']}��{$init->_tagName}��ȯ�ײ�{$init->_tagBig}<BR>
{$GLOBALS['BACK_TO_TOP']}<br />
</center>
<script type="text/javascript">
<!--
var w;
var p = $defaultTarget;
var hako_select_pointX=0;//�������򤵤�Ƥ����ݥ����X
var hako_select_pointY=0;//�������򤵤�Ƥ����ݥ����Y

// �ʣ��֣�������ץȳ�ȯ�������۸�
// ���äݡ���Ȣ������ http://appoh.execweb.cx/hakoniwa/ ��
// Programmed by Jynichi Sakai(���äݡ�)
// �� ������ʤ��ǲ�������
var str;
g = [$com_max];
k1 = [$com_max];
k2 = [$com_max];
tmpcom1 = [ [0,0,0,0,0] ];
tmpcom2 = [ [0,0,0,0,0] ];
command = [
$set_com];

comlist = [
$set_listcom
];

islname = [
$set_island];

shiplist = [$set_ships];

function hako_init() {
  for(i = 0; i < command.length ;i++) {
    for(s = 0; s < $com_count ;s++) {
      var comlist2 = comlist[s];
      for(j = 0; j < comlist2.length ; j++) {
        if(command[i][0] == comlist2[j][0]) {
          g[i] = comlist2[j][1];
        }
      }
    }
  }
  SelectList('');
  outp();
  str = plchg();
  disp(str, "", "ok");
  comLineBgSet(InputPlan);
}

function cominput(theForm, x, k) {
  a = theForm.number.options[theForm.number.selectedIndex].value;
  b = theForm.command.options[theForm.command.selectedIndex].value;
  c = theForm.pointx.options[theForm.pointx.selectedIndex].value;
  d = theForm.pointy.options[theForm.pointy.selectedIndex].value;
  e = theForm.amount.options[theForm.amount.selectedIndex].value;
  f = theForm.targetid.options[theForm.targetid.selectedIndex].value;
  //  if(x == 6){ b = k; menuclose(); }
  if (x == 1 || x == 6){
    for(i = $init->commandMax - 1; i > a; i--) {
      command[i] = command[i-1];
      g[i] = g[i-1];
    }
  } else if(x == 3) {
    for(i = Math.floor(a); i < ($init->commandMax - 1); i++) {
      command[i] = command[i + 1];
      g[i] = g[i+1];
    }
    command[$init->commandMax - 1] = [41, 0, 0, 0, 0];
    g[$init->commandMax - 1] = '��ⷫ��';
    str = plchg();
    disp(str,"white","no");
    outp();
    comLineBgSet(InputPlan);
    return true;
  } else if(x == 4) {
    i = Math.floor(a);
    if (i == 0){ return true; }
    i = Math.floor(a);
    tmpcom1[i] = command[i];tmpcom2[i] = command[i - 1];
    command[i] = tmpcom2[i];command[i-1] = tmpcom1[i];
    k1[i] = g[i];k2[i] = g[i - 1];
    g[i] = k2[i];g[i-1] = k1[i];
    ns(--i);
    str = plchg();
    disp(str,"white","no");
    outp();
    comLineBgSet(InputPlan);
    return true;
  } else if(x == 5) {
    i = Math.floor(a);
    if (i == $init->commandMax - 1){ return true; }
    tmpcom1[i] = command[i];tmpcom2[i] = command[i + 1];
    command[i] = tmpcom2[i];command[i + 1] = tmpcom1[i];
    k1[i] = g[i];k2[i] = g[i + 1];
    g[i] = k2[i];g[i + 1] = k1[i];
    ns(++i);
    str = plchg();
    disp(str,"white","no");
    outp();
    comLineBgSet(InputPlan);
    return true;
  }

  for(s = 0; s < $com_count; s++) {
    var comlist2 = comlist[s];
    for(i = 0; i < comlist2.length; i++){
      if(comlist2[i][0] == b){
        g[a] = comlist2[i][1];
        break;
      }
    }
  }
  command[a] = [b, c, d, e, f];
  ns(++a);
  str = plchg();
  disp(str, "white","no");
  outp();
  comLineBgSet(InputPlan);
  return true;
}
function plchg() {
  strn1 = "";
  for(i = 0; i < $init->commandMax; i++) {
    c = command[i];

    kind = '{$init->tagComName_}' + g[i] + '{$init->_tagComName}';
    x = c[1];
    y = c[2];
    tgt = c[4];
    point = '{$init->tagName_}' + "(" + x + "," + y + ")" + '{$init->_tagName}';
    ps_tag = 'ps(' + x + ',' + y + ');';
    for(j = 0; j < islname.length ; j++) {
      if(tgt == islname[j][0]){
        tgt = '{$init->tagName_}' + islname[j][1] + "��" + '{$init->_tagName}';
      }
    }
    if(c[0] == $init->comDoNothing || c[0] == $init->comGiveup){ // ��ⷫ�ꡢ�������
      strn2 = kind;
      ps_tag = '';
    }else if(c[0] == $init->comMissileNM || // �ߥ������Ϣ
             c[0] == $init->comMissilePP ||
             c[0] == $init->comMissileST ||
             c[0] == $init->comMissileLD){
      if(c[3] == 0) {
        arg = "�������ϡ�";
      } else {
        arg = "��" + c[3] + "ȯ��";
      }
      strn2 = tgt + point + "��" + kind + arg;
    } else if(c[0] == $init->comSendMonster) { // �����ɸ�
      strn2 = tgt + "��" + kind;
      ps_tag = '';
    } else if(c[0] == $init->comSell) { // ����͢��
      if(c[3] == 0){ c[3] = 1; }
      arg = c[3] * 100;
      arg = "��" + arg + "{$init->unitFood}��";
      strn2 = kind + arg;
      ps_tag = '';
    } else if(c[0] == $init->comPropaganda) { // Ͷ�׳�ư
      strn2 = kind;
      ps_tag = '';
    } else if(c[0] == $init->comMoney) { // �����
      if(c[3] == 0){ c[3] = 1; }
      arg = c[3] * {$init->comCost[$init->comMoney]};
      arg = "��" + arg + "{$init->unitMoney}��";
      strn2 = tgt + "��" + kind + arg;
      ps_tag = '';
    } else if(c[0] == $init->comFood) { // �������
      if(c[3] == 0){ c[3] = 1; }
      arg = c[3] * 100;
      arg = "��" + arg + "{$init->unitFood}��";
      strn2 = tgt + "��" + kind + arg;
      ps_tag = '';
    } else if(c[0] == $init->comDestroy) { // ����
      if(c[3] == 0){
        strn2 = point + "��" + kind;
      } else {
        arg = c[3] * {$init->comCost[$init->comDestroy]};
        arg = "��ͽ\��" + arg + "{$init->unitMoney}��";
        strn2 = point + "��" + kind + arg;
      }
    } else if(c[0] == $init->comFarm || // ���졢���졢���͡��η�������
              c[0] == $init->comFactory ||
              c[0] == $init->comSeaSide ||
              c[0] == $init->comMountain) {
      if(c[3] != 0){
        arg = "��" + c[3] + "���";
        strn2 = point + "��" + kind + arg;
      }else{
        strn2 = point + "��" + kind;
      }
    } else if(c[0] == $init->comMakeShip){ //¤��
		arg = c[3];
		strn2 =  point + "��" + kind + " (" + shiplist[arg] + ")";
    }else{
      strn2 = point + "��" + kind;
    }
    tmpnum = '';
    if(i < 9){ tmpnum = '0'; }
    strn1 +=
      '<span id="com_line'+i+'"><a style="text-decoration:none;color:000000" HREF="javascript:void(0);" onclick="ns(' + i + ');' + ps_tag + 'comLineBgSet(InputPlan);"><nobr>' +
        tmpnum + (i + 1) + ':' +
          strn2 + '<\\/nobr><\\/a></span><br />\\n';
  }
  return strn1;
}

function disp(str,bgclr,status) {
  if (status == "ok"){
	str = '<font color="blue">�� �����Ѥ� ��<\\/font><br />' + str;
	document.InputPlan.send.style.visibility="hidden";
	document.InputPlanAuto.command.style.visibility="visible";
	document.InputPlanAuto.autoset.style.visibility="visible";
  } else {
	str = '<font color="red"><strong>�� ̤���� ��<\\/strong><\\/font><br />' + str;
	document.InputPlan.send.style.visibility="visible";
	document.InputPlanAuto.command.style.visibility="hidden";
	document.InputPlanAuto.autoset.style.visibility="hidden";
  }
  if(str==null)  str = "";

  if(document.getElementById){
    document.getElementById("LINKMSG1").innerHTML = str;
    if(bgclr != "")
      document.getElementById("plan").bgColor = bgclr;
  } else if(document.all){
    el = document.all("LINKMSG1");
    el.innerHTML = str;
    if(bgclr != "")
      document.all.plan.bgColor = bgclr;
  } else if(document.layers) {
    lay = document.layers["PARENT_LINKMSG"].document.layers["LINKMSG1"];
    lay.document.open();
    lay.document.write("<font style='font-size:11pt'>"+str+"<\\/font>");
    lay.document.close();
    if(bgclr != "")
      document.layers["PARENT_LINKMSG"].bgColor = bgclr;
  }
}

function outp() {
  comary = "";

  for(k = 0; k < command.length; k++){
    comary = comary + command[k][0]
      + " " + command[k][1]
        + " " + command[k][2]
          + " " + command[k][3]
            + " " + command[k][4]
              + " " ;
  }
  document.InputPlan.comary.value = comary;
}
function target_ps_x(x){
	target_ps(x,document.InputPlan.pointy.options[document.InputPlan.pointy.selectedIndex].value);
}
function target_ps_y(y){
	target_ps(document.InputPlan.pointx.options[document.InputPlan.pointx.selectedIndex].value,y);
}
function target_ps(x, y){
  pos3 = "hakoimg(" + x + "," + y + ")";
	if (document.all) hakoimgP = document.all.item(pos3);
		else if (document.getElementById) hakoimgP = document.getElementById(pos3);
	hakoimgP.style.width='28px';
	hakoimgP.style.height='28px';
	hakoimgP.style.border='solid 2px yellow';
	hako_map_style["border"]=hakoimgP.style.border;
	hako_map_style["width"]=hakoimgP.style.width;
	hako_map_style["height"]=hakoimgP.style.height;
	if ((hako_select_pointX != x) || (hako_select_pointY != y)){
		clear_ps(hako_select_pointX, hako_select_pointY);
		hako_select_pointX = x;
		hako_select_pointY = y;
	}
}
function clear_ps(x, y){
	pos3 = "hakoimg(" + x + "," + y + ")";
	if (document.all) hakoimgPC = document.all.item(pos3);
		else if (document.getElementById) hakoimgPC = document.getElementById(pos3);
	hakoimgPC.style.width='32px';
	hakoimgPC.style.height='32px';
	hakoimgPC.style.border='none';
}
function ps(x, y) {
  document.InputPlan.pointx.options[x].selected = true;
  document.InputPlan.pointy.options[y].selected = true;
  target_ps(x, y);
  return true;
}


function ns(x) {
  if (x == $init->commandMax){ return true; }
  document.InputPlan.number.options[x].selected = true;
  return true;
}

function set_com(x, y, land) {
  com_str = land + " ";
  for(i = 0; i < $init->commandMax; i++) {
    c = command[i];
    x2 = c[1];
    y2 = c[2];
    if(x == x2 && y == y2 && c[0] < 30){
      com_str += "[" + (i + 1) +"]" ;
      kind = g[i];
      if(c[0] == $init->comDestroy){
        if(c[3] == 0){
          com_str += kind;
        } else {
          arg = c[3] * 200;
          arg = "��ͽ\��" + arg + "{$init->unitMoney}��";
          com_str += kind + arg;
        }
      } else if(c[0] == $init->comFarm ||
                c[0] == $init->comFactory ||
                c[0] == $init->comMountain) {
        if(c[3] != 0){
          arg = "��" + c[3] + "���";
          com_str += kind + arg;
        } else {
          com_str += kind;
        }
      } else {
        com_str += kind;
      }
      com_str += " ";
    }
  }
  document.InputPlan.comstatus.value= com_str;
}


function SelectList(theForm) {
  var u, selected_ok;
  selected_ok = 0;
  if(!theForm) { s = '' }
  else { s = theForm.menu.value; }
  if(s == ''){
    u = 0;
    document.InputPlan.command.options.length = $All_listCom;
    for (i=0; i<comlist.length; i++) {
      var command = comlist[i];
      for (a=0; a<command.length; a++) {
        comName = command[a][1] + "(" + command[a][2] + ")";
        document.InputPlan.command.options[u].value = command[a][0];
        document.InputPlan.command.options[u].text = comName;
        if(command[a][0] == $default_Kind){
          document.InputPlan.command.options[u].selected = true;
          SelectAmount(command[a][0]);
          selected_ok = 1;
        }
        u++;
      }
    }
    if(selected_ok == 0){
      document.InputPlan.command.selectedIndex = 0;
      SelectAmount(document.InputPlan.command.options[0].value);
    }
  } else {
    var command = comlist[s];
    document.InputPlan.command.options.length = command.length;
    for (i=0; i<command.length; i++) {
      comName = command[i][1] + "(" + command[i][2] + ")";
      document.InputPlan.command.options[i].value = command[i][0];
      document.InputPlan.command.options[i].text = comName;
      if(command[i][0] == $default_Kind){
        document.InputPlan.command.options[i].selected = true;
        SelectAmount(command[i][0]);
        selected_ok = 1;
      }
    }
    if(selected_ok == 0){
      document.InputPlan.command.selectedIndex = 0;
      SelectAmount(document.InputPlan.command.options[0].value);
    }
  }
}
function SelectAmount(selectvalue){
	if( selectvalue == $init->comMissileNM ||
		selectvalue == $init->comMissilePP ||
		selectvalue == $init->comMissileST ||
		selectvalue == $init->comMissileLD){
		//�ߥ������
		if (document.InputPlan.amount.options[0].text != '������'){
			document.InputPlan.amount.options.length = 100;
			document.InputPlan.amount.options[0].value = 0;
			document.InputPlan.amount.options[0].text = '������';
			for (i=1; i<100; i++){
				document.InputPlan.amount.options[i].value = i;
				document.InputPlan.amount.options[i].text = i;
			}
		}
	} else if (selectvalue == $init->comSendMonster ||
		selectvalue == $init->comSell ||
		selectvalue == $init->comMoney ||
		selectvalue == $init->comFood ||
		selectvalue == $init->comDestroy ||
		selectvalue == $init->comFarm ||
		selectvalue == $init->comFactory ||
		selectvalue == $init->comSeaSide ||
		selectvalue == $init->comMountain){
		//���̻��ꤹ����
		if (document.InputPlan.amount.options[0].text != '1'){
			document.InputPlan.amount.options.length = 100;
			for (i=0; i<100; i++){
				document.InputPlan.amount.options[i].value = i+1;
				document.InputPlan.amount.options[i].text = i+1;
			}
		}
	} else if (selectvalue == {$init->comMakeShip}){
		//¤���ξ��
		if (document.InputPlan.amount.options[0].text != shiplist[0]){
			document.InputPlan.amount.options.length = {$init->shipKind};
			for (i=0; i<{$init->shipKind}; i++){
				document.InputPlan.amount.options[i].value = i;
				document.InputPlan.amount.options[i].text = shiplist[i];
			}
		}
	} else {
		//����¾
		if (document.InputPlan.amount.options[0].text != '����ʤ�'){
			document.InputPlan.amount.options.length = 1;
			document.InputPlan.amount.options[0].value = 0;
			document.InputPlan.amount.options[0].text = '����ʤ�';
		}
	}
}

function settarget(part){
  p = part.options[part.selectedIndex].value;
}
function targetopen() {
  w = window.open("{$GLOBALS['THIS_FILE']}?mode=targetView&target=" + p, "","width={$width},height={$height},scrollbars=1,resizable=1,toolbar=1,menubar=1,location=1,directories=0,status=1");
}
function comLineBgSet(theForm) {
	line_num = theForm.number.options[theForm.number.selectedIndex].value;
	document.InputPlanAuto.number.value = line_num;

END;
	for ($i=0;$i<$init->commandMax;$i++){
		print "if(line_num == $i){document.all('com_line$i').style.backgroundColor='#99decc';}
			else {document.all('com_line$i').style.backgroundColor='white';}";
	}
	print <<<END
}
    //-->
</script>
END;

    // �ǽ��������� �� �������󹹿��������
    $this->lastModified($hako);

    $this->islandInfo($island, $number, 1);

    print <<<END
<center>
<table class='hako' border>
<tr valign="top">
<td $init->bgInputCell>
<form action="{$GLOBALS['THIS_FILE']}" method="post" name="InputPlan">
<input type="hidden" name="mode" value="command">
<input type="hidden" name="comary" value="comary">
<input type="hidden" name="developemode" value="java">
<center>
<hr />
<b>�ײ��ֹ�</b>
<select name="number" onChange="comLineBgSet(InputPlan);">
END;

    // �ײ��ֹ�
    for($i = 0; $i < $init->commandMax; $i++) {
      $j = $i + 1;
      if ($i == $last_com) {
      	print "<option value=\"$i\" selected>$j</option>\n";
      } else {
		print "<option value=\"$i\">$j</option>\n";
	  }
    }

    if ($HmenuOpen == 'on') {
      $open = "CHECKED";
    }else{
      $open = "";
    }

    print <<<END
</select>
<hr />
<b>��ȯ�ײ�</b>
<input type="hidden" name="menu" value="">
<input type="button" value="������" onClick="InputPlan.menu.value='';SelectList(InputPlan);"><br />
END;

    for($i = 0; $i < $com_count; $i++) {
      list($aa, $tmp) = split(",", $init->commandDivido[$i], 2);
      print "<input type=\"button\" value=\"{$aa}\" onClick=\"InputPlan.menu.value='$i';SelectList(InputPlan);\">";
    }
    print <<<END
<br />
<select name="command" onchange="SelectAmount(this.options[this.selectedIndex].value);">
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
<option>��������������������</option>
</select>
<hr />
<b>��ɸ(</b>
<select name="pointx" onChange="target_ps_x(this.options[this.selectedIndex].value);">

END;

    for($i = 0; $i < $init->islandSize; $i++) {
      if($i == $data['defaultX']) {
        print "<option value=\"$i\" selected>$i</option>\n";
      } else {
        print "<option value=\"$i\">$i</option>\n";
      }
    }

    print "</select>, <select name=\"pointy\" onChange=\"target_ps_y(this.options[this.selectedIndex].value);\">\n";

    for($i = 0; $i < $init->islandSize; $i++) {
      if($i == $data['defaultY']) {
        print "<option value=\"$i\" selected>$i</option>\n";
      } else {
        print "<option value=\"$i\">$i</option>\n";
      }
    }

    print <<<END
</select><b> )</b>
<hr />
<b>����</b><select name="amount">

END;

    // ����
    //for($i = 0; $i < 100; $i++) {
    //  print "<option value=\"$i\">$i</option>\n"
    //}
	print "<option value=\"0\">����ʤ�</option>\n";
    print <<<END
</select>
<hr />
<b>��ɸ����</b><br />
<select name="targetid" onchange="settarget(this);">
$hako->targetList<br />
</select>
<input type="button" value="��ɸ��ª" onClick="javascript: targetopen();">
<hr />
<b>���ޥ������</b><br />
<b>
<a href="javascript:void(0);" onclick="cominput(InputPlan,1)"><span class="NaviBtn">����</span></a> 
<a href="javascript:void(0);" onclick="cominput(InputPlan,2)"><span class="NaviBtn">���</span></a> 
<a href="javascript:void(0);" onclick="cominput(InputPlan,3)"><span class="NaviBtn" style="color:#FF6633;">���</span></a>
</b>
<hr />
<b>���ޥ�ɰ�ư</b>��
<a href="javascript:void(0);" onclick="cominput(InputPlan,4);comLineBgSet(InputPlan);" style="text-decoration:none;font-size:18px;"> �� </a>����
<a href="javascript:void(0);" onclick="cominput(InputPlan,5);comLineBgSet(InputPlan);" style="text-decoration:none;font-size:18px;"> �� </a>
<hr />
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="password" value="{$data['defaultPassword']}">
<input type="submit" name="send" value="�ײ�����Click!" style="background-color:#ffffff;color:red;">
<hr /></form>
<form action="{$GLOBALS['THIS_FILE']}" method="post" name="InputPlanAuto">
<input type="hidden" name="mode" value="command">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="commandmode" value="insert">
<input type="hidden" name="number" value="0">
<input type="hidden" name="from_js" value="java">
<select name="command">
END;
    // ��ư���Ϸϥ��ޥ��
	for($i = 0; $i < $init->commandTotal; $i++) {
		$kind = $init->comList[$i];
		if ($kind > 60){
			//echo $kind."<br />";
			//if($kind == $data['defaultKind']) {
			//	$s = 'selected';
			//} else {
			//	$s = '';
			//}
			print "<option value=\"{$kind}\" {$s}>{$init->comName[$kind]}</option>\n";
    	}
    }
    print <<<END
</select>
<input name="autoset" type="submit" value="���å�">
</form></center></td>
<td $init->bgMapCell><center>
</center>
END;

    $this->islandMap($hako, $island, 1);    // ����Ͽޡ���ͭ�ԥ⡼��

    $comment = $hako->islands[$number]['comment'];
    
    if ($init->mailUse){
	    if ($island['tomail']){
			$ToMailBoxTag="<input id=\"hako_tomail\" type=\"checkbox\" name=\"tomail\" value=\"1\" checked>";
		} else {
			$ToMailBoxTag="<input id=\"hako_tomail\" type=\"checkbox\" name=\"tomail\" value=\"1\">";
		}
		$mailTo_tag = "<label for=\"hako_tomail\">�᡼�����Ρ�{$ToMailBoxTag}�ʥ����󤴤Ȥ˥��٥�Ȥ�᡼������Τ��ޤ�����</label><br />";
	} else {
		$mailTo_tag = "";
	}

    print <<<END

</td>
<td $init->bgCommandCell id="plan">
<ilayer name="PARENT_LINKMSG"  style="width:100%;" height="100%">
<layer name="LINKMSG1" width="200"></layer>
<span id="LINKMSG1"></span>
</ilayer>
<br />
</td>
</tr>
</table>
</center>
<hr />
<div id='CommentBox'>
<h2>�����ȹ���</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="password" value="{$data['defaultPassword']}">
<input type="hidden" name="mode" value="comment">
<input type="hidden" name="islandid" value="{$island['id']}">
<input type="hidden" name="developemode" value="java">
{$mailTo_tag}
������<input type="text" name="message" size="80" value="{$island['comment']}">
<input type="submit" value="�����ȹ���">
</form>
</div>
<script>hako_init()</script>
END;

  }
}



class HtmlSetted extends HTML {
  function setSkin() {
    global $init;
    print "{$init->tagBig_}�������륷���Ȥ����ꤷ�ޤ�����{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function comment() {
    global $init;
    print "{$init->tagBig_}�����Ȥ򹹿����ޤ���{$init->_tagBig}<hr />";
  }
  function change() {
    global $init;
    print "{$init->tagBig_}�ѹ���λ���ޤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function lbbsDelete() {
    global $init;
    print "{$init->tagBig_}��Ģ���Ƥ������ޤ���{$init->_tagBig}<hr />";
  }
  function lbbsAdd() {
    global $init;
    print "{$init->tagBig_}��Ģ��Ԥ��ޤ���{$init->_tagBig}<hr />";
  }
  // ���ޥ�ɺ��
  function commandDelete() {
    global $init;
    print "{$init->tagBig_}���ޥ�ɤ������ޤ���{$init->_tagBig}<hr />\n";
  }

  // ���ޥ����Ͽ
  function commandAdd() {
    global $init;
    print "{$init->tagBig_}���ޥ�ɤ���Ͽ���ޤ���{$init->_tagBig}<hr />\n";
  }
}
class Error {
  function wrongPassword() {
    global $init;
    print "{$init->tagBig_}��Υ����ʤ������������Ǥ��ޤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  // {$init->datafileName}���ʤ�
  function noDataFile() {
    global $init;
    print "{$init->tagBig_}�ǡ����ե����뤬�����ޤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function newIslandFull() {
    global $init;
    print "{$init->tagBig_}����������ޤ����礬���դ���Ͽ�Ǥ��ޤ��󡪡�{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function newIslandNoName() {
    global $init;
    print "{$init->tagBig_}��ˤĤ���̾����ɬ�פǤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function newIslandBadName() {
    global $init;
    print "{$init->tagBig_},?()<>\$�Ȥ����äƤ��ꡢ��̵����פȤ����ä��Ѥ�̾���Ϥ��ޤ��礦�����{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function newIslandLimit() {
    global $init;
    print "{$init->tagBig_}��Ͱ���˸��ꤷ�Ƥ��ޤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function newIslandAlready() {
    global $init;
    print "{$init->tagBig_}������ʤ餹�Ǥ�ȯ������Ƥ��ޤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function newIslandNoPassword() {
    global $init;
    print "{$init->tagBig_}�ѥ���ɤ�ɬ�פǤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function changeNoMoney() {
    global $init;
    print "{$init->tagBig_}�����­�Τ����ѹ��Ǥ��ޤ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function changeNothing() {
    global $init;
    print "{$init->tagBig_}̾�����ѥ���ɤȤ�˶���Ǥ�{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function problem() {
    global $init;
    print "{$init->tagBig_}����ȯ�����Ȥꤢ������äƤ���������{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function lbbsNoMessage() {
    global $init;
    print "{$init->tagBig_}̾���ޤ������Ƥ��󤬶���Ǥ���{$init->_tagBig}{$GLOBALS['BACK_TO_TOP']}\n";
  }
  function turndMessage() {
    global $init;
    print "{$init->tagBig_}�����������λ��\n";
  }
  function noturndMessage() {
    global $init;
    print "{$init->tagBig_}��������֤�ã���Ƥ��ޤ���\n";
  }
}
?>
