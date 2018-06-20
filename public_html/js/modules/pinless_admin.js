'use strict';
var PinlessAdminModule = (function ($, utilMod) {
	var _this = {};
	var text = $('div#language-text');

	function bindEvents() {
		utilMod.initTabs();
		utilMod.onlyNumbers($('.onlynumbers'));
		alertCloseEvent();
		createRegisteredNumberFormEvent();
		deleteRegisteredNumberFormEvent();
		pinlessAccountFormEvent();
		swichLanguageEvent();
		updateSpeedDialsFormEvent();
	}

	function alertCloseEvent() {
		var closeBtn = $('div.alert button.close');
		if(utilMod.existElement(closeBtn)) {
			utilMod.bindEvent({
				elm: closeBtn,
				callback: function(elem) {
					elem.parent().remove();
				}
			});
		}
	};

	function createRegisteredNumberFormEvent() {
		var form = $('form#assign_account_holder_form');
		if(utilMod.existElement(form)) {
			form.submit(function(evt) {
				params = utilMod.getFormData(form);
				window.location.href = (form.attr('action') + '/' + params.code + '/' + params.ani + '/' + params.new_ani);
				evt.preventDefault();
				return false;
			});
		}
	};

	function deleteRegisteredNumberFormEvent() {
		var links = $('a.delete_registered_number');
		if(utilMod.existElement(links)) {
			utilMod.bindEvent({
				elm: links,
				callback: function(elem) {
					utilMod.swalConfirm({
						title: utilMod.getLangText(text, 'are_you_sure'),
						cancelButtonText: utilMod.getLangText(text, 'cancel_btn'),
						callback: function(isConfirm) {
							if(isConfirm) {
								window.location.href = elem.attr('href');
							}
						},
						confirmButtonText: utilMod.getLangText(text, 'continue_btn')
					});
				}
			});
		}
	};

	function pinlessAccountFormEvent() {
		var form = $('form#pinless_accounts_form');
		if(utilMod.existElement(form)) {
			form.submit(function(evt) {
				params = utilMod.getFormData(form);
				window.location.href = (form.attr('action') + '/' + params.code + '/' + params.ani);
				evt.preventDefault();
			});
		}
	};

	function swichLanguageEvent() {
		var btn = $('input#swich_language_btn');
		if(utilMod.existElement(btn)) {
			utilMod.bindEvent({
				elm: btn,
				callback: function() {
					utilMod.swalConfirm({
						title: utilMod.getLangText(text, 'are_you_sure'),
						cancelButtonText: utilMod.getLangText(text, 'cancel_btn'),
						callback: function(isConfirm) {
							if(isConfirm) {
								var form = $('form#swich_language_form');
								if(utilMod.existElement(form)) {
									form.submit();
								}
							}
						},
						confirmButtonText: utilMod.getLangText(text, 'continue_btn')
					});
				}
			});
		}
	};

	function updateSpeedDialsFormEvent() {
		var btn = $('input#update_speed_dials_btn');
		if(utilMod.existElement(btn)) {
			utilMod.bindEvent({
				elm: btn,
				callback: function() {
					utilMod.swalConfirm({
						title: utilMod.getLangText(text, 'are_you_sure'),
						cancelButtonText: utilMod.getLangText(text, 'cancel_btn'),
						callback: function(isConfirm) {
							if(isConfirm) {
								$('form#update_speed_dials_form').submit();
							}
						},
						confirmButtonText: utilMod.getLangText(text, 'continue_btn')
					});
				}
			});
		}
	};

	_this.init = function() {
		$(document).ready(function() {
			bindEvents();
		});
	};

	return _this;
})(jQuery, UtilModule);
PinlessAdminModule.init();