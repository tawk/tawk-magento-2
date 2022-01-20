define(['jquery', 'jquery/ui'], function($) {
	'use strict';

	$.noConflict();

	return function(config) {
		var domain = config.domain;
		var baseUrl = config.baseUrl;
		var removeWidgetUrl = config.removeWidgetUrl;
		var storedWidgetUrl = config.storedWidgetUrl;
		var formAction = config.form.action;
		var formKey = config.form.key;

		jQuery(function() {
			displayWidget(jQuery('#websiteids').val());

			if (jQuery("#alwaysdisplay").prop("checked")) {
				jQuery("#exlucded_urls_container").show();
			} else {
				jQuery("#exlucded_urls_container").hide();
			}

			if (jQuery("#donotdisplay").prop("checked")) {
				jQuery("#included_urls_container").show();
			} else {
				jQuery("#included_urls_container").hide();
			}

			window.addEventListener('message', function(e) {
				if(e.origin === baseUrl) {
					if(e.data.action === 'setWidget') {
						setWidget(e);
					}
					if(e.data.action === 'removeWidget') {
						removeWidget(e);
					}
					if(e.data.action === 'reloadHeight') {
						reloadIframeHeight(e.data.height);
					}
				}
			});

			function setWidget(e) {
				var alwaysdisplay = jQuery('#alwaysdisplay').is(":checked");
				var alwaysdisplayvalue = alwaysdisplay ? 1 : 0;

				var donotdisplay = jQuery('#donotdisplay').is(":checked");
				var donotdisplayvalue = donotdisplay ? 1 : 0;

				jQuery.post(formAction, {
					pageId   : e.data.pageId,
					widgetId : e.data.widgetId,
					id       : jQuery('#websiteids').val(),
					excludeurl : jQuery('#excludeurl').val(),
					includeurl : jQuery('#includeurl').val(),
					alwaysdisplay : alwaysdisplayvalue,
					donotdisplay: donotdisplayvalue,
					enableVisitorRecognition : jQuery('#enable_visitor_recognition').is(":checked") ? 1 : 0,
					form_key : formKey
				}, function(response) {
					e.source.postMessage({action : 'setDone'}, baseUrl);
				});
			}

			function removeWidget(e) {
				jQuery.get(removeWidgetUrl + '?id=' + jQuery( "#websiteids" ).val(), function (response) {
					e.source.postMessage({action : 'removeDone'}, baseUrl);
				});
			}

			function reloadIframeHeight(height) {
				if (!height) {
					return;
				}

				var iframe = jQuery('#tawkIframe');
				if (height === iframe.height()) {
					return;
				}

				iframe.height(height);
			}

			jQuery(".savesettingsbtn" ).click(function(e) {
				e.preventDefault();
				var alwaysdisplay = jQuery('#alwaysdisplay').is(":checked");
				var alwaysdisplayvalue = alwaysdisplay ? 1 : 0;

				var donotdisplay = jQuery('#donotdisplay').is(":checked");
				var donotdisplayvalue = donotdisplay ? 1 : 0;

				jQuery.post(formAction, {
					pageId     : "-1",
					widgetId   : "-1",
					id         : jQuery('#websiteids').val(),
					excludeurl : jQuery('#excludeurl').val(),
					includeurl : jQuery('#includeurl').val(),
					alwaysdisplay : alwaysdisplayvalue,
					donotdisplay: donotdisplayvalue,
					enableVisitorRecognition : jQuery('#enable_visitor_recognition').is(":checked") ? 1 : 0,
					form_key : formKey
				}, function() {
					alert('Visibility options Saved');
				});
			});

			jQuery('#websiteids').on('change', function() {
				if (this.value == 0) {
					document.getElementById('tawk_widget_customization').src = "";
					jQuery("#visibility_options").hide();
				} else {
					displayWidget(this.value)
					jQuery("#visibility_options").show();
				}
			});

			jQuery("#alwaysdisplay").change(function() {
				if(this.checked){
					jQuery("#exlucded_urls_container").show();
					jQuery('#donotdisplay').prop('checked', false);
					jQuery("#included_urls_container").hide();
				}else{
					jQuery("#exlucded_urls_container").hide();
					jQuery('#donotdisplay').prop('checked', true);
					jQuery("#included_urls_container").show();
				}
			});

			jQuery("#donotdisplay").change(function() {
				if(this.checked){
					jQuery("#included_urls_container").show();
					jQuery('#alwaysdisplay').prop('checked', false);
					jQuery("#exlucded_urls_container").hide();
				}else{
					jQuery("#included_urls_container").hide();
					jQuery('#alwaysdisplay').prop('checked', true);
					jQuery("#exlucded_urls_container").show();
				}
			});
		});

		function displayWidget (websiteId) {
			jQuery.get(storedWidgetUrl + '?id=' + websiteId, function (response) {
				var src = baseUrl + '/generic/widgets?';
				if (response.widgetid) {
					src = src + '&currentWidgetId=' + response.widgetid;
				}
				if (response.pageid) {
					src = src + '&currentPageId=' + response.pageid;
				}
				src = src + '&transparentBackground=1&pltf=magento&pltfv=2&parentDomain=' + domain;
				document.getElementById('tawk_widget_customization').src = src;

				jQuery('#excludeurl').val(response.excludeurl);
				jQuery('#includeurl').val(response.includeurl);
				if (response.alwaysdisplay == 1) {
					jQuery('#alwaysdisplay').prop('checked', true);
					jQuery("#exlucded_urls_container").show();
				} else {
					jQuery('#alwaysdisplay').prop('checked', false);
					jQuery("#exlucded_urls_container").hide();
				}

				if (response.donotdisplay == 1) {
					jQuery('#donotdisplay').prop('checked', true);
					jQuery("#included_urls_container").show();
				} else {
					jQuery('#donotdisplay').prop('checked', false);
					jQuery("#included_urls_container").hide();
				}

				jQuery('#enable_visitor_recognition').prop('checked', response.enableVisitorRecognition == 1)
			});
		}
	}
 });
