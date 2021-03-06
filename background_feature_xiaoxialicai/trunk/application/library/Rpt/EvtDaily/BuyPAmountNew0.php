<?php
namespace Rpt\EvtDaily;
/**
 * 日行为统计管理类：新注册用户注册当日购买金额
 * -- flgext01  prdtType   flgext02  uType
 * 注意：这里的clientType始终是0
 * @author Simon Wang <hillstill_simon@163.com>
 */
class BuyPAmountNew0 extends Base{
	protected function actName(){return 'BuyPAmountNew0';}
	public static function displayName(){return '当日注册并购买金额';}
	protected function basement() {return 100;}
}
