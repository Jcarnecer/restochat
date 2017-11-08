(function() {
	"use strict";

	var userId;
	var companyId;

	var socket;
	var audio;

	var baseUrl = "http://localhost/chat";
	var apiUrl = "/api/dev";


	function getConversationName(conversation) {
	    var conversationName = "";
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


	function getUserConversations() {
		return $.ajax({
			url: baseUrl + apiUrl + "/users/conversations",
			method: "GET"
		}).then(function(data) {
			var conversations = $.parseJSON(data);

			$("#sidebar").find(".shimmer").hide();
			$(".sidebar__header").append(`
				<a class="sidebar__header__item" href="http://localhost/main">
					<i class="fa fa-arrow-left"></i>
				</a>
				<a class="sidebar__header__item">kaChat</a>
				<a class="sidebar__header__item" href="#" data-toggle="modal" data-target="#createConversationModal">
					<i class="fa fa-plus-circle"></i>
				</a>	
			`);

			$.each(conversations, function(index, conversation) {
				var conversationName = getConversationName(conversation);
				$("#conversationList").append(`
					<li title="${conversationName}" data-conversation="${conversation.id}">
						<a href="${baseUrl}/conversations/${conversation.id}" class="sidebar__item ${conversation.name === 'General' ? 'active': ''}">
							<div class="">${conversationName}</div>
							<small>${conversation.latest_message.body}</small>
						</a>
					</li>
				`);
			});
			$(".sidebar__item").toggleActive();
		})
	}

	function createConversation(conversationDetails) {
		return $.ajax({
			url: baseUrl + apiUrl + "/conversations",
			method: "POST",
			data: conversationDetails
		}).then(function(data) {
			var conversation = $.parseJSON(data);
			var conversationName = getConversationName(conversation);

			$(".sidebar__item").removeClass("active");

			if (!$(`[data-conversation="${conversation.id}"]`).length) {
				var $conversation = $(`
					<li title="${conversationName}" data-conversation="${conversation.id}">
						<a href="${baseUrl}/conversations/${conversation.id}" class="sidebar__item">
							<div class="">${conversationName}</div>
							<small>${conversation.latest_message.body}</small>
						</a>
					</li>
				`)

				$conversation.find(".sidebar__item").addClass("active");
				$("#conversationList").prepend($conversation);
			} else {
				$(`[data-conversation="${conversation.id}"]`).find(".sidebar__item").addClass("active");
			}

			$(".sidebar__item").toggleActive();
			$("#createConversationModal").modal("hide");
			page.redirect("/conversations/" + conversation.id);
		})
	}

	function getCompanyUsers() {
		return $.ajax({
			url: baseUrl + apiUrl + "/companies/users",
			method: "GET"
		}).then(function(data) {
			var users = $.parseJSON(data);

			$.each(users, function(index, user) {
				if (user.id !== userId) {
					var $item = $(`
						<div class="menu__item">
							<img class="menu__image" src="http://localhost/main/assets/img/avatar/${user.id}.png" />
							${user.first_name} ${user.last_name}
						</div>
					`);

					$item.unbind("click").click(function() {
						var conversationDetails = {
							participants: [user.id],
							type: 2
						};
						createConversation(conversationDetails);
					});

					$('#createConversationModal .menu').append($item);
				}
			});
		});
	}

	function getConversation(conversationId) {
		return $.ajax({
			url: baseUrl + apiUrl + "/conversations/" + conversationId,
			method: "GET"
		}).then(function(data) {
			var conversation = $.parseJSON(data);
			
			conversation.name = getConversationName(conversation);
			$("title").text(conversation.name);
			$(".navbar-brand").html(conversation.name);

			$("#messageArea").html("");
			$.each(conversation.messages, function(index, message) {
				var $message;
				var $lastMessage = $('.message').last();

				if (message.created_by.id === userId) {
					$message = $(`
						<div class="message message--primary" data-user="${message.created_by.id}" data-message="${message.id}">
							<div class="message__body">
								<div class="message__time"></div>
								<div class="message__bubble">${message.body}</div>
							</div>
						</div>
					`);
				} else {
					if ($lastMessage.attr('data-user') === message.created_by.id) {
						$message = $(`
						<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
							<div class="message__body">
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
					} else {
						$message = $(`
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

				$message.eventShowTime({timestamp: message.created_at});
				$("#messageArea").append($message);
			});

			$("#messageArea").scrollToBottom();
		});
	}

	function createConversationMessage(conversationId, messageDetails, $message) {
		return $.ajax({
			url: baseUrl + apiUrl + "/conversations/" + conversationId + "/messages",
			method: "POST",
			data: messageDetails
		}).then(function(data) {
			let message = $.parseJSON(data);

			$message.attr("data-message", message.id);
			$message.eventShowTime({timestamp: message.created_at});
			$message.find(".message__status").remove();

			$(`[data-conversation="${message.conversation_id}"]`).find("small").html(message.body);
			$("#conversationList").prepend($(`[data-conversation="${message.conversation_id}"]`).remove());

			socket.emit("chat message", message);
		}.bind($message)).fail(function(data) {
			$message.removeClass('message--primary').addClass('message--danger');
			$message.find('.message__status').html("Click to resend message");

			$message.unbind("click").click(function() {
				$message.removeClass("message--danger").addClass("message--primary");
				$message.find(".message__status").html("Sending...");
				$("#messageArea").append($message.remove());
				createConversationMessage(conversationId, messageDetails, $message);
			});
		}.bind($message, conversationId, messageDetails));
	}

	/* page.js routes */
	function index(context) {
		page.redirect("/conversations/" + $('meta[name="general_conversation"]').attr('content'));
	}

	function showConversation(context) {
		let conversationId = context.params.conversationId;

		getConversation(conversationId);

		$("#createMessageForm").unbind("submit").submit(function(event) {
			event.preventDefault();

			if ($("#messageBody").val().trim()) {
				let $message = $(`
					<div class="message message--primary">
						<div class="message__body">
							<div class="message__time"></div>
							<div class="message__bubble">${$("#messageBody").val().trim()}</div>
						</div>
						<div class="message__status">Sending...</div>
					</div>
				`);

				$message.find('.message__time').hide();
				$("#messageArea").append($message);
				$("#messageArea").scrollToBottom();

				createConversationMessage(conversationId, $("#createMessageForm").serialize(), $message);
				$("#messageBody").val("");
			}
		});

		socket.off("chat message");
		socket.on("chat message", function(message) {
			var $message;
			var $lastMessage = $(".message").last();

			if (message.created_by.id !== userId && message.conversation_id === conversationId) {
				if ($lastMessage.attr('data-user') === message.created_by.id) {
					$message = $(`
						<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
							<div class="message__body">
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
				} else {
					$message = $(`
						<div class="message message--default" data-user="${message.created_by.id}" data-message="${message.id}">
							<div class="message__user">${message.created_by.first_name} ${message.created_by.last_name}</div>
							<div class="message__body">
								<img class="message__avatar" src="http://localhost/main/assets/img/avatar/${message.created_by.id}.png" />
								<div class="message__bubble">${message.body}</div>
								<div class="message__time"></div>
							</div>
						</div>`);
				}

				$message.eventShowTime({timestamp: message.created_at});
				$("#messageArea").append($message).scrollToBottom();
				audio.play();
			}

			if ($(`[data-conversation="${message.conversation_id}"]`).length) {
				$(`[data-conversation="${message.conversation_id}"]`).find("small").html(message.body);
				$('#conversationList').prepend($(`[data-conversation="${message.conversation_id}"]`).remove());
				$(".sidebar__item").toggleActive();

				if (message.created_by.id !== userId) {
					$(`[data-conversation="${message.conversation_id}"]`).find(".sidebar__item").addClass("new");
					audio.play();	
				}
			}
		});
	}

	function exitConversation(context, next) {
		$("title").text("");
		$(".navbar-brand").html("<div class='shimmer shimmer--light w-100 m-2'></div>");
		$("#messageArea").html(`
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

	function attachEvents() {
		$("#createConversationModal").on("shown.bs.modal", function() {
			getCompanyUsers();
		}).on("hide.bs.modal", function() {
			$("#createConversationModal .menu").html("");
		});

		$("#conversationList").scroll(function() {
			if ($("#conversationList").scrollTop() > 0) {
				$(".sidebar__header").css("box-shadow", "0px 2px 2px rgba(0, 0, 0, 0.12)");
			} else {
				$(".sidebar__header").css("box-shadow", "none");
			}
		});
	}

	function initPageJs() {
		page.base("/chat");
		page("/", index);
		page("/conversations/:conversationId", showConversation);
		page.exit("/conversations/:conversationId", exitConversation);
		page("*", notFound);
		page({hashbang: false});
		page.start();
	}

	function init() {
		userId = $('meta[name="user_id"]').attr("content");
		companyId = $('meta[name="company_id"]').attr("content");
		socket = io('https://socket-simpleapp.herokuapp.com/');
		audio = new Audio('https://notificationsounds.com/soundfiles/68ce199ec2c5517597ce0a4d89620f55/file-sounds-954-all-eyes-on-me.mp3');

		attachEvents();
		initPageJs();
		getUserConversations();
	}
	
	init();
})();