"use strict";

function getConversationName(conversation) {
    let conversationName = '';
	if (conversation.name) {
		conversationName = conversation.name;
	} else {
		$.each(conversation.participants, function(index, participant) {
			if (participant.id !== userId) {
				conversationName += participant.first_name + ' ' + participant.last_name;
				conversationName += (index < conversation.participants.length - 2) ? ', ' : '';
			}
		});
	}
	return conversationName;
}


function getMessageStatus(message) {
	let status = "Delivered";
	/*
	if (message.reads.length) {
		status = "Read by ";
		$.each(message.reads, function(index, read) {
			status += read.first_name + ' ' + read.last_name;
			status += (index < message.reads.length - 1) ? ', ' : '';
		});
	}*/
	return status;
}


function init() {

	// Loads sidebar content, populate with users' conversations
	getUserConversations(userId)
		.then(function(response) {
			let conversations = $.parseJSON(response);

			$('#sidebar').find('.shimmer').hide();
			$_conversationList.append(`<li><a data-toggle="modal" data-target="#createConversationModal">New Message</a></li>`);
			$.each(conversations, function(index, conversation) {
				let conversationName = getConversationName(conversation);
				$_conversationList.append(`
					<li>
						<a href="${baseUrl}/messages/${conversation.id}" class="sidebar__item ${conversation.name === 'General' ? 'active': ''}" data-id="${conversation.id}">
							${conversationName}
						</a>
					</li>`);
			});

			$('.sidebar__item').toggleActive();
		});


	getCompanyUsers(companyId)
		.then(function(response) {
			let users = $.parseJSON(response);

			$.each(users, function(index, user) {
				if (user.id !== userId) {
					$('.participants').append(`
						<div class="form-check">
							<label class="form-check-label">
								<input class="form-check-input" type="checkbox" name="participants[]" value="${user.id}">
								${user.first_name} ${user.last_name}
							</label>
						</div>`);
				}
			});
		});
}


init();