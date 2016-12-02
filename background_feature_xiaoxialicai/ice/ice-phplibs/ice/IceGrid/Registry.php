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
// Generated from file `Registry.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once __DIR__.'/../IceGrid/Exception.php';
require_once __DIR__.'/../IceGrid/Session.php';
require_once __DIR__.'/../IceGrid/Admin.php';

global $IceGrid__t_Registry;
global $IceGrid__t_RegistryPrx;

if(!interface_exists('IceGrid_Registry',false))
{
    interface IceGrid_Registry extends Ice_Object
    {
        public function createSession($userId, $password);
        public function createAdminSession($userId, $password);
        public function createSessionFromSecureConnection();
        public function createAdminSessionFromSecureConnection();
        public function getSessionTimeout();
        public function getACMTimeout();
    }

    class IceGrid_RegistryPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::IceGrid::Registry', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::IceGrid::Registry', $facet);
        }

        public static function ice_staticId()
        {
            return '::IceGrid::Registry';
        }
    }

    $IceGrid__t_Registry = IcePHP_defineClass('::IceGrid::Registry', 'IceGrid_Registry', -1, true, false, $Ice__t_Object, null, null);

    $IceGrid__t_RegistryPrx = IcePHP_defineProxy($IceGrid__t_Registry);

    IcePHP_defineOperation($IceGrid__t_Registry, 'createSession', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_SessionPrx, false, 0), array($IceGrid__t_PermissionDeniedException));
    IcePHP_defineOperation($IceGrid__t_Registry, 'createAdminSession', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_string, false, 0)), null, array($IceGrid__t_AdminSessionPrx, false, 0), array($IceGrid__t_PermissionDeniedException));
    IcePHP_defineOperation($IceGrid__t_Registry, 'createSessionFromSecureConnection', 0, 0, 0, null, null, array($IceGrid__t_SessionPrx, false, 0), array($IceGrid__t_PermissionDeniedException));
    IcePHP_defineOperation($IceGrid__t_Registry, 'createAdminSessionFromSecureConnection', 0, 0, 0, null, null, array($IceGrid__t_AdminSessionPrx, false, 0), array($IceGrid__t_PermissionDeniedException));
    IcePHP_defineOperation($IceGrid__t_Registry, 'getSessionTimeout', 2, 1, 0, null, null, array($IcePHP__t_int, false, 0), null);
    IcePHP_defineOperation($IceGrid__t_Registry, 'getACMTimeout', 2, 1, 0, null, null, array($IcePHP__t_int, false, 0), null);
}
?>