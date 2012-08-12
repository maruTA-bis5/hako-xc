<?php
/**
 * @package hako-xc
 * @author bis5 <bis5@bis5.mydns.jp>
 */

include dirname(dirname(__FILE__)) . "/mainfile.php";

$controller =& XCube_Root::getSingleton();
$controller->mController->executeHeader();

$render =& $controller->mContext->mModule->getRenderTarget();
$render->setTemplateName('hako-xc' . '_top.tpl');

$controler->mController->executeView();
