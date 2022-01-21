define([], function() {
	return function(config) {
		'use strict';

		window.Tawk_API = window.Tawk_API || {};
		var visitor = config.visitor;
		var embedUrl = config.embedUrl;

		if (visitor) {
			window.Tawk_API.visitor = {
				name : visitor.name,
				email : visitor.email
			}
		}

		var Tawk_LoadStart = new Date();
		(function(){
			var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
			s1.async=true;
			s1.src=embedUrl;
			s1.charset='UTF-8';
			s1.setAttribute('crossorigin','*');
			s0.parentNode.insertBefore(s1,s0);
		})();
	}
});

