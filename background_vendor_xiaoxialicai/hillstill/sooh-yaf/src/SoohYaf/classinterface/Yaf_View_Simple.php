<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Yaf_View_Simple
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Yaf_View_Simple {
	/**
	 * 
	 * @param mixed $name string as key for value OR array(k1=>v1,k2=>v2)
	 * @param mixed $value
	 * @return \Yaf_View_Simple
	 */
	public function assign( $name , $value = NULL ){return $this;}
	/**
	 * 渲染页面并返回渲染结果字符串
	 * @param string $view_path
	 * @param array $tpl_vars
	 */
	public function render($view_path, $tpl_vars){return "<div>html-string-byTpl</div>";}
	/**
	 * 渲染页面输出渲染结果字符串
	 * @param string $view_path
	 * @param array $tpl_vars
	 */
	public function display(string $view_path, array $tpl_vars= NULL){}
	/**
	 * 设置模板的基目录, 默认的Yaf_Dispatcher会设置此目录为APPLICATION_PATH . "/views".
	 * @return boolean
	 */
	public function setScriptPath(string $view_directory){return true;}
	public function getScriptPath(){return 'script-path';}
	
	
}
