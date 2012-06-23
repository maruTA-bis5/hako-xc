<?php
//======================================================================
// トップページ用
// 次のターンまでの時間
print <<<END
<script Language="JavaScript">
<!--
function remainTime(nextTime) {
	var now = new Date();
	var remain = nextTime - Math.floor(now / 1000);
	var rem_mes = "";
	var hour_txt = "";
	var min_txt = "";
	if(remain < 0) {
		rem_mes = "(ターン完了! [<a href='{$init->$mainFileUrl}?'><b>リロード</b></a>]してください。)";
	} else {
		var hour = Math.floor(remain / 3600);
		var min	= Math.floor(remain % 3600 / 60);
		var sec	= Math.floor(remain % 3600 % 60);
		//if(min < 10) { min = "0" + min; }
		//if(sec < 10) { sec = "0" + sec; }
		hour_txt = '<b>' + hour + '<\/b> 時間 ';
		min_txt = '<b>' + min + '<\/b> 分 ';
		if (hour < 1) {
			hour_txt = '';
			if (min < 1) {min_txt = '';}
		}
		rem_mes = '(次のターンまで、あと ' + hour_txt + min_txt + '<b>' + sec + '<\/b> 秒)';
	}

	if (document.layers) rem_doc = document.layers['remain_time'];
	else if (document.all) rem_doc = document.all.item('remain_time');
	else if (document.getElementById) rem_doc = document.getElementById('remain_time');

	if (document.layers) {
		with (rem_doc.document) { open(); write(rem_mes); close(); }
	} else {
		rem_doc.innerHTML = rem_mes;
	}
	
	setTimeout("remainTime(nextTime)",1000);

}

//======================================================================
// 開発画面用
//======================================================================
// 開発、観光画面
function NaviClose() {
      if (document.layers)
         with (doc) {
            with (document) { open(); write(""); close(); }
            visibility = "hidden";
         }
      else {
         doc.innerHTML = "";
         with (doc.style) { visibility = "hidden"; }
      }
	//hakoimgP.style.width=32;
	//hakoimgP.style.height=32;
	//hakoimgP.style.border='none';
	if (hako_map_style["border"]=="") hako_map_style["border"]="none";
	hakoimgP.style.border=hako_map_style["border"];
	hakoimgP.style.width=hako_map_style["width"];
	hakoimgP.style.height=hako_map_style["height"];
	window.status = hako_map_style["status"];
}
/* 窓の出現位置調整 */
	var off_left = 50; // 横方向
	var off_top =  100;  // 縦方向
	var hako_map_style = new Object(); //マップピクセルのスタイルシート保存用

function Navi(img, title, pos2, text, exp, order_y) {


   if (navigator.userAgent.match(/msie/i) && navigator.userAgent.match(/mac/i))
      return;
      
    order_x = pos2.replace(/\(([0-9]+),[0-9]+\)/, "$1");
    
    if (order_y > 6) {
		off_top = -100;
	} else {
		off_top = 50
	}
	
	if (order_x > 6) {
		off_left = -200;
	} else {
		off_left = 50
	}
    
    pos3="hakoimg" + pos2;
    if (document.all) hakoimgP = document.all.item(pos3);
	else if (document.getElementById) hakoimgP = document.getElementById(pos3);
	hako_map_style["border"]=hakoimgP.style.border;
	hako_map_style["width"]=hakoimgP.style.width;
	hako_map_style["height"]=hakoimgP.style.height;
	hakoimgP.style.width='28px';
	hakoimgP.style.height='28px';
	hakoimgP.style.border='solid 2px red';
	
	pos = "NaviView";
	rw = 150; 
	mes = "<div class='NaviTitle'>" + title + " " + pos2 + "<\/div><img class='NaviImg' src={$init->imgDir}/" + img + " style='float:left;' /><div class='NaviText'>" + text.replace("\\n", "<br />") + "<\/div>";
	if(exp) {
		mes += "<div class='NaviText'>" + eval(exp) + "<\/div>";
	}
	mes2 = "地点"+pos2+" "+title;
	if (text) {mes2+=" ["+text+"] ";}
	if (exp) {mes2+="計画:"+exp;}
	tx = "#000000"; 
	bg = "#FFFFFF"; 
	bd = "#0000ff";
	
	if (document.all) doc = document.all.item(pos);
	else if (document.getElementById) doc = document.getElementById(pos);
	else if (document.layers) doc = document.layers[pos];

	if (document.layers) {
		with (doc.document) { open(); write(mes); close(); }
	} else {
		doc.innerHTML = mes;
	}
	hako_map_style["status"] = window.status;
	
	window.status = deleteTag(mes2);
	window.status = order_x;

      if (document.layers) {
         with (doc) { left = rx; top = ry; bgColor = bg; visibility = "visible"; }
      }
      else {
         with (doc.style) {
            left = rx; top = ry; width = rw
            borderColor = bd; background = bg;
            visibility = "visible";
         }
      }
}

function mouse_pos(e) {

   if (document.layers) {
      rx = e.pageX;
      ry = e.pageY;
   }
   else if (document.all) {
      rx = event.clientX + (document.body.scrollLeft || document.documentElement.scrollLeft);
      ry = event.clientY + (document.body.scrollTop || document.documentElement.scrollTop);
   }
   else if (document.getElementsByName) {
      rx = e.pageX;
      ry = e.pageY;
   }
   rx += off_left;
   ry += off_top;
   rx = rx + 'px';
   ry = ry + 'px';
}

if (document.layers) document.captureEvents(Event.MOUSEMOVE);
document.onmousemove = mouse_pos;

// Copyright (C) 2002-2002 suepon , All rights reserved.
// Script found at http://suepon.com/

//searchStringより前にある文字列を取得
function getFront(mainStr,searchStr){
	foundOffset=mainStr.indexOf(searchStr)
	if (foundOffset==-1){
		return null
	}
	return mainStr.substring(0,foundOffset)
}

//searchStrngより後にある文字列を取得
function getEnd(mainStr,searchStr){
	foundOffset=mainStr.indexOf(searchStr)
	if(foundOffset==-1){
		return null
	}
	return mainStr.substring(foundOffset+searchStr.length,mainStr.length)
}

//タグを削除する関数
function deleteTag(mainStr){
	//まずコメントタグを取り除く
	var str=""
	var front=getFront(mainStr,"<!--")
	var end=mainStr
	while(front!=null){
		str+=front
		end=getEnd(getEnd(end,"<!--"),"-->")
		if(end!=null){
			front=getFront(end,"<!--")
		}else{
			end=""
			break
		}
	}
	str+=end

	//残ったタグを取り除く
	front=getFront(str,"<")
	end=str
	str=""
	while(front!=null){
		str+=front
		tmp=getEnd(getEnd(end,"<"),">")
		if(tmp!=null){
			end=tmp
			front=getFront(tmp,"<")
		}else{
			break
		}
	}
	return str+end
}

// -->
</script>
END;
?>
