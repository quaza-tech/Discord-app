const Appstat = (function(){
    'use strict';

    let currentServer = null;
    let currentChannel = null;
    let user = null;

    function getCurrentServer(){
        return currentServer;
    }
    function getCurrentChannel(){
        return currentChannel;
    }
    function getCurrentUser(){
        return user;
    }
    function setCurrentServer(serverId){
        currentServer = serverId;
    }
    function setCurrentChannel(channelId){
        currentChannel = channelId;
    }
    function setCurrentUser(userId){
        user = userId;
    }
    return {
        getCurrentServer : getCurrentServer,
        getCurrentChannel : getCurrentChannel,
        getCurrentUser : getCurrentUser,
        setCurrentServer : setCurrentServer,
        setCurrentChannel : setCurrentChannel,
        setCurrentUser :setCurrentUser
    };
})();
