(function($) {

	const userId = $('meta[name="user_id"]').attr('content');
	const companyId = $('meta[name="company_id"]').attr('content');

	const $_conversationList = $('.conversation-list');
	const $_messageArea = $('.message-area');
	const $_createMessageForm = $('#createMessageForm');
	const $_messageBody = $_createMessageForm.find('[name="body"]');
	const $_createConversationModal = $('#createConversationModal');
	const $_createConversationForm = $('#createConversationForm');

	const socket = io('https://socket-simpleapp.herokuapp.com/');

	page.base('/chat-alpha');

	page('/', function() {
		page.redirect('/messages/5TxcjbARH0z');
	});

	page('/messages/:conversationId', function(context) {

		let conversationId = context.params.conversationId;

		getConversation(conversationId).then(function(response) {
			let conversation = $.parseJSON(response);
			let conversationName = getConversationName(conversation);

			document.title = conversationName;
			$('.navbar-brand').html(conversationName);
		});

		getConversationMessages(conversationId).then(function(response) {
			let messages = $.parseJSON(response);

			$_messageArea.html("");

			$.each(messages, function(index, message) {
				let $_message;
				let $_lastMessage = $('.message').last();

				if (message.created_by.id === userId) {
					$_message = $(`
					<div class="message message--primary" data-user="${message.created_by.id}">
						<div class="message__body">
							<div class="message__time"></div>
							<div class="message__bubble">${message.body}</div>
						</div>
					</div>`);
				} else {
					if ($_lastMessage.attr('data-user') === message.created_by.id) {
						$_message = $(`
						<div class="message message--default" data-user="${message.created_by.id}">
							<div class="message__body">
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
					} else {
						$_message = $(`
						<div class="message message--default" data-user="${message.created_by.id}">
							<div class="message__user">${message.created_by.first_name} ${message.created_by.last_name}</div>
							<div class="message__body">
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
					}
				}

				$_message.eventShowTime({timestamp: message.created_at});
				$_messageArea.append($_message);
			});

			$_messageArea.scrollToBottom();

			socket.on('chat message', function(message) {

				if ($(`[data-id="${message.conversation_id}"]`).length) {
					$_conversationList.prepend($(`[data-id="${message.conversation_id}"]`).remove());
					$(`[data-id="${message.conversation_id}"]`).find('.subtitle').html(message.body);
				}


				if (message.created_by.id !== userId && message.conversation_id === conversationId) {
					let $_lastMessage = $('.message').last();
					let $_message;

					if ($_lastMessage.attr('data-user') === message.created_by.id) {
						$_message = $(`
							<div class="message message--default" data-user="${message.created_by.id}">
								<div class="message__body">
									<div class="message__bubble">${message.body}</div>
									<div class="message__time"></div>
								</div>
							</div>`);
					} else {
						$_message = $(`
							<div class="message message--default" data-user="${message.created_by.id}">
								<div class="message__user">${message.created_by.first_name} ${message.created_by.last_name}</div>
								<div class="message__body">
									<div class="message__bubble">${message.body}</div>
									<div class="message__time"></div>
								</div>
							</div>`);
					}

					$_message.eventShowTime({timestamp: message.created_at});
					$_messageArea.append($_message).scrollToBottom();
				}
			});


		});

		$_createMessageForm.off();
		$_createMessageForm.submit(function(event) {
			event.preventDefault();
			if ($_messageBody.val()) {
				let $_message = $(`
					<div class="message message--primary">	
						<div class="message__body">
							<div class="message__time"></div>
							<div class="message__bubble">${$_messageBody.val()}</div>
						</div>
						<div class="message__status">Sending...</div>
					</div>
				`);

				$_message.find('.message__time').hide();
				$_messageArea.append($_message);
				$_messageArea.scrollToBottom();

				createConversationMessage(conversationId, $_createMessageForm.serialize()).then(function(response) {
					let message = $.parseJSON(response);

					$_message.eventShowTime({timestamp: message.created_at});
					$_message.eventShowStatus({status: "Delivered"});
					socket.emit('chat message', message);
				}).fail(function(response) {
					$_message.removeClass('message--primary').addClass('message--danger');
					$_message.find('.message__status').html("Click to resend message");
				}.bind($_message));

				$_messageBody.val("");
			}
		});


		$_createConversationForm.submit(function(event) {
			event.preventDefault();

			let conversationDetails = $_createConversationForm.serializeArray();
			conversationDetails.push({name: 'participants[]', value: userId }, {name: 'company_id', value: companyId });

			createConversation(companyId, conversationDetails).then(function(response) {
				let conversation = $.parseJSON(response);

				$('.sidebar__item').removeClass('active');

				if ($(`[data-id="${conversation.id}"]`).length) {
					$(`[data-id="${conversation.id}"]`).addClass('active');
				} else {
					let conversationName = getConversationName(conversation);

					$_conversationList.append(`
						<li data-id="${conversation.id}">
							<a href="${baseUrl}/messages/${conversation.id}" class="sidebar__item active">
								${conversationName}
								<div class="subtitle"></div>
							</a>
						</li>`);
				}

				$_createConversationModal.modal('hide');
				$_createConversationForm[0].reset();
				$('.sidebar__item').toggleActive();
				page.redirect('/messages/' + conversation.id);
			});
		});
	});

	page({hashbang: false});


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


	function loadSidebar() {
		getUserConversations(userId).then(function(response) {
			let conversations = $.parseJSON(response);
			
			$('#sidebar').find('.shimmer').hide();
			
			$_conversationList.append(`<li><a data-toggle="modal" data-target="#createConversationModal">New Message</a></li>`);
			$.each(conversations, function(index, conversation) {
				let conversationName = getConversationName(conversation);
				$_conversationList.append(`
					<li>
						<a href="${baseUrl}/messages/${conversation.id}" class="sidebar__item ${conversation.name === 'General' ? 'active': ''}" data-id="${conversation.id}">
							${conversationName}
							<div class="subtitle">${conversation.body}</div>
						</a>
					</li>`);
			});

			$('.sidebar__item').toggleActive();
		});
	}

	function init() {
		loadSidebar();

		getCompanyUsers(companyId).done(function(response) {
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
}(jQuery));