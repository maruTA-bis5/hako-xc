<?php
/*******************************************************************

  Ȣ����磲 for PHP

  
  $Id$

*******************************************************************/
if (! file_exists('config.php'))
{
	exit ("<h4>Please file copy 'config.php.dev' to 'config.php'</h4>");
}

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

require 'config.php';
require 'hako-html.php';
define("READ_LINE", 1024);
$init = new Init;
$THIS_FILE = $init->baseDir . "/hako-mente.php";

class HtmlMente extends HTML {

  function enter() {
    global $init;
    print <<<END
<h1>Ȣ�磲 ���ƥʥ󥹥ġ���</h1>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<strong>�ѥ���ɡ�</strong>
<input type="password" size="32" maxlength="32" name="password">
<input type="hidden" name="mode" value="enter">
<input type="submit" value="���ƥʥ�">
</form>

END;
  
  }
  function main($data) {
    global $init;
    print "<h1>Ȣ�磲 ���ƥʥ󥹥ġ���</h1>\n";
    
    if(is_dir("{$init->dirName}") && (file_exists("{$init->dirName}/{$init->datafileName}"))) {
      $this->dataPrint($data);
    } else {
      print "<hr />\n";
      print "<form action=\"{$GLOBALS['THIS_FILE']}\" method=\"post\">\n";
      //print "<input type=\"hidden\" name=\"PASSWORD\" value=\"{$data['PASSWORD']}\">\n";
      print "<input type=\"hidden\" name=\"mode\" value=\"NEW\">\n";
      print "<input type=\"submit\" value=\"�������ǡ�������\">\n";
      print "</form>\n";
    }

    // �Хå����åץǡ���
    $dir = opendir("./");
    $sufs = array();
    while($dn = readdir($dir)) {
      if(preg_match("/{$init->dirName}\.bak(.*)$/", $dn, $suf)) {
        //$this->dataPrint($data, $suf[1]);
        $sufs[] = $suf[1];
      }
    }
    closedir($dir);
    natcasesort($sufs);
    foreach($sufs as $suf)
    {
    	$this->dataPrint($data, $suf);
    }


  }
  // ɽ���⡼��
  function dataPrint($data, $suf = "") {
    global $init;

    print "<HR>";
    if(strcmp($suf, "") == 0) {
      $fp = fopen("{$init->dirName}/{$init->datafileName}", "r");
      print "<h1>����ǡ���</h1>\n";
    } else {
      $fp = fopen("{$init->dirName}.bak{$suf}/{$init->datafileName}", "r");
      print "<h1>�Хå����å�{$suf}</h1>\n";
    }

    $lastTurn = chop(fgets($fp, READ_LINE));
    $lastTime = chop(fgets($fp, READ_LINE));
    fclose($fp);
    $timeString = timeToString($lastTime);

    print <<<END
<strong>������$lastTurn</strong><br />
<strong>�ǽ���������</strong>:$timeString<br />
<strong>�ǽ���������(�ÿ�ɽ��)</strong>:1970ǯ1��1������$lastTime ��<br />
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="DELETE">
<input type="hidden" name="number" value="{$suf}">
<input type="submit" value="���Υǡ�������">
</form>

END;

    if(strcmp($suf, "") == 0) {
      $time = localtime($lastTime, TRUE);
      $time['tm_year'] += 1900;
      $time['tm_mon']++;

      print <<<END
<h2>�ǽ��������֤��ѹ�</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="NTIME">
<input type="hidden" name="number" value="{$suf}">
<input type="text" size="4" name="year" value="{$time['tm_year']}">ǯ
<input type="text" size="2" name="mon" value="{$time['tm_mon']}">��
<input type="text" size="2" name="date" value="{$time['tm_mday']}">��
<input type="text" size="2" name="hour" value="{$time['tm_hour']}">��
<input type="text" size="2" name="min" value="{$time['tm_min']}">ʬ
<input type="text" size="2" name="nsec" value="{$time['tm_sec']}">��
<input type="submit" value="�ѹ�">
</form>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="STIME">
<input type="hidden" name="number" value="{$suf}">
1970ǯ1��1������<input type="text" size="32" name="ssec" value="$lastTime">��
<input type="submit" value="�û�����ѹ�">
</form>

END;
    } else {
      print <<<END
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="CURRENT">
<input type="hidden" name="number" value="{$suf}">
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
	global $init;
    $html = new HtmlMente;

    $this->parseInputData();
	$admin_header['admin_mente'] = "yes";
    $html->header($admin_header);
    switch($this->mode) {
    case "NEW":
      if($this->passCheck())
      if (!$this->newMode()){
		$pm = sprintf("%o",$init->dirMode);
		echo "<div style=\"text-align:left;color:red;\">ERROR:<br />��".$init->baseDir."�פˡ�<br />�ǡ����ǥ��쥯�ȥ��".$init->dirName."�פ�ѡ��ߥå�����".$pm."�פǺ������Ƥ���⤦���٥����������Ƥ���������</div>";
		break;
	  }

      $html->main($this->dataSet);
      break;

    case "CURRENT":
      if($this->passCheck())
        $this->currentMode($this->dataSet['NUMBER']);
      
      $html->main($this->dataSet);
      break;

    case "DELETE":
      if($this->passCheck())
        $this->delMode($this->dataSet['NUMBER']);

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
      //$html->enter();
      $html->main($this->dataSet);
      break;
    }
    $html->footer();
  }
  //----------------------------------------
  function parseInputData() {
    $this->mode = $_POST['mode'];    
    if(!empty($_POST)) {

      // Xoops Protector �к�
      $toupper = array("number","year","mon","date","hour","min","nsec","ssec");

      foreach($_POST as $name=>$value) {
//        $value = Util::sjis_convert($value);
        // Ⱦ�ѥ��ʤ���������Ѥ��Ѵ������֤�
//        $value = i18n_ja_jp_hantozen($value,"KHV");
          $value = str_replace(",", "", $value);

          // Xoops Protector �к�
          if (in_array($name,$toupper)) $name = strtoupper($name);

        $this->dataSet["{$name}"] = $value;
      }
    }
  }
  function newMode() {
    global $init;
    $flg = @mkdir($init->dirName, $init->dirMode);
    if (!$flg) {
		$fp=@opendir($init->dirName);
		if (!$fp) return(false);
	}

    // ���ߤλ��֤����
    $now = time();
    $now = $now - ($now % ($init->unitTime));

    $fileName = "{$init->dirName}/{$init->datafileName}";
    $fp = fopen($fileName, "w");
    fputs($fp, "1\n");
    fputs($fp, "{$now}\n");
    fputs($fp, "0\n");
    fputs($fp, "1\n");
    fclose($fp);
    return(true);

  }
  function delMode($id="") {
    global $init;
    if($id === "") {
      $dirName = "{$init->dirName}";
    } else {
      $dirName = "{$init->dirName}.bak{$id}";
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
  function currentMode($id) {
    global $init;
    $this->rmTree("{$init->dirName}");
    mkdir("{$init->dirName}", $init->dirMode);

    $dir = opendir("{$init->dirName}.bak{$id}/");
    while($fileName = readdir($dir)) {
      if(!(strcmp($fileName, ".") == 0 || strcmp($fileName, "..") == 0))
        copy("{$init->dirName}.bak{$id}/{$fileName}", "{$init->dirName}/{$fileName}");
    } 
    closedir($dir);
  }
  //----------------------------------------
  function rmTree($dirName) {
	//echo $dirName;
    if(is_dir($dirName)) {
      $dir = opendir($dirName."/");
      while($fileName = readdir($dir)) {
        if(!(strcmp($fileName, ".") == 0 || strcmp($fileName, "..") == 0))
          unlink("{$dirName}/{$fileName}");
      }
      closedir($dir);
      rmdir($dirName);
    }
  }
  function passCheck() {
    global $init;
    //if(strcmp($this->dataSet['PASSWORD'], $init->masterPassword) == 0) {
      return 1;
    //} else {
    //  print "<h2>�ѥ���ɤ��㤤�ޤ���</h2>\n";
    //  return 0;
    //}
  }
}

$start = new Main();
$start->execute();
//xoops admin footer
	CloseTable();
	xoops_cp_footer();
//
?>