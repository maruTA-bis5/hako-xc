<?php
/*******************************************************************

  Ȣ����磲 for PHP

  
  $Id$

*******************************************************************/
//xoops admin header
include("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/class/xoopsmodule.php");
include(XOOPS_ROOT_PATH."/include/cp_functions.php");
//xoops admin header
if ( $xoopsUser ) {
	$xoopsModule = XoopsModule::getByDirname("hako");
	if ( !$xoopsUser->isAdmin($xoopsModule->mid()) ) { 
		redirect_header(XOOPS_URL."/",3,_NOPERM);
		exit();
	}
} else {
	redirect_header(XOOPS_URL."/",3,_NOPERM);
	exit();
}
	xoops_cp_header();
	OpenTable();

//
require 'jcode.phps';
require 'config.php';
require 'hako-html.php';
define("READ_LINE", 1024);
$init = new Init;
$THIS_FILE = $init->baseDir . "/hako-mente-safemode.php";

class HtmlMente extends HTML {

  function enter() {
    global $init;
    print <<<END
<h1>Ȣ�磲 ���ƥʥ󥹥ġ���</h1>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<strong>�ѥ���ɡ�</strong>
<input type="password" size="32" maxlength="32" name="PASSWORD">
<input type="hidden" name="mode" value="enter">
<input type="submit" value="���ƥʥ�">
</form>

END;
  
  }
  function main($data) {
    global $init;
    print "<h1>Ȣ�磲 ���ƥʥ󥹥ġ���</h1>\n";
    
    if(is_file("{$init->dirName}/{$init->datafileName}")) {
      $this->dataPrint($data);
    } else {
      print "<hr />\n";
      print "<form action=\"{$GLOBALS['THIS_FILE']}\" method=\"post\">\n";
      print "<input type=\"hidden\" name=\"PASSWORD\" value=\"{$data['PASSWORD']}\">\n";
      print "<input type=\"hidden\" name=\"mode\" value=\"NEW\">\n";
      print "<input type=\"submit\" value=\"�������ǡ�������\">\n";
      print "</form>\n";
    }

    // �Хå����åץǡ���
    $dir = opendir("./");
    while($dn = readdir($dir)) {
      if(preg_match("/{$init->dirName}\.bak/", $dn)) {
        $this->dataPrint($data, 1);
      }
    }
    closedir($dir);


  }
  // ɽ���⡼��
  function dataPrint($data, $suf = "") {
    global $init;

    print "<HR>";
    if(strcmp($suf, "") == 0) {
      $fp = fopen("{$init->dirName}/{$init->datafileName}", "r");
      print "<h1>����ǡ���</h1>\n";
    } else {
      $fp = fopen("{$init->dirName}.bak/{$init->datafileName}", "r");
      print "<h1>�Хå����å�</h1>\n";
    }

    $lastTurn = chop(fgets($fp, READ_LINE));
    $lastTime = chop(fgets($fp, READ_LINE));
    fclose($fp);
    $timeString = timeToString($lastTime);

    print <<<END
<strong>������$lastTurn</strong><br />
<strong>�ǽ���������</strong>:$timeString<br />
<strong>�ǽ���������(�ÿ�ɽ\��)</strong>:1970ǯ1��1������$lastTime ��<br />

END;

    if(strcmp($suf, "") == 0) {
      $time = localtime($lastTime, TRUE);
      $time['tm_year'] += 1900;
      $time['tm_mon']++;

      print <<<END
<h2>�ǽ��������֤��ѹ�</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
<input type="hidden" name="mode" value="NTIME">
<input type="hidden" name="NUMBER" value="{$suf}">
<input type="text" size="4" name="YEAR" value="{$time['tm_year']}">ǯ
<input type="text" size="2" name="MON" value="{$time['tm_mon']}">��
<input type="text" size="2" name="DATE" value="{$time['tm_mday']}">��
<input type="text" size="2" name="HOUR" value="{$time['tm_hour']}">��
<input type="text" size="2" name="MIN" value="{$time['tm_min']}">ʬ
<input type="text" size="2" name="NSEC" value="{$time['tm_sec']}">��
<input type="submit" value="�ѹ�">
</form>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
<input type="hidden" name="mode" value="STIME">
<input type="hidden" name="NUMBER" value="{$suf}">
1970ǯ1��1������<input type="text" size="32" name="SSEC" value="$lastTime">��
<input type="submit" value="�û�����ѹ�">
</form>

END;
    } else {
      print <<<END
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
<input type="hidden" name="mode" value="CURRENT">
<input type="submit" value="���Υǡ��������">
</form>

END;

    }
  }
}

function timeToString($t) {
  $time = localtime($t, TRUE);
  $time['tm_year'] += 1900;
  $time['tm_mon']++;

  return "{$time['tm_year']}ǯ {$time['tm_mon']}�� {$time['tm_mday']}�� {$time['tm_hour']}�� {$time['tm_min']}ʬ {$time['tm_sec']}��";
}

class Main {
  var $mode;
  var $dataSet = array();
  function execute() {
    $html = new HtmlMente;

    $this->parseInputData();

    $html->header();
    switch($this->mode) {
    case "NEW":
      if($this->passCheck())
        $this->newMode();

      $html->main($this->dataSet);
      break;

    case "CURRENT":
      if($this->passCheck())
        $this->currentMode();
      
      $html->main($this->dataSet);
      break;

    case "DELETE":
      if($this->passCheck())
        $this->delMode();

      $html->main($this->dataSet);
      break;
    case "NTIME":
      if($this->passCheck())
        $this->timeMode();

      $html->main($this->dataSet);
      break;

    case "STIME":
      if($this->passCheck())
        $this->stimeMode($this->dataSet['SSEC']);

      $html->main($this->dataSet);
      break;

    case "enter":
     if($this->passCheck())
       $html->main($this->dataSet);
      break;
    default:
      $html->enter();
      break;
    }
    $html->footer();
  }
  //----------------------------------------
  function parseInputData() {
    $this->mode = $_POST['mode'];    
    if(!empty($_POST)) {
      while(list($name, $value) = each($_POST)) {
//        $value = Util::sjis_convert($value);
        // Ⱦ�ѥ��ʤ���������Ѥ��Ѵ������֤�
//        $value = i18n_ja_jp_hantozen($value,"KHV");
        JcodeConvert($value, 0, 2);
        $value = str_replace(",", "", $value);

        $this->dataSet["{$name}"] = $value;
      }
    }
  }
  function newMode() {
    global $init;
//    mkdir($init->dirName, $init->dirMode);

    // ���ߤλ��֤����
    $now = time();
    $now = $now - ($now % ($init->unitTime));

    $fileName = "{$init->dirName}/{$init->datafileName}";
    touch($fileName);
    $fp = fopen($fileName, "w");
    fputs($fp, "1\n");
    fputs($fp, "{$now}\n");
    fputs($fp, "0\n");
    fputs($fp, "1\n");
    fclose($fp);
  }
  function delMode() {
    global $init;
    if(empty($this->dataSet['NUMBER'])) {
      $dirName = "data";
    } else {
      $dirName = "data.bak{$this->dataSet['NUMBER']}";
    }
    $this->rmTree($dirName);
  }
  function timeMode() {
    $year = $this->dataSet['YEAR'];
    $day  = $this->dataSet['DATE'];
    $mon  = $this->dataSet['MON'];
    $hour = $this->dataSet['HOUR'];
    $min  = $this->dataSet['MIN'];
    $sec  = $this->dataSet['NSEC'];
    $ctSec = mktime($hour, $min, $sec, $mon, $day, $year);
    $this->stimeMode($ctSec);
  }
  function stimeMode($sec) {
    global $init;
    
    $fileName = "{$init->dirName}/{$init->datafileName}";
    $fp = fopen($fileName, "r+");
    $buffer = array();
    while($line = fgets($fp, READ_LINE)) {
      array_push($buffer, $line);
    }
    $buffer[1] = "{$sec}\n";
    fseek($fp, 0);
    while($line = array_shift($buffer)) {
      fputs($fp, $line);
    }
    fclose($fp);
    
  }
  function currentMode() {
    global $init;
//    $this->rmTree("{$init->dirName}");
//    mkdir("{$init->dirName}", $init->dirMode);

    $dir = opendir("{$init->dirName}.bak/");
    while($fileName = readdir($dir)) {
      if(!(strcmp($fileName, ".") == 0 || strcmp($fileName, "..") == 0))
        copy("{$init->dirName}.bak/{$fileName}", "{$init->dirName}/{$fileName}");
    } 
    closedir($dir);
  }
  //----------------------------------------
  function rmTree($dirName) {
    if(is_dir("{$dirName}")) {
      $dir = opendir("{$dirName}/");
      while($fileName = readdir($dir)) {
        if(!(strcmp($fileName, ".") == 0 || strcmp($fileName, "..") == 0))
          unlink("{$dirName}/{$fileName}");
      }
      closedir($dir);
//      rmdir($dirName);
    }
  }
  function passCheck() {
    global $init;
    if(strcmp($this->dataSet['PASSWORD'], $init->masterPassword) == 0) {
      return 1;
    } else {
      print "<h2>�ѥ���ɤ��㤤�ޤ���</h2>\n";
      return 0;
    }
  }
}

$start = new Main();
$start->execute();

//xoops admin footer
	CloseTable();
	xoops_cp_footer();
//
?>