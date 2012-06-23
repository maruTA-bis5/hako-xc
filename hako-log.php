<?php
/*******************************************************************

  Ȣ����磲 for PHP

  
  $Id$

*******************************************************************/

class Log extends LogIO {

  function discover($name) {
    global $init;
    $this->history("{$init->tagName_}{$name}��{$init->_tagName}��ȯ������롣");
  }
  function changeName($name1, $name2) {
    global $init;
    $this->history("{$init->tagName_}{$name1}��{$init->_tagName}��̾�Τ�{$init->tagName_}{$name2}��{$init->_tagName}���ѹ����롣");
  }
  // ����
  function prize($id, $name, $pName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��<strong>$pName</strong>����ޤ��ޤ�����",$id);
    $this->history("{$init->tagName_}{$name}��{$init->_tagName}��<strong>$pName</strong>�����");
  }
  // ����
  function dead($id, $name) {
    global $init;
    $this->out("{$init->tagName_}${name}��{$init->_tagName}����ͤ����ʤ��ʤꡢ<strong>̵����</strong>�ˤʤ�ޤ�����", $id);
    $this->history("{$init->tagName_}${name}��{$init->_tagName}���ͤ����ʤ��ʤ�<strong>̵����</strong>�Ȥʤ롣");
  }
  function doNothing($id, $name, $comName) {
    //global $init;
    //$this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagComName_}${comName}{$init->_tagComName}���Ԥ��ޤ�����",$id);
  }
  // ���­��ʤ�
  function noMoney($id, $name, $comName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->tagComName_}{$comName}{$init->_tagComName}�ϡ������­�Τ�����ߤ���ޤ�����",$id);
  }
  // ����­��ʤ�
  function noFood($id, $name, $comName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->tagComName_}{$comName}{$init->_tagComName}�ϡ����߿�����­�Τ�����ߤ���ޤ�����",$id);
  }
  // �о��Ϸ��μ���ˤ�뼺��
  function landFail($id, $name, $comName, $kind, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->tagComName_}{$comName}{$init->_tagComName}�ϡ�ͽ���Ϥ�{$init->tagName_}{$point}{$init->_tagName}��<strong>{$kind}</strong>���ä�������ߤ���ޤ�����",$id);
  }
  // ����
  function landSuc($id, $name, $comName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��{$init->tagComName_}{$comName}{$init->_tagComName}���Ԥ��ޤ�����",$id);
  }
  // ��¢��
  function maizo($id, $name, $comName, $value) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}�Ǥ�{$init->tagComName_}{$comName}{$init->_tagComName}��ˡ�<strong>{$value}{$init->unitMoney}�����¢��</strong>��ȯ������ޤ�����",$id);
  }
  function noLandAround($id, $name, $comName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->tagComName_}{$comName}{$init->_tagComName}�ϡ�ͽ���Ϥ�{$init->tagName_}{$point}{$init->_tagName}�μ��դ�Φ�Ϥ��ʤ��ä�������ߤ���ޤ�����",$id);
  }
  // ����ȯ��
  function oilFound($id, $name, $point, $comName, $str) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$str}</strong>��ͽ����Ĥ������{$init->tagComName_}{$comName}{$init->_tagComName}���Ԥ�졢<strong>���Ĥ��������Ƥ��ޤ���</strong>��",$id);
  }
  // ����ȯ���ʤ餺
  function oilFail($id, $name, $point, $comName, $str) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$str}</strong>��ͽ����Ĥ������{$init->tagComName_}{$comName}{$init->_tagComName}���Ԥ��ޤ����������Ĥϸ��Ĥ���ޤ���Ǥ�����",$id);
  }
  // �ɱһ��ߡ��������å�
  function bombSet($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��<strong>�������֤����å�</strong>����ޤ�����",$id);
  }
  // �ɱһ��ߡ�������ư
  function bombFire($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}�������ֺ�ư����{$init->_tagDisaster}",$id);
  }
  // ����or�ߥ��������
  function PBSuc($id, $name, $comName, $point) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$point}{$init->_tagName}��{$init->tagComName_}{$comName}{$init->_tagComName}���Ԥ��ޤ�����",$id);
    $this->out("������ʤ�����{$init->tagName_}{$name}��{$init->_tagName}��<strong>��</strong>���������褦�Ǥ���",$id);
  }
  // �ϥ�ܥ�
  function hariSuc($id, $name, $comName, $comName2, $point) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$point}{$init->_tagName}��{$init->tagComName_}{$comName}{$init->_tagComName}���Ԥ��ޤ�����",$id);
    $this->landSuc($id, $name, $comName2, $point);
  }
  // ��ǰ�ꡢȯ��
  function monFly($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��<strong>�첻�ȤȤ������Ω���ޤ���</strong>��",$id);
  }
  // �ߥ������Ȥ��Ȥ���(or �����ɸ����褦�Ȥ���)���������åȤ����ʤ�
  function msNoTarget($id, $name, $comName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->tagComName_}{$comName}{$init->_tagComName}�ϡ���ɸ����˿ͤ���������ʤ�������ߤ���ޤ�����",$id);
  }
  // ���ƥ륹�ߥ������ä����ϰϳ�
  function msOutS($id, $tId, $name, $tName, $comName, $point) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��$point{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������<strong>�ΰ賰�γ�</strong>����������ͤǤ���",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�ظ�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������<strong>�ΰ賰�γ�</strong>����������ͤǤ���",$tId);
  }
  // �ߥ������ä����ϰϳ�
  function msOut($id, $tId, $name, $tName, $comName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������<strong>�ΰ賰�γ�</strong>����������ͤǤ���",$id, $tId);
  }
  // ���ƥ륹�ߥ������ä����ɱһ��ߤǥ���å�
  function msCaughtS($id, $tId, $name, $tName, $comName, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��������ˤ��Ͼ��ª����졢<strong>������ȯ</strong>���ޤ�����",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�ظ�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��������ˤ��Ͼ��ª����졢<strong>������ȯ</strong>���ޤ�����",$tId);
  }
  // �ߥ������ä����ɱһ��ߤǥ���å�
  function msCaught($id, $tId, $name, $tName, $comName, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��������ˤ��Ͼ��ª����졢<strong>������ȯ</strong>���ޤ�����",$id, $tId);
  }
  // ���ƥ륹�ߥ������ä������̤ʤ�
  function msNoDamageS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��������Τ��ﳲ������ޤ���Ǥ�����",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��������Τ��ﳲ������ޤ���Ǥ�����",$tId);
  }  
  // �ߥ������ä������̤ʤ�
  function msNoDamage($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��������Τ��ﳲ������ޤ���Ǥ�����",$id, $tId);
  }
  // Φ���˲��ơ�����̿��
  function msLDMountain($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��̿�档<strong>{$tLname}</strong>�Ͼä����ӡ����ϤȲ����ޤ�����",$id, $tId);
  }
  // Φ���˲��ơ�������Ϥ�̿��
  function msLDSbase($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��������ȯ��Ʊ�����ˤ��ä�<strong>{$tLname}</strong>���׷���ʤ��᤭���Ӥޤ�����",$id, $tId);
  }
  // Φ���˲��ơ����ä�̿��
  function msLDMonster($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}�����Ƥ���ȯ��Φ�Ϥ�<strong>����{$tLname}</strong>���Ȥ���פ��ޤ�����",$id, $tId);
  }
  // Φ���˲��ơ�������̿��
  function msLDSea1($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>�����ơ����줬�������ޤ�����",$id, $tId);
  }
  // Φ���˲��ơ�����¾���Ϸ���̿��
  function msLDLand($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>�����ơ�Φ�ϤϿ��פ��ޤ�����",$id, $tId);
  }
  // ���ƥ륹�ߥ����롢���Ϥ�����
  function msWasteS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>������ޤ�����",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>������ޤ�����",$tId);
  }
  // �̾�ߥ����롢���Ϥ�����
  function msWaste($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�������{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>������ޤ�����",$id, $tId);
  }
  // ���ƥ륹�ߥ����롢���ä�̿�桢�Ų���ˤ�̵��
  function msMonNoDamageS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�桢�������Ų����֤��ä�������̤�����ޤ���Ǥ�����",$id, $tId);
    $this->out("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�桢�������Ų����֤��ä�������̤�����ޤ���Ǥ�����",$tId);
  }
  // �̾�ߥ����롢���ä�̿�桢�Ų���ˤ�̵��
  function msMonNoDamage($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�桢�������Ų����֤��ä�������̤�����ޤ���Ǥ�����",$id, $tId);
  }
  // ���ƥ륹�ߥ����롢���ä�̿�桢����
  function msMonKillS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�档<strong>����{$tLname}</strong>���ϿԤ����ݤ�ޤ�����",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�档<strong>����{$tLname}</strong>���ϿԤ����ݤ�ޤ�����", $tId);
  }
  // �̾�ߥ����롢���ä�̿�桢����
  function msMonKill($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�档<strong>����{$tLname}</strong>���ϿԤ����ݤ�ޤ�����",$id, $tId);
  }
  // ���äλ���
  function msMonMoney($tId, $mName, $value) {
    global $init;
    $this->out("<strong>����{$mName}</strong>�λĳ��ˤϡ�<strong>{$value}{$init->unitMoney}</strong>���ͤ��դ��ޤ�����",$tId);
  }
  // ���ƥ륹�ߥ����롢���ä�̿�桢���᡼��
  function msMonsterS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�档<strong>����{$tLname}</strong>�϶줷��������Ӭ���ޤ�����",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�档<strong>����{$tLname}</strong>�϶줷��������Ӭ���ޤ�����",$tId);
  }
  // �̾�ߥ����롢���ä�̿�桢���᡼��
  function msMonster($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>����{$tLname}</strong>��̿�档<strong>����{$tLname}</strong>�϶줷��������Ӭ���ޤ�����",$id, $tId);
  }
  // ���ƥ륹�ߥ������̾��Ϸ���̿��
  function msNormalS($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->secret("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��̿�桢���Ӥ����Ǥ��ޤ�����",$id, $tId);
    $this->late("<strong>���Ԥ�</strong>��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��̿�桢���Ӥ����Ǥ��ޤ�����",$tId);
  }
  // �̾�ߥ������̾��Ϸ���̿��
  function msNormal($id, $tId, $name, $tName, $comName, $tLname, $point, $tPoint) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$point}{$init->_tagName}�����˸�����{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ���{$init->tagName_}{$tPoint}{$init->_tagName}��<strong>{$tLname}</strong>��̿�桢���Ӥ����Ǥ��ޤ�����",$id, $tId);
  }
  // �ߥ������Ȥ��Ȥ��������Ϥ��ʤ�
  function msNoBase($id, $name, $comName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->tagComName_}{$comName}{$init->_tagComName}�ϡ�<strong>�ߥ�������������ͭ���Ƥ��ʤ�</strong>����˼¹ԤǤ��ޤ���Ǥ�����",$id);
  }
  // �ߥ�������̱����
  function msBoatPeople($id, $name, $achive) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}�ˤɤ�����Ȥ�ʤ�<strong>{$achive}{$init->unitPop}�����̱</strong>��ɺ�夷�ޤ�����{$init->tagName_}{$name}��{$init->_tagName}�ϲ����������줿�褦�Ǥ���",$id);
  }
  // �����ɸ�
  function monsSend($id, $tId, $name, $tName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��<strong>��¤����</strong>���¤��{$init->tagName_}{$tName}��{$init->_tagName}�����ꤳ�ߤޤ�����",$id, $tId);
  }
  // ͢��
  function sell($id, $name, $comName, $value) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��<strong>{$value}{$init->unitFood}</strong>��{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�����",$id);
  }
  // ���
  function aid($id, $tId, $name, $tName, $comName, $str) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagName_}{$tName}��{$init->_tagName}��<strong>{$str}</strong>��{$init->tagComName_}{$comName}{$init->_tagComName}��Ԥ��ޤ�����",$id, $tId);
  }
  // Ͷ�׳�ư
  function propaganda($id, $name, $comName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagComName_}{$comName}{$init->_tagComName}���Ԥ��ޤ�����",$id);
  }
  // ����
  function giveup($id, $name) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}���������졢<strong>̵����</strong>�ˤʤ�ޤ�����",$id);
    $this->history("{$init->tagName_}{$name}��{$init->_tagName}����������<strong>̵����</strong>�Ȥʤ롣");
  }
  // ���Ĥ���μ���
  function oilMoney($id, $name, $lName, $point, $str) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>���顢<strong>{$str}</strong>�μ��פ��夬��ޤ�����",$id);
  }
  // ���ĸϳ�
  function oilEnd($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>�ϸϳ餷���褦�Ǥ���",$id);
  }
  
	// ͷ���Ϥ���μ���
	function ParkMoney($id, $name, $lName, $point, $str) {
	    global $init;
	    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>{$lName}</B>���顢<B>{$str}</B>�μ��פ��夬��ޤ�����",$id);
	}

	// ͷ���ϤΥ��٥��
	function ParkEvent($id, $name, $lName, $point, $str) {
	    global $init;
	    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>{$lName}</B>�ǥ��٥�Ȥ����Ť��졢<B>{$str}</B>�ο��������񤵤�ޤ�����",$id);
	}
	
	// ͷ���ϤΥ��٥������
	function ParkEventLuck($id, $name, $lName, $point, $str) {
	    global $init;
	    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>{$lName}</B>�ǳ��Ť��줿���٥�Ȥ���������<B>{$str}</B>�μ��פ��夬��ޤ�����",$id);
	}

	// ͷ���ϤΥ��٥�ȸ���
	function ParkEventLoss($id, $name, $lName, $point, $str) {
	    global $init;
	    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>{$lName}</B>�ǳ��Ť��줿���٥�Ȥ����Ԥ���<B>{$str}</B>��»�����Ǥޤ�����",$id);
	}

	// ͷ���Ϥ��ı�
	function ParkEnd($id, $name, $lName, $point) {
	    global $init;
	    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>{$lName}</B>�ϻ��ߤ�Ϸ�ಽ���������ı�Ȥʤ�ޤ�����",$id);
	}
  
  // ���á��ɱһ��ߤ�Ƨ��
  function monsMoveDefence($id, $name, $lName, $point, $mName) {
    global $init;
	$this->out("<strong>����{$mName}</strong>��{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>����ã��<strong>{$lName}�μ������֤���ư����</strong>",$id);
  }
  // ����ư��
  function monsMove($id, $name, $lName, $point, $mName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��<strong>����{$mName}</strong>��Ƨ�߹Ӥ餵��ޤ�����",$id);
  }
  // �к�
  function fire($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}�к�{$init->_tagDisaster}�ˤ����Ǥ��ޤ�����",$id);
  }
  // �����ﳲ�����η���
  function wideDamageSea2($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>���׷���ʤ��ʤ�ޤ�����",$id);
  }
  // �����ﳲ�����ÿ���
  function wideDamageMonsterSea($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��Φ�Ϥ�<strong>����{$lName}</strong>���Ȥ���פ��ޤ�����",$id);
  }
  // �����ﳲ������
  function wideDamageSea($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��<strong>����</strong>���ޤ�����",$id);
  }
  // �����ﳲ������
  function wideDamageMonster($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>����{$lName}</strong>�Ͼä����Ӥޤ�����",$id);
  }
  // �����ﳲ������
  function wideDamageWaste($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>�ϰ�֤ˤ���<strong>����</strong>�Ȳ����ޤ�����",$id);
  }
  // �Ͽ�ȯ��
  function earthquake($id, $name) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}���絬�Ϥ�{$init->tagDisaster_}�Ͽ�{$init->_tagDisaster}��ȯ������",$id);
  }
  // �Ͽ��ﳲ
  function eQDamage($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}�Ͽ�{$init->_tagDisaster}�ˤ����Ǥ��ޤ�����",$id);
  }
  // ����
  function starve($id, $name) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagDisaster_}��������­{$init->_tagDisaster}���Ƥ��ޤ�����",$id);
  }
  // ������­�ﳲ
  function svDamage($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��<strong>��������ƽ�̱������</strong>��<strong>{$lName}</strong>�ϲ��Ǥ��ޤ�����",$id);
  }
  // ����ȯ��
  function tsunami($id, $name) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}�ն��{$init->tagDisaster_}����{$init->_tagDisaster}ȯ������",$id);
  }
  // �����ﳲ
  function tsunamiDamage($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}����{$init->_tagDisaster}�ˤ���������ޤ�����",$id);
  }
  // ���ø���
  function monsCome($id, $name, $mName, $point, $lName) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��<strong>����{$mName}</strong>�и�����{$init->tagName_}{$point}{$init->_tagName}��<strong>{$lName}</strong>��Ƨ�߹Ӥ餵��ޤ�����",$id);
  }
  // ��������ȯ��
  function falldown($id, $name) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagDisaster_}��������{$init->_tagDisaster}��ȯ�����ޤ�������",$id);
  }
  // ���������ﳲ
  function falldownLand($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>�ϳ���������ߤޤ�����",$id);
  }
  // ����ȯ��
  function typhoon($id, $name) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$init->_tagName}��{$init->tagDisaster_}����{$init->_tagDisaster}��Φ����",$id);
  }

  // �����ﳲ
  function typhoonDamage($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}����{$init->_tagDisaster}�����Ф���ޤ�����",$id);
  }
  // ��С�����¾
  function hugeMeteo($id, $name, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������{$init->tagDisaster_}�������{$init->_tagDisaster}�������",$id);
  }
  // ��ǰ�ꡢ�
  function monDamage($id, $name, $point) {
    global $init;
    $this->out("<strong>�����ȤƤĤ�ʤ����</strong>��{$init->tagName_}{$name}��{$point}{$init->_tagName}����������ޤ�������",$id);
  }
  // ��С���
  function meteoSea($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}���{$init->_tagDisaster}������ޤ�����",$id);
  }
  // ��С���
  function meteoMountain($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}���{$init->_tagDisaster}�����<strong>{$lName}</strong>�Ͼä����Ӥޤ�����",$id);
  }
  // ��С��������
  function meteoSbase($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<strong>{$lName}</strong>��{$init->tagDisaster_}���{$init->_tagDisaster}�����<strong>{$lName}</strong>���������ޤ�����",$id);
  }
  // ��С�����
  function meteoMonster($id, $name, $lName, $point) {
    global $init;
    $this->out("<strong>����{$lName}</strong>������{$init->tagName_}{$name}��{$point}{$init->_tagName}������{$init->tagDisaster_}���{$init->_tagDisaster}�����Φ�Ϥ�<strong>����{$lName}</strong>���Ȥ���פ��ޤ�����",$id);
  }
  // ��С�����
  function meteoSea1($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������{$init->tagDisaster_}���{$init->_tagDisaster}��������줬�������ޤ�����",$id);
  }
  // ��С�����¾
  function meteoNormal($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������<strong>{$lName}</strong>��{$init->tagDisaster_}���{$init->_tagDisaster}��������Ӥ����פ��ޤ�����",$id);
  }
  // ʮ��
  function eruption($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������{$init->tagDisaster_}�л���ʮ��{$init->_tagDisaster}��<strong>��</strong>������ޤ�����",$id);
  }
  // ʮ�С�����
  function eruptionSea1($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������<strong>{$lName}</strong>�ϡ�{$init->tagDisaster_}ʮ��{$init->_tagDisaster}�αƶ���Φ�Ϥˤʤ�ޤ�����",$id);
  }
  // ʮ�С���or����
  function eruptionSea($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������<strong>{$lName}</strong>�ϡ�{$init->tagDisaster_}ʮ��{$init->_tagDisaster}�αƶ��ǳ��줬δ���������ˤʤ�ޤ�����",$id);
  }
  // ʮ�С�����¾
  function eruptionNormal($id, $name, $lName, $point) {
    global $init;
    $this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}������<strong>{$lName}</strong>�ϡ�{$init->tagDisaster_}ʮ��{$init->_tagDisaster}�αƶ��ǲ��Ǥ��ޤ�����",$id);
  }
	# ����˳����ʤ��Ƽ���
	function NoSeaAround($id, $name, $comName, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->HtagName_}{$comName}{$init->_tagComName}�ϡ�ͽ���Ϥ�{$init->tagName_}{$point}{$init->_tagName}�μ��դ˳����ʤ��ä�������ߤ���ޤ�����",$id);
	}
	# �����ʤ��Ƽ���
	function NoSea($id, $name, $comName, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->HtagName_}{$comName}{$init->_tagComName}�ϡ�ͽ���Ϥ����Ǥʤ��ä�������ߤ���ޤ�����",$id);
	}
	# �����ʤ��Τǡ�¤������
	function NoPort($id, $name, $comName, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$init->_tagName}��ͽ�ꤵ��Ƥ���{$init->HtagName_}{$comName}{$init->_tagComName}�ϡ�<b>��</b>���ʤ��ä�������ߤ���ޤ�����",$id);
	}
	# ���ĺ�
	function ClosedPort($id, $name, $lName, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>{$lName}</B>���ĺ������褦�Ǥ���",$id);
	}
	# ��±������
	function VikingCome($id, $name, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>��±��</B>�и�����",$id);
	}
	# ��±�����
	function VikingAway($id, $name, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}����<B>��±��</B>���ɤ����˵�äƤ����ޤ�����",$id);
	}
	# ��±����
	function VikingAttack($id, $name, $lName, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<b>{$lName}</b>��<B>��±��</B>�ˤ�ä����פ������ޤ�����",$id);
	}

	# ��±������å
	function RobViking($id, $name, $money, $food) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$init->_tagName}��<B>��±</B>��<b>{$money}{$init->unitMoney}</b>�ζ��<b>{$food}{$init->unitFood}</b>�ο�����å���Ƥ����ޤ�����",$id);
	}
	# ���¾�
	function RunAground($id, $name, $lName, $point) {
		global $init;
		$this->out("{$init->tagName_}{$name}��{$point}{$init->_tagName}��<B>$lName</B>��{$init->tagDisaster_}�¾�{$init->_tagDisaster}���ޤ�����",$id);
	}
}

?>