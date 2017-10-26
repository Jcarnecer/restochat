"use strict";

function getConversationName(conversation) {
    let conversationName = '';
	if (conversation.name) {
		conversationName = conversation.name;
	} else {
		$.each(conversation.participants, function(index, participant) {
			if (participant.id !== userId) {
				conversationName += participant.first_name + ' ' + participant.last_name;
				conversationName += (conversation.participants.length > 2 && index < conversation.participants.length - 1) ? ", " : "";
			}
		});
	}
	return conversationName;
}


function getMessageStatus(message) {
	let status = "Delivered";
	return status;
}