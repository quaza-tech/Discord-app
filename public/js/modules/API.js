const API = (function () {
    'use strict';

    const BASE = 'php/api';

    // Helper générique
    function request(url, method, data) {
        var options = {
            url: url,
            type: method,
            success: function (response) {
                console.log('API Success: ' + url.split('/').pop(), response);
            },
            error: function(xhr, status, error) {
                    console.error('❌ API Error:', url, xhr.responseText);
                    
                    // Gestion des erreurs selon le code HTTP
                    if (xhr.status === 401) {
                        Validation.showToast("Session expirée", "error", 3000);
                        setTimeout(function() {
                            window.location.href = 'index.html';
                        }, 2000);
                    } else if (xhr.status === 500) {
                        Validation.showToast("Erreur serveur", "error", 3000);
                    } else if (xhr.status === 403) {
                        Validation.showToast("Accès refusé", "error", 3000);
                    } else {
                        Validation.showToast("Erreur de connexion", "error", 3000);
                    }
                
                }
        };

        if (data) options.data = data;

        return $.ajax(options);
    }
    // ============================================
    // AUTH
    // ============================================
    function login(email, mot) {
        return request(BASE + '/auth/login.php', 'POST', { email, mot });
    }

    function register(email, pseudo, mdp) {
        return request(BASE + '/auth/register.php', 'POST', { email, pseudo, mdp });
    }

    function logout() {
        window.location.href = BASE + '/auth/logout.php';
    }

    function requestPasswordReset(email) {
        return request(BASE + '/auth/password.php', 'POST', { email });
    }

    // ============================================
    // USERS
    // ============================================
    function getUserInfo() {
        return request(BASE + '/users/index.php', 'GET');
    }

    function getUser(userId, serverId) {
        var params = { id: userId };
        if (serverId) params.server_id = serverId;
        return request(BASE + '/users/index.php?' + $.param(params), 'GET');
    }

    // ============================================
    // SERVERS
    // ============================================
    function getUserServers() {
        return request(BASE + '/servers/index.php', 'GET');
    }

    function getAvailableServers() {
        return request(BASE + '/servers/index.php?available', 'GET');
    }

    function joinServer(serverId) {
        return request(BASE + '/servers/index.php', 'POST', { server_id: serverId });
    }

    // ============================================
    // CHANNELS
    // ============================================
    function getChannels(serverId) {
        return request(BASE + '/channels/index.php?server_id=' + serverId, 'GET');
    }

    function getMessages(serverId, channelId) {
        return request(BASE + '/channels/messages.php?server_id=' + serverId + '&channel_id=' + channelId, 'GET');
    }

    function sendMessage(serverId, salonId, texte) {
        return request(BASE + '/channels/messages.php', 'POST', { salonid: salonId, texte: texte });
    }

    function editMessage(messageId, texte, convId) {
        if (convId) {
            // MP
            return request(BASE + '/conversations/index.php', 'PUT', { id: messageId, texte: texte });
        }
        // Serveur
        return request(BASE + '/channels/messages.php', 'PUT', { id: messageId, texte: texte });
    }

    function deleteMessage(messageId, convId) {
        if (convId) {
            // MP
            return request(BASE + '/conversations/index.php', 'DELETE', { id: messageId });
        }
        // Serveur
        return request(BASE + '/channels/messages.php', 'DELETE', { id: messageId });
    }

    // ============================================
    // CONVERSATIONS (MP)
    // ============================================
    function getConv() {
        return request(BASE + '/conversations/index.php', 'GET');
    }

    function getMP(convId) {
        return request(BASE + '/conversations/index.php?id=' + convId, 'GET');
    }

    function sendMP(convId, texte) {
        return request(BASE + '/conversations/index.php', 'POST', { convID: convId, texte: texte });
    }

    // ============================================
    // MEMBERS
    // ============================================
    function getMemberRole(convId, serverId) {
        var params = serverId ? { server_id: serverId } : { conv_id: convId };
        return request(BASE + '/members/index.php?' + $.param(params), 'GET');
    }

    // ============================================
    // EXPORTS
    // ============================================
    return {
        // Auth
        login,
        register,
        logout,
        requestPasswordReset,
        // Users
        getUserInfo,
        getUser,
        // Servers
        getUserServers,
        getAvailableServers,
        joinServer,
        // Channels
        getChannels,
        getMessages,
        sendMessage,
        editMessage,
        deleteMessage,
        // Conversations
        getConv,
        getMP,
        sendMP,
        // Members
        getMemberRole
    };

})();

// =============================================================================
// API.JS — Correspondance anciens fichiers → nouvelles routes REST
// =============================================================================
// Ancien                        Nouveau
// ─────────────────────────────────────────────────────────────────────────────
// test_connexion.php         →  POST   api/auth/login.php
// inscription.php            →  POST   api/auth/register.php
// log_out.php                →  GET    api/auth/logout.php
// changement_mdp.php         →  POST   api/auth/password.php
// reset_password.php         →  GET    api/auth/password.php?token=X
// get_user_info.php          →  GET    api/users/index.php
// getUser.php                →  GET    api/users/index.php?id=X
// recup_serv_in.php          →  GET    api/servers/index.php
// recup_serveur.php          →  GET    api/servers/index.php?available
// join_server.php            →  POST   api/servers/index.php
// recup_salon.php            →  GET    api/channels/index.php?server_id=X
// recup_message.php          →  GET    api/channels/messages.php?server_id=X&channel_id=Y
// ajax.php                   →  POST   api/channels/messages.php
// edit_message.php (server)  →  PUT    api/channels/messages.php
// supp_message.php (server)  →  DELETE api/channels/messages.php
// recup_convSalon.php        →  GET    api/conversations/index.php
// recup_private_Message.php  →  GET    api/conversations/index.php?id=X
// send_mp.php                →  POST   api/conversations/index.php
// edit_message.php (mp)      →  PUT    api/conversations/index.php
// supp_message.php (mp)      →  DELETE api/conversations/index.php
// getMemberRole.php          →  GET    api/members/index.php?server_id=X
// =============================================================================
