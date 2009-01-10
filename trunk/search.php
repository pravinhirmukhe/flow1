<?php
//引入图片插件入口文件
require_once('./wp-content/plugins/nextgen-gallery/ngg-config.php');
require_once('./inc/php/cfg.php');

$page_title='FOTO';
$title='Search'.$cfg['sitetitle'];
require_once './inc/html/head.html';
?>
<div id='main'>
<!-- SiteSearch Google -->
<form method="get" action="http://www.google.com/custom" target="google_window">
<table border="0" align="center" bgcolor="#ffffff">
<tr><td nowrap="nowrap" height="32">

</td>
<td nowrap="nowrap">
<input type="hidden" name="domains" value="foto.tfbj.cc"></input>
<label for="sbi" style="display: none">Enter your search terms</label>
<input type="text" name="q" size="50" maxlength="255" value="" id="sbi"></input>
<label for="sbb" style="display: none">Submit search form</label>
<input type="submit" name="sa" value="Google Search" id="sbb"></input>
</td></tr>
<tr>
<td>&nbsp;</td>
<td nowrap="nowrap">
<table align="center">
<tr>
<td>
<input type="radio" name="sitesearch" value="" id="ss0"></input>
<label for="ss0" title="Search the Web"><font size="-1" color="#000000">Web </font></label></td>
<td>
<input type="radio" name="sitesearch" value="foto.tfbj.cc" checked id="ss1"></input>
<label for="ss1" title="Search foto.tfbj.cc"><font size="-1" color="#000000">foto.tfbj.cc</font></label></td>
</tr>
</table>
<input type="hidden" name="client" value="pub-6834157029902877"></input>
<input type="hidden" name="forid" value="1"></input>
<input type="hidden" name="ie" value="UTF-8"></input>
<input type="hidden" name="oe" value="UTF-8"></input>
<input type="hidden" name="cof" value="GALT:#008000;GL:1;DIV:#ffffff;VLC:663399;AH:center;BGC:FFFFFF;LBGC:ffffff;ALC:0000FF;LC:0000FF;T:000000;GFNT:0000FF;GIMP:0000FF;LH:50;LW:117;L:http://foto.tfbj.cc/inc/img/google-search-logo.jpg;S:http://foto.tfbj.cc/;FORID:1"></input>
<input type="hidden" name="hl" value="en"></input>
</td></tr></table>
</form>
<!-- SiteSearch Google -->
</div>
<?php
require_once './inc/html/foot.html';?>
