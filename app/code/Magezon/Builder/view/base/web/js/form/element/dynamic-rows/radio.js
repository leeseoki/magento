define([
	'jquery',
	'angular'
], function($, angular) {

	return {
		link: function(scope, element, attrs, ctrl, $timeout) {
			$(element).find('input').click(function(event) {
				scope.$emit('radioDefaultDynamicItem', {
					item: scope.model,
					key: scope.options.key
				});
			});
		}
	}
});