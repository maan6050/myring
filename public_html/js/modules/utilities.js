var UtilModule = (function($) {
	var _this = {}

	_this.bindEvent = function (config) {
		var _default = {
			elm: false,
			eventType: 'click',
			callback: false,
			usePrevent: true
		},
		opts = $.extend(_default, config);
		(opts.elm).each(function () {
			var _this = $(this);
			_this.unbind(opts.eventType).bind(opts.eventType, function (evt) {
				if (opts.usePrevent) { evt.preventDefault(); }
				if (opts.callback) { opts.callback(_this, evt); }
				if (opts.usePrevent) { return false; }
			});
		});
	};

	_this.callAjax = function(config) {
		var _default = {
			cache: false,
			data: {},
			dataType: 'json',
			type: 'POST',
			url: ''
		},
		config_ajax = $.extend(_default, config);
		config_ajax.url = config_ajax.url + '?csrf_protection_safe=safe_ok';
		return $.ajax(config_ajax);
	};

	_this.existElement = function(elem) {
		return (elem.length > 0);
	};

	_this.getFormData = function(form) {
		var obj = {}, val;
		form.find('input, select').each(function() {
			var _this = $(this);
			if('' !== _this.prop('name')) {
				if('checkbox' === _this.prop('type')) {
					val = _this.is(':checked') ? 1 : 0;
				} else {
					val = _this.val();
				}
				obj[_this.prop('name')] = val;
			}
		});
		return obj;
	};

	_this.getLangText = function(container, key) {
		return container.find('.' + key).data('value');
	};

	_this.initTabs = function() {
		var tabs = $('ul.tabs li');        
		if (tabs.length > 0) {
			tabs.click(function() {
				var tab_id = $(this).attr('data-tab');
				$('ul.tabs li').removeClass('current');
				$('.tab-content').removeClass('current');
				$(this).addClass('current');
				$("#"+tab_id).addClass('current');
			});
		}
	};

	_this.onlyNumbers = function(_elem) {
		_elem.keypress(function (evt) {
			var keyCode = evt.which ? evt.which : evt.keyCode;
			if (118 == keyCode) { return true; }
			if (keyCode != 8 && keyCode != 0 && (keyCode < 48 || keyCode > 57)) { return false; }
		});
	};

	_this.swalConfirm = function(config) {
		var _default = {
			title: '',
			text: '',
			type: 'warning',
			showCancelButton: true,
			cancelButtonText: 'Cancel',
			callback: false,
			confirmButtonColor: '#D29105',
			confirmButtonText: 'Continue',
			closeOnConfirm: true
		},
		opt = $.extend(_default, config);
		swal({
			title: opt.title,
			text: opt.text,
			type: opt.type,
			showCancelButton: opt.showCancelButton,
			cancelButtonText: opt.cancelButtonText,
			confirmButtonColor: opt.confirmButtonColor,
			confirmButtonText: opt.confirmButtonText,
			closeOnConfirm: opt.closeOnConfirm
		},
		function(isConfirm) {
			if(opt.callback) { opt.callback(isConfirm); }
		});
	};

	_this.toInt = function(str) {
		return parseInt(str, 10);
	};

	return _this;
}(jQuery));
