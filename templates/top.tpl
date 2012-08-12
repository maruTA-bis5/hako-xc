<{if $isEmpty}>
<a href="<{$GLOBALS.THIS_FILE}>?mode=coonf"><{$smarty.const._MD_HAKO_FIND_NEW_ISLAND}></a>
<{else}>
<{$smarty.const._MD_HAKO_IS_NOT_EMPTY}>
<{/if}>
<table class="hako" style="width:100%;" border="0" cellspacing="5">
  <td style="width:40%" nowrap>
    <h1><{$init.title}></h1>
    <h2 class="Turn"><{$smarty.const._MD_HAKO_TURN}><{$hako.islandTurn}>    <{$find_island}></h2>
  </td>
  <td style="width:40%;" nowrap>
    <{php}>nl2br($init["topMessage"]);<{/php}>
  </td>
</table>

<{if $isDebug}>
<form action="<{$GLOBALS.THIS_FILE}>" method="post">
  <input type="hidden" name="mode" value="debugTurn" />
  <input type="submit" value="<{$smarty.const._MD_HAKO_DEBUGTURN}>" />
</form>
<{/if}>

<hr />

{* HtmlTop#lastModified() *}
<h2 class="lastModified"><{$smarty.const._MD_HAKO_LAST_MODIFIED}><{$hako.lastTurn}> : <{$timeString}>
<span style="font-weight:normal;">
  <span id="remain_time"><layer name="remain_time"></layer></span>
  <script type="text/javascript"> <!--
    var nextTime = <{$hako.islandLastTime}> + <{$init.unitTime}>;
    var clientTime = new Date();
    clientTime = Math.floor(CloentTime / 1000);
    nextTime = nextTime + (ClientTime - <{$server_time}>);
    remainTime(nextTime);
  //-->
  </script>
</span>
</h2>
{* end of HtmlTop#lastModified() *}

<{if $xoops_user}>
<div id="MyIsland">
  <h2><{$smarty.const._MD_HAKO_GO_TO_OWN_ISLAND}></h2>
  <form action="<{GLOBALS.THIS_FILE}>" method="post">
    <{$smarty.const._MD_HAKO_SELECT_OWN_ISLAND}><br />
    <select name="islandid">
      <{$hako.islandList}>
    </select>
    <input type="hidden" name="mode" value="owner" />
    <script type="text/javascript"><!--
      document.write('inpyt type="hidden" name="developemode" value="java" />');
    //--></script>
    <noscript><input type="hidden" name="developemode" value="cgi"></noscript>
    <{$smarty.const._MD_HAKO_TO}> <input type="submit" value="<{$smarty.const._MD_HAKO_GO_TO_DEVEL}>" />
  </form>
</div>
<{/if}>

<hr />

<div id="IslandView">
  <h2><{$smarty.const._MD_HAKO_CURRENT_ISLANDS}></h2>
  <p><{$smarty.const._MD_HAKO_SIGHTSEEING_MESSAGE}>
  &nbsp;[&nbsp;<a href="<{$GLOBALS.THIS_FILE}>#recent"><{$smarty.const._MD_HAKO_RECENT_EVENTS}></a>&nbsp;]
  &nbsp;[&nbsp;<a href="<{$GLOBALS.THIS_FILE}>#found"><{$smarty.const._MD_HAKO_FOUND_LOG}></a>&nbsp;]
  </p>
  <table class="hako" border="1" style="width:100%">
    <tr>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_RANK}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_ISLAND}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_POPULATION}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_AREA}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_MONEY}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_FOOD}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_FARM}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_FACTORY}><{$init._tagTH}></th>
      <th <{$init.bgTitleCell}>><{$init.tagTH_}><$smarty.const._MD_HAKO_TH_PIT}><{$init._tagTH}></th>
    </tr>

    {* island info *}
    <{foreach from="islands" item="island" key="key" name="island_info_loop"}>
    <tr>
      <th <{$init.bgNumberCell}> rowspan="2"><{$init.tagNumber_}><{$island.id}><{$init._tagNumber}></th>
      <td <{$init.bgNameCell}> rowspan="2">
        <a href="<{$GLOBALS.THIS_FILE}>?sight=<{$island.id}>"><{$island.name}></a>
        <{$island.monster}>
        <{$island.prize}>
      </td>
      <td <{$init.bgInfoCell}>><{$island.pop}></td>
      <td <{$init.bgInfoCell}>><{$island.area}></td>
      <td <{$init.bgInfoCell}>><{$island.money}></td>
      <td <{$init.bgInfoCell}>><{$island.food}></td>
      <td <{$init.bgInfoCell}>><{$island.farm}></td>
      <td <{$init.bgInfoCell}>><{$island.factory}></td>
      <td <{$init.bgInfoCell}>><{$island.mountain}></td>
    </tr>
    <tr>
      <td <{$init.bgCommentCell}> colspan="7"><{$init.tagTH_}><{$island.owner}> : <{$init._tagTH}><{$island.comment}></td>
    </tr>
    <{/foreach}>
  </table>

  <hr />

  {* HtmlTop#logprintTop() *}
  <div id="RecentlyLog"><a name="recent"></a>
  <h2><{$smaty.const._MD_HAKO_RECENTLY_LOG}></h2>
  <{$log}>
  </div>
  {* end of HtmlTop#logprintTop() *}

  {* HtmlTop#historyPrint() *}
  <div id="HistoryLog"><a name="found"></a>
  <h2><{$smarty.const._MD_HAKO_FOUND_LOG}></h2>
  <{$found}>
  </div>
