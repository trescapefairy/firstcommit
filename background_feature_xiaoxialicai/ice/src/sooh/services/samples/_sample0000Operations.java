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
// Generated from file `samples.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

package sooh.services.samples;

public interface _sample0000Operations
{
    void sayhi(String who, Ice.Current __current);

    String echohi(String who, Ice.Current __current);

    void forwardAsy(String serviceClass, String serviceFunc, String jsonedArray, Ice.Current __current);

    String forwardSyn(String serviceClass, String serviceFunc, String jsonedArray, Ice.Current __current);
}