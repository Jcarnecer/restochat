const baseUrl = "http://localhost/chat";

function getConversations() {
    return $.ajax({
        url: baseUrl + '/api/dev/conversations',
        method: 'GET'
    });
}

function getConversation(conversationId) {
    return $.ajax({
        url: baseUrl + '/api/dev/conversations/' + conversationId,
        method: 'GET',
    });
}

function createConversation(companyId, conversationDetails) {
    return $.ajax({
        url: baseUrl + '/api/dev/companies/' + companyId + '/conversations',
        method: 'POST',
        data: conversationDetails,
        async: false
    });
}

function getConversationMessages(conversationId) {
    return $.ajax({
        url: baseUrl + '/api/dev/conversations/' + conversationId + '/messages',
        method: 'GET'
    });
}

function createConversationMessage(conversationId, messageDetails) {
    return $.ajax({
        url: baseUrl + '/api/dev/conversations/' + conversationId + '/messages',
        method: 'POST',
        data: messageDetails
    });
}

function getCompanyUsers(companyId, userDetails) {
    return $.ajax({
        url: baseUrl + '/api/dev/companies/' + companyId + '/users',
        method: 'GET',
        data: userDetails,
    });
}

function getUserConversations(userId) {
    return $.ajax({
        url: baseUrl + '/api/dev/users/' + userId + '/conversations',
        method: 'GET'
    });
}

function createMessageReads(messageId, messageReadDetails) {
    return $.ajax({
        url: baseUrl + '/api/dev/messages/' + messageId + '/reads',
        method: 'POST',
        data: messageReadDetails
    });
}