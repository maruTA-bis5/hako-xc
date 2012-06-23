<?php
/*******************************************************************

  箱庭諸島２ for PHP

  
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
<h1>箱島２ メンテナンスツール</h1>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<strong>パスワード：</strong>
<input type="password" size="32" maxlength="32" name="password">
<input type="hidden" name="mode" value="enter">
<input type="submit" value="メンテナンス">
</form>

END;
  
  }
  function main($data) {
    global $init;
    print "<h1>箱島２ メンテナンスツール</h1>\n";
    
    if(is_dir("{$init->dirName}") && (file_exists("{$init->dirName}/{$init->datafileName}"))) {
      $this->dataPrint($data);
    } else {
      print "<hr />\n";
      print "<form action=\"{$GLOBALS['THIS_FILE']}\" method=\"post\">\n";
      //print "<input type=\"hidden\" name=\"PASSWORD\" value=\"{$data['PASSWORD']}\">\n";
      print "<input type=\"hidden\" name=\"mode\" value=\"NEW\">\n";
      print "<input type=\"submit\" value=\"新しいデータを作る\">\n";
      print "</form>\n";
    }

    // バックアップデータ
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
  // 表示モード
  function dataPrint($data, $suf = "") {
    global $init;

    print "<HR>";
    if(strcmp($suf, "") == 0) {
      $fp = fopen("{$init->dirName}/{$init->datafileName}", "r");
      print "<h1>現役データ</h1>\n";
    } else {
      $fp = fopen("{$init->dirName}.bak{$suf}/{$init->datafileName}", "r");
      print "<h1>バックアップ{$suf}</h1>\n";
    }

    $lastTurn = chop(fgets($fp, READ_LINE));
    $lastTime = chop(fgets($fp, READ_LINE));
    fclose($fp);
    $timeString = timeToString($lastTime);

    print <<<END
<strong>ターン$lastTurn</strong><br />
<strong>最終更新時間</strong>:$timeString<br />
<strong>最終更新時間(秒数表示)</strong>:1970年1月1日から$lastTime 秒<br />
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="DELETE">
<input type="hidden" name="number" value="{$suf}">
<input type="submit" value="このデータを削除">
</form>

END;

    if(strcmp($suf, "") == 0) {
      $time = localtime($lastTime, TRUE);
      $time['tm_year'] += 1900;
      $time['tm_mon']++;

      print <<<END
<h2>最終更新時間の変更</h2>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="NTIME">
<input type="hidden" name="number" value="{$suf}">
<input type="text" size="4" name="year" value="{$time['tm_year']}">年
<input type="text" size="2" name="mon" value="{$time['tm_mon']}">月
<input type="text" size="2" name="date" value="{$time['tm_mday']}">日
<input type="text" size="2" name="hour" value="{$time['tm_hour']}">時
<input type="text" size="2" name="min" value="{$time['tm_min']}">分
<input type="text" size="2" name="nsec" value="{$time['tm_sec']}">秒
<input type="submit" value="変更">
</form>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="STIME">
<input type="hidden" name="number" value="{$suf}">
1970年1月1日から<input type="text" size="32" name="ssec" value="$lastTime">秒
<input type="submit" value="秒指定で変更">
</form>

END;
    } else {
      print <<<END
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="mode" value="CURRENT">
<input type="hidden" name="number" value="{$suf}">
<input type="submit" value="このデータを現役に">
</form>

END;

    }
  }
}

function timeToString($t) {
  $time = localtime($t, TRUE);
  $time['tm_year'] += 1900;
  $time['tm_mon']++;

  return "{$time['tm_year']}年 {$time['tm_mon']}月 {$time['tm_mday']}日 {$time['tm_hour']}時 {$time['tm_min']}分 {$time['tm_sec']}秒";
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
		echo "<div style=\"text-align:left;color:red;\">ERROR:<br />「".$init->baseDir."」に、<br />データディレクトリ「".$init->dirName."」をパーミッション「".$pm."」で作成してからもう一度アクセスしてください。</div>";
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

      // Xoops Protector 対策
      $toupper = array("number","year","mon","date","hour","min","nsec","ssec");

      foreach($_POST as $name=>$value) {
//        $value = Util::sjis_convert($value);
        // 半角カナがあれば全角に変換して返す
//        $value = i18n_ja_jp_hantozen($value,"KHV");
          $value = str_replace(",", "", $value);

          // Xoops Protector 対策
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

    // 現在の時間を取得
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
    //  print "<h2>パスワードが違います。</h2>\n";
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