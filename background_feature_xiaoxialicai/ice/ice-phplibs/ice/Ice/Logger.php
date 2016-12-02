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
// Generated from file `Logger.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//


global $Ice__t_Logger;

if(!interface_exists('Ice_Logger',false))
{
    interface Ice_Logger
    {
        public function _print($message);
        public function trace($category, $message);
        public function warning($message);
        public function error($message);
        public function getPrefix();
        public function cloneWithPrefix($prefix);
    }

    $Ice__t_Logger = IcePHP_defineClass('::Ice::Logger', 'Ice_Logger', -1, true, false, $Ice__t_Object, null, null);
}
?>