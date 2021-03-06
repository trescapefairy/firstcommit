<?php
namespace Sooh\Base\Crond;
/**
 * 定时任务控制类 (如果默认的routeUri是__=apicopartner/hourly,那么定时任务中要加上__=apicopartner/hourly)
 * a) standalone目录下所有的任务都会被独立执行；
 * b) 其他的有依赖关系的任务要放在同一目录下（不能取名standalone）
 * c) 加载任务只扫描第一层目录，可以为任务建立更深的子目录
 * 
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Ctrl {
	protected $_baseDir;
	protected $_baseNamespace='';
	protected $_useSubdirAsNamespace=null;
	protected $_isManual=false;
	protected $_router;
	/**
	 *
	 * @var \Sooh\Base\Crond\Log
	 */
	protected $_log;
	/**
	 * 
	 * @param string $baseDir
	 * @param string $routerUriBase
	 * @param \Sooh\Base\Crond\Log $loger
	 */
	public function __construct($baseDir, $routerUriBase="__=apicopartner/hourly",$loger=null)
	{
		$this->_baseDir=$baseDir;
		$this->_router = $routerUriBase;
		if($loger===null)$this->_log=new \Sooh\Base\Crond\Log ();
		else $this->_log = $loger;
		$this->_log->ensureCrondTable();
	}
	/**
	 * 
	 * @param string $baseNamespace
	 * @param boolean $useSubdir
	 * @return \Sooh\Base\Crond\Ctrl
	 */
	public function initNamespace($baseNamespace='',$useSubdir=false)
	{
		if(!empty($baseNamespace)){
			if(substr($baseNamespace,-1)!=='\\')$this->_baseNamespace=$baseNamespace.'\\';
			else $this->_baseNamespace=$baseNamespace;
		}
		$this->_useSubdirAsNamespace=$useSubdir;
		return $this;
	}
	protected $_planToRun=array();
	/**
	 * 执行函数
	 * @param string $task 从request中获取的task的值
	 * @param int $ymdh  从request中获取的ymdh的值
	 * @throws \ErrorException
	 */
	public function runManually($task=null, $ymdh=null)
	{
		$this->_log->writeCrondLog(null, __FUNCTION__."($task=null, $ymdh=null)");
		clearstatcache();
		$this->_isManual=true;
		$dt = \Sooh\Base\Time::getInstance();
		if($ymdh!==null){
			if($ymdh>99991231){//yyyymmddhh
				$dt->mktime($ymdh%100, 0, 0, floor($ymdh/100));
			}elseif($ymdh<19000101){
				throw new \ErrorException('ymdh specified error:'.$ymdh);
			}else{//yyyymmdd
				$dt->mktime(0, 0, 0, $ymdh);
			}
		}
		

		if(empty($task)){
			throw new \ErrorException('you need specify a task');
		}elseif(substr($task,-1)=='*'){
			$dir=substr($task,0,-2);
			$tasks = $this->getTasks($dir);
			foreach($tasks as $task=>$fullpath){
				$this->newTask($dir,$task,$fullpath);
			}
		}else{
			$dir=explode('.',$task);
			if(sizeof($dir)!==2)throw new \ErrorException('task specified error:'.$task);
			$this->newTask($dir[0],$dir[1],$this->_baseDir.'/'.  implode('/', $dir).'.php');
		}
		$this->loop($dt);

		//$this->_log->writeCrondLog(null, __FUNCTION__." done");
	}
	public function runCrond($task=null, $ymdh=null)
	{
		//$this->_log->writeCrondLog(null, __FUNCTION__."($task=null, crond)");
		clearstatcache();
		$ini = \Sooh\Base\Ini::getInstance();
		$this->_isManual=false;
		$dt = \Sooh\Base\Time::getInstance();
		if($dt->hour==3){
			$this->_log->remoreCrondLogExpired();
		}

		if(empty($task)){// loop and check all new paths in a hour
			$subdirs0 = array();
			for($i=0;$i<6;$i++){
				$subdirs = $this->getTaskDirs();
				//$this->_log->writeCrondLog(null, __FUNCTION__." scan subdirs,".sizeof($subdirs).' found');
				foreach ($subdirs as $subdir){
					if(!in_array($subdir, $subdirs0)){
						$this->forkTask($subdir.'.*');
						$subdirs0[]=$subdir;
					}
				}
				if($i<5){
					sleep(600);
					clearstatcache();
				}
			}
		}elseif(substr($task,-1)=='*'){// loop and check all new class in specify path in a hour
			$dir = substr($task,0,-2);
			$task0=array();
			if($dir=='Standalone'){
				for($i=0;$i<6;$i++){
					$tasks = $this->getTasks($dir);
					//$this->_log->writeCrondLog(null, __FUNCTION__." scan Standalone,".sizeof($tasks).' tasks found');
					foreach($tasks as $task=>$fullpath){
						if(!in_array($task, $task0)){
							$task0[]=$task;
							$this->forkTask('Standalone.'.$task);
						}
					}
					if($i<5){
						sleep(600);
						clearstatcache();
					}
				}
			}else{
				for($i=0;$i<60;$i++){
					if($i%10==0){
						$tasks = $this->getTasks($dir);
						//$this->_log->writeCrondLog(null, __FUNCTION__." scan $dir,".sizeof($tasks).' tasks found');
						foreach($tasks as $task=>$fullpath){
							if(!in_array($task, $task0)){
								$task0[]=$task;
								$this->newTask($dir,$task,$fullpath);

							}
						}
						clearstatcache();
					}
					$ret = $this->loop($dt);
					if($i>50&&$ret==false)break;
					$dt->sleepTo($dt->hour, $dt->minute+1,$ini->get('crondBatchSleepTo')-0);
				}
			}
		}else{
			$r = explode('.', $task);
			$this->newTask($r[0],$r[1],$this->_baseDir.'/'.$r[0].'/'.$r[1].'.php');
			for($i=0;$i<60;$i++){
				if($this->loop($dt)){
					//$this->_log->writeCrondLog(null, __FUNCTION__." run single,needs sleep to ".$dt->hour.":".($dt->minute+1));
					$dt->sleepTo($dt->hour, $dt->minute+1,$ini->get('crondBatchSleepTo')-0);
				}else{
					//$this->_log->writeCrondLog(null, __FUNCTION__." run single,end no more");
					break;
				}
			}
		}

		//$this->_log->writeCrondLog(null, __FUNCTION__." done");
	}
	protected function forkTask($task)
	{
		$cmd = \Sooh\Base\Tools::runBackground($this->_router.'&task='.$task.'&type=crond');
		//$this->_log->writeCrondLog(null, __FUNCTION__."($task) with cmd=$cmd");
		error_log($cmd);
	}
	protected $_tasks=array();
	/**
	 * 
	 * @param string $taskname
	 * @param string $fullpatch
	 * @param \Sooh\Base\Crond\Task $_ignore_
	 */
	protected function newTask($subdir,$taskname,$fullpatch,$_ignore_=null)
	{
		//$this->_log->writeCrondLog(null,__FUNCTION__."($taskname,$fullpatch)");
		if(!isset($this->_tasks[$taskname])){
			include $fullpatch;
			if($this->_useSubdirAsNamespace!==null){
				if($this->_useSubdirAsNamespace){
					$realclass = $this->_baseNamespace.$subdir.'\\'.$taskname;
				}else{
					$realclass = $this->_baseNamespace.$taskname;
				}
			}else{
				$realclass = $taskname;
			}
			
			//$this->_log->writeCrondLog(null,__FUNCTION__."new $realclass() from $fullpatch");
			$_ignore_ = new $realclass($this->_isManual);
			//$this->_log->writeCrondLog(null,__FUNCTION__."new $realclass() ->init()");
			$_ignore_->init();
			$_ignore_->subdir=$subdir;
			//$this->_log->writeCrondLog(null,__FUNCTION__."new $realclass() created");
			$this->_tasks[$taskname]=$_ignore_;
			$dt = \Sooh\Base\Time::getInstance();
			$this->_log->updCrondStatus($dt->YmdFull, $dt->hour, $subdir.'.'.$taskname, 'inited',0, $this->_isManual?1:0);
		}
	}
	/**
	 * @param \Sooh\Base\Time $dt 
	 * @param \Sooh\Base\Crond\Task $_ignore_
	 * @return boolean has task(s) needs to be run next loop
	 */
	protected function loop($dt,$_ignore_=null)
	{
		//$this->_log->writeCrondLog(null,__FUNCTION__."(".date('Y-m-d H:i:s',$dt->timestamp()).")");
		$waiting=  array_keys($this->_tasks );
		$done=array();
		$total=sizeof($waiting);
		$retry=6;
		while($retry>0){
			$retry--;
			$numBool=0;
		//	$this->_log->writeCrondLog(null, "trace while($retry)");
			
			$exec=0;
			foreach ($waiting as $taskindex){
				$_ignore_ = $this->_tasks[$taskindex];
				//$this->_log->writeCrondLog($taskindex, "trace while($retry exec:$exec done:".sizeof($done)." bool:$numBool total:$total):check:$taskindex");
				if(isset($done[$taskindex]))continue;
				elseif(is_bool($_ignore_)){$done[$taskindex]=$_ignore_;$numBool++;}
				else{
					$req = $_ignore_->requirement();
					if(empty($req) || $done[$req]){
						//$this->_log->writeCrondLog($taskindex, "trace while($retry exec:$exec done:".sizeof($done)." bool:$numBool total:$total):run:$taskindex");
						try{
							$exec++;
							$ret = $_ignore_->run($dt);
							//$this->_log->writeCrondLog($taskindex, "trace while($retry exec:$exec done:".sizeof($done)." bool:$numBool total:$total):ret:$taskindex=".  var_export($ret,true).' lastMsg='.$_ignore_->lastMsg. ' '. ($_ignore_->toBeContinue?'continue':'done'));
						} catch (\ErrorException $e) {
							$ret = $_ignore_->onError($e);
							$this->_log->writeCrondLog($taskindex, "trace while($retry exec:$exec done:".sizeof($done)." bool:$numBool total:$total):error found(".$e->getMessage().") :$taskindex=".  var_export($ret,true).' lastMsg='.$_ignore_->lastMsg. ' '. ($_ignore_->toBeContinue?'continue':'done'));
						}
						if($ret!==false || $_ignore_->lastMsg!==null){
							$this->_log->updCrondStatus($dt->YmdFull, $dt->hour, $_ignore_->subdir.'.'.$taskindex, $_ignore_->lastMsg, $ret?1:0, $this->_isManual?1:0);
						}
						if($ret)$_ignore_->counterOk++;
						$done[$taskindex]=$ret;
						if($_ignore_->toBeContinue==false){
							$_ignore_->free ();
							$this->_tasks[$taskindex]=$_ignore_->counterOk>0;
						}
					}else {
						//$this->_log->writeCrondLog($taskindex, "trace while($retry exec:$exec done:".sizeof($done)." bool:$numBool total:$total):wait next retry for $req:$taskindex");
					}
				}
			}
			if($exec==0){
				
				if($numBool==$total)return false;
//				if(sizeof($done)!==$total)
//					$this->_log->writeCrondLog(null, "task require mismatch exec:$exec done:".sizeof($done)." for:".implode(',', $waiting));
				break;
			}
		}
		return true;
	}
	
	protected function getTaskDirs()
	{
		$subdirs=array();
		$dh = opendir($this->_baseDir);
		if(!$dh)die($this->_log->writeCrondLog(null, "read base dir failed:".$this->_baseDir));
		while(false!==($subdir=  readdir($dh))){
			if($subdir[0]!=='.' && is_dir($this->_baseDir.'/'.$subdir)){
				$subdirs[]=$subdir;
			}
		}
		closedir($dh);
		sort($subdirs);
		return $subdirs;
	}
	protected function getTasks($path)
	{
		$classes=array();

		$dh = opendir($this->_baseDir.'/'.$path);
		if(!$dh)die($this->_log->writeCrondLog(null, "read tasks in dir failed:".$this->_baseDir.'/'.$path));
		while(false!==($f=  readdir($dh))){
			if(substr($f,-4)=='.php'){
				$classes[substr($f,0,-4)]=$this->_baseDir.'/'.$path.'/'.$f;
			}
		}
		closedir($dh);
		return $classes;
	}
}
