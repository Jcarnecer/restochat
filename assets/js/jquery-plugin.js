(function($) {

	function convertTimestamp(timestamp) {
		let today = moment(new Date());
		let date = moment(new Date(timestamp * 1000));

		if (today.isSame(date, 'd')) {
			return date.format('h:mm A');
		}
		return date.format('MMM DD, YYYY h:mm A');
	}


	$.fn.eventShowTime = function(options) {
		let settings = $.extend({
			timestamp: ""
		}, options);

		let $_messageTime = this.find('.message__time');

		$_messageTime.html(convertTimestamp(settings.timestamp)).hide();
		this.find('.message__bubble').hover(function() {
			this.show();
		}.bind($_messageTime), function() {
			this.hide();
		}.bind($_messageTime));
	}

	$.fn.eventShowStatus = function(options) {
		let settings = $.extend({
			status: "Sending..."
		}, options);

		let $_messageStatus = this.find('.message__status');
		let $_messageBubble = this.find('.message__bubble');

		$_messageStatus.html(settings.status).hide();
		$_messageBubble.click(function() {
			$_messageStatus.toggle();
		});
	}


	$.fn.toggleActive = function() {
		this.click(function() {
			$('.sidebar__item').removeClass('active');
			$(this).addClass('active');
		});
	}

	$.fn.scrollToBottom = function() {
		this.scrollTop(this.prop('scrollHeight'));
	}

}(jQuery));