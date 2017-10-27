"use strict";

const userId = $('meta[name="user_id"]').attr('content');
const companyId = $('meta[name="company_id"]').attr('content');

const $_messageArea = $('.message-area');
const $_createMessageForm = $('#createMessageForm');
const $_conversationList = $('.conversation-list');
const $_messageBody = $_createMessageForm.find('[name="body"]');
const $_createConversationModal = $('#createConversationModal');
const $_createConversationForm = $('#createConversationForm');

const socket = io('https://socket-simpleapp.herokuapp.com/');
const audio = new Audio('https://notificationsounds.com/soundfiles/68ce199ec2c5517597ce0a4d89620f55/file-sounds-954-all-eyes-on-me.mp3');

page('/', index);
page("/messages/create", showCreateConversation);
page("/messages/:conversationId", message);
page.exit("/messages/:conversationId", exitConversation);
page("*", notFound);
page({ hashbang: false });
page.start();


function index(context) {
	$("#sidebar").ready(function() {
		getUserConversations(userId).then(function(response) {
			let conversations = $.parseJSON(response);

			$('#sidebar').find('.shimmer').hide();
			$('.sidebar__header').append(`
				<a class="sidebar__header__item" href="http://localhost/main">
					<i class="fa fa-arrow-left"></i>
				</a>
				<a class="sidebar__header__item">kaChat</a>
				<a class="sidebar__header__item" href="#" data-toggle="modal" data-target="#createConversationModal">
					<i class="fa fa-plus-circle"></i>
				</a>
			`);

			$.each(conversations, function(index, conversation) {
				let conversationName = getConversationName(conversation);
				$_conversationList.append(`
					<li title="${conversationName}" data-conversation="${conversation.id}">
						<a href="${baseUrl}/messages/${conversation.id}" class="sidebar__item ${conversation.name === 'General' ? 'active': ''}">
							<div class="">${conversationName}</div>
							<small>${conversation.body}</small>
						</a>
					</li>
				`);
			});

			$('.sidebar__item').toggleActive();
		});
	});
	
	$("#createConversationModal").on("shown.bs.modal", function() {
		getCompanyUsers(companyId).then(function(response) {
			let users = $.parseJSON(response);

			$.each(users, function(index, user) {
				if (user.id !== userId) {
					$('#createConversationModal .menu').append(
						$(`
							<div class="menu__item">
								<img class="menu__image" src="http://localhost/main/assets/img/avatar/${user.id}.png" />
								${user.first_name} ${user.last_name}
							</div>
						`)
						.click(function() {
							let conversationDetails = {
								"participants": [userId, user.id],
								"company_id": companyId,
								"type": 2
							};

							getPrivateConversation(conversationDetails).done(function(response) {
								let conversation = $.parseJSON(response);

								$("#createConversationModal").modal("hide");

								if (conversation) {
									$(`[data-message="${conversation.id}"]`).addClass("active");
									page.redirect("/messages/" + conversation.id );
								} else {
									page.redirect("/messages/create?" + querystring.stringify(conversationDetails));
								}
							});
						})
					);
				}
			});
		});
	});

	$('#createConversationModal').on("hide.bs.modal", function() {
		$("#createConversationModal .menu").html("");
	});

	page.redirect("/messages/" + $('meta[name="general_conversation"]').attr('content'));
}

function showCreateConversation(context) {
	let conversation = querystring.parse(context.querystring);

	document.title = "";
	$(".navbar-brand").html("");
	$(".message-area").html("");

	$.each(conversation.participants, function(index, participant) {
		getUser(participant).done(function(data) {
			let user = $.parseJSON(data);
			document.title += user.first_name + " " + user.last_name;
			$(".navbar-brand").append(user.first_name + " " + user.last_name);
		});
	});

	$("#createMessageForm").unbind("submit").submit(function(event) {
		event.preventDefault();

		if ($("#createMessageForm").find("[name='body']").val()) {
			createConversation(conversation).done(function(data) {
				let conversation = $.parseJSON(data);
				let conversationName = getConversationName(conversation);

				createConversationMessage(conversation.id, $("#createMessageForm").serialize()).done(function(data) {
					let message = $.parseJSON(data);

					$(".conversation-list").find(".active").removeClass("active");
					$(".conversation-list").prepend(
						$(`
							<li title="${conversationName}">
								<a href="${baseUrl}/messages/${conversation.id}" class="sidebar__item active" data-conversation="${conversation.id}">
									<div class="">${conversationName}</div>
									<small>${message.body}</small>
								</a>
							</li>
						`)
					);
					$(".conversation-list .sidebar__item").toggleActive();
					//socket.emit("push conversation", conversation);
					page.redirect("/messages/" + conversation.id);
				});
			});
			$("#createMessageForm").find("[name='body']").val("");
		}
	});
}


function message(context) {
	let conversationId = context.params.conversationId;

	getConversation(conversationId).done(function(response) {
		let conversation = $.parseJSON(response);
		let conversationName = getConversationName(conversation);

		document.title = conversationName;
		$('.navbar-brand').html(conversationName);
	});

	loadMessageArea(conversationId);

	$("#createMessageForm").unbind("submit").submit(function(event) {
		event.preventDefault();

		if ($_messageBody.val().trim()) {
			let $_message = $(`
				<div class="message message--primary">
					<div class="message__body">
						<div class="message__time"></div>
						<div class="message__bubble">${$_messageBody.val().trim()}</div>
					</div>
					<div class="message__status">Sending...</div>
				</div>
			`);

			$_message.find('.message__time').hide();
			$_messageArea.append($_message);
			$_messageArea.scrollToBottom();

			createConversationMessage(conversationId, $_createMessageForm.serialize()).then(function(response) {
				let message = $.parseJSON(response);

				$_message.attr("data-message", message.id);
				$_message.eventShowTime({timestamp: message.created_at});
				$_message.eventShowStatus({status: "Delivered"});

				$(`[data-conversation="${message.conversation_id}"]`).find("small").html(message.body);
				$(".conversation-list").prepend($(`[data-conversation="${message.conversation_id}"]`).remove());

				socket.emit("chat message", message);
			}).fail(function(response) {
				$_message.removeClass('message--primary').addClass('message--danger');
				$_message.find('.message__status').html("Click to resend message");
			}.bind($_message));

			$_messageBody.val("");
		}
	});
}


function exitConversation(context, next) {
	$("title").html("");
	$(".navbar-brand").html("<div class='shimmer shimmer--light w-100 m-2'></div>");
	$(".message-area").html(`
		<div class="shimmer shimmer--light w-50 m-2 mt-3"></div>
        <div class="shimmer shimmer--light w-25 m-2"></div>
        <div class="shimmer shimmer--light w-50 m-2 mt-4"></div>
        <div class="shimmer shimmer--light w-25 m-2"></div>
    `);
	next();
}

function notFound() {
	page.redirect("/");
}

function loadMessageArea(conversationId) {
	getConversationMessages(conversationId).then(function(response) {
		let messages = $.parseJSON(response);

		$(".message-area").html("");

		$.each(messages, function(index, message) {
			let $_message;
			let $_lastMessage = $('.message').last();

			if (message.created_by.id === userId) {
				$_message = $(`
					<div class="message message--primary" data-user="${message.created_by.id}" data-message="${message.id}">
						<div class="message__body">
							<div class="message__time"></div>
							<div class="message__bubble">${message.body}</div>
						</div>
						<div class="message__status"></div>
					</div>
				`);
			} else {
				if ($_lastMessage.attr('data-user') === message.created_by.id) {
					$_message = $(`
					<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
						<div class="message__body">
							<div class="message__bubble">${message.body}</div>
							<div class="message__time"></div>
						</div>
					</div>`);
				} else {
					$_message = $(`
					<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
						<div class="message__user">${message.created_by.first_name} ${message.created_by.last_name}</div>
						<div class="message__body">
							<img class="message__avatar" src="http://payakapps.com/assets/img/avatar/${message.created_by.id}.png" />
							<div class="message__bubble">${message.body}</div>
							<div class="message__time"></div>
						</div>
					</div>`);
				}
			}

			$_message.eventShowTime({timestamp: message.created_at});
			$_message.eventShowStatus({status: getMessageStatus(message)});
			$_messageArea.append($_message);
		});

		$_messageArea.scrollToBottom();

		socket.off("chat message");
		socket.on("chat message", function(message) {
			let $_lastMessage = $('.message').last();
			let $_message;

			if (message.created_by.id !== userId && message.conversation_id === conversationId) {
				if ($_lastMessage.attr('data-user') === message.created_by.id) {
					$_message = $(`
						<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
							<div class="message__body">
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
				} else {
					$_message = $(`
						<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
							<div class="message__user">${message.created_by.first_name} ${message.created_by.last_name}</div>
							<div class="message__body">
								<img class="message__avatar" src="http://localhost/main/assets/img/avatar/${message.created_by.id}.png" />
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
				}

				$_message.eventShowTime({timestamp: message.created_at});
				$_messageArea.append($_message).scrollToBottom();
				audio.play();
			}

			if ($(`[data-conversation="${message.conversation_id}"]`).length) {
				$(`[data-conversation="${message.conversation_id}"]`).find("small").html(message.body);
				//$(`[data-conversation="${message.conversation_id}"]`).find(".sidebar__item").addClass("new");
				$('.conversation-list').prepend($(`[data-conversation="${message.conversation_id}"]`).remove());
				audio.play();
			}
		});
	});
}