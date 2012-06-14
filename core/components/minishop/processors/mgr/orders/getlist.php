<?php
/**
 * Get a list of Orders
 *
 * @package minishop
 * @subpackage processors
 */
if (!$modx->hasPermission('view')) {return $modx->error->failure($modx->lexicon('ms.no_permission'));}

$isLimit = !empty($_REQUEST['limit']);
$start = $modx->getOption('start',$_REQUEST,0);
$limit = $modx->getOption('limit',$_REQUEST,$modx->getOption('default_per_page'));
$sort = $modx->getOption('sort',$_REQUEST,'created');

$dir = $modx->getOption('dir',$_REQUEST,'DESC');
$warehouse = $modx->getOption('warehouse', $_REQUEST, $_SESSION['minishop']['warehouse']);
$status = $modx->getOption('status', $_REQUEST, $_SESSION['minishop']['status']);
$_SESSION['minishop']['warehouse'] = $warehouse;
$_SESSION['minishop']['status'] = $status;

$query = $modx->getOption('query',$_REQUEST, 0);
$c = $modx->newQuery('ModOrders');

if (!empty($status)) {
	$c->andCondition(array('status' => $status));
}

if (!empty($query)) {
	$c->andCondition(array('num:LIKE' => '%'.$query.'%'));
}
if (!empty($warehouse)) {
	$c->andCondition(array('wid' => $warehouse));
}

$count = $modx->getCount('ModOrders',$c);

$c->sortby($sort,$dir);
if ($isLimit) $c->limit($limit, $start);
$orders = $modx->getCollection('ModOrders',$c);

$arr = array();
foreach ($orders as $v) {
    $tmp = $v->toArray();
	$tmp['fullname'] = $v->getFullName();
	$tmp['warehousename'] = $v->getWarehouseName();
	$tmp['sum'] += $v->getDeliveryPrice();
	$arr[]= $tmp;
	
}
return $this->outputArray($arr, $count);
