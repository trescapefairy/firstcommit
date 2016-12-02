<?php
// **********************************************************************
//
// Copyright (c) 2003-2016 ZeroC, Inc. All rights reserved.
//
// This copy of Ice is licensed to you under the terms described in the
// ICE_LICENSE file included in this distribution.
//
// **********************************************************************
//
// Ice version 3.6.2
//
// <auto-generated>
//
// Generated from file `ImplicitContext.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../Ice/LocalException.php';
require_once __DIR__.'/../Ice/Current.php';

global $Ice__t_ImplicitContext;

if(!interface_exists('Ice_ImplicitContext',false))
{
    interface Ice_ImplicitContext
    {
        public function getContext();
        public function setContext($newContext);
        public function containsKey($key);
        public function get($key);
        public function put($key, $value);
        public function remove($key);
    }

    $Ice__t_ImplicitContext = IcePHP_defineClass('::Ice::ImplicitContext', 'Ice_ImplicitContext', -1, true, false, $Ice__t_Object, null, null);
}
?>