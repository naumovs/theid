/* global window */
define([
	'jquery',
	'app/lib/utils',
	'./base/controller',
], function ($, utils, BaseController) {
	return BaseController.extend({
//		initialize: function () {
//			BaseController.prototype.initialize.apply(this, arguments);
//		},
		trackEventLocal: function(category, action, value) {
			$.ajax(utils.reverse('trackEvent'), {
				type: 'post',
				data: {
					'form[category]': category,
					'form[action]': action,
					'form[value]': value
				}
			});
		}
	});

});