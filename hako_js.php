<?php
//======================================================================
// �ȥåץڡ�����
// ���Υ�����ޤǤλ���
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
		rem_mes = "(������λ! [<a href='{$init->$mainFileUrl}?'><b>�����</b></a>]���Ƥ���������)";
	} else {
		var hour = Math.floor(remain / 3600);
		var min	= Math.floor(remain % 3600 / 60);
		var sec	= Math.floor(remain % 3600 % 60);
		//if(min < 10) { min = "0" + min; }
		//if(sec < 10) { sec = "0" + sec; }
		hour_txt = '<b>' + hour + '<\/b> ���� ';
		min_txt = '<b>' + min + '<\/b> ʬ ';
		if (hour < 1) {
			hour_txt = '';
			if (min < 1) {min_txt = '';}
		}
		rem_mes = '(���Υ�����ޤǡ����� ' + hour_txt + min_txt + '<b>' + sec + '<\/b> ��)';
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
// ��ȯ������
//======================================================================
// ��ȯ���Ѹ�����
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
/* ��νи�����Ĵ�� */
	var off_left = 50; // ������
	var off_top =  100;  // ������
	var hako_map_style = new Object(); //�ޥåץԥ�����Υ������륷������¸��

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
	mes2 = "����"+pos2+" "+title;
	if (text) {mes2+=" ["+text+"] ";}
	if (exp) {mes2+="�ײ�:"+exp;}
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

//searchString������ˤ���ʸ��������
function getFront(mainStr,searchStr){
	foundOffset=mainStr.indexOf(searchStr)
	if (foundOffset==-1){
		return null
	}
	return mainStr.substring(0,foundOffset)
}

//searchStrng����ˤ���ʸ��������
function getEnd(mainStr,searchStr){
	foundOffset=mainStr.indexOf(searchStr)
	if(foundOffset==-1){
		return null
	}
	return mainStr.substring(foundOffset+searchStr.length,mainStr.length)
}

//������������ؿ�
function deleteTag(mainStr){
	//�ޤ������ȥ����������
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

	//�Ĥä������������
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
