define(['jquery', 'jquery/ui'],
    function($){
    'use strict';

    $.widget('tawk.widget', {
        options: {
            spConfig: {},
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            $.noConflict();

            this._initialize();
            // Initial setting of various option values
        },

        _initialize: function() {
            var $widget = this;
            
            jQuery.get($widget.options.spConfig.storeWidget + '?id=' + jQuery( "#websiteids" ).val(), function (response) {
                document.getElementById('tawk_widget_customization').src = 'https://plugins.tawk.to/generic/widgets?currentWidgetId=' + response.widgetid + '&currentPageId='+ response.pageid +'&transparentBackground=1&parentDomain=' + $widget.options.spConfig.mainUrl;

                jQuery('#excludeurl').val(response.excludeurl);
                jQuery('#includeurl').val(response.includeurl);
                if(response.alwaysdisplay == 1){
                    jQuery('#alwaysdisplay').prop('checked', true);
                    jQuery("#exlucded_urls_container").show();
                }else{
                    jQuery('#alwaysdisplay').prop('checked', false);
                    jQuery("#exlucded_urls_container").hide();
                }
                if(response.donotdisplay == 1){
                    jQuery('#donotdisplay').prop('checked', true);
                    jQuery("#included_urls_container").show();
                }else{
                    jQuery('#donotdisplay').prop('checked', false);
                    jQuery("#included_urls_container").hide();
                }

            });


            if(jQuery("#alwaysdisplay").prop("checked")){
                jQuery("#exlucded_urls_container").show();
            }else{
                jQuery("#exlucded_urls_container").hide();
            }

            if(jQuery("#donotdisplay").prop("checked")){
                jQuery("#included_urls_container").show();
            }else{
                jQuery("#included_urls_container").hide();
            }

            window.addEventListener('message', function(e) {
                if(e.origin === '<?php echo $this->getBaseUrl() ?>') {
                    if(e.data.action === 'setWidget') {
                        setWidget(e);
                    }
                    if(e.data.action === 'removeWidget') {
                        removeWidget(e);
                    }
                }
            });

            function setWidget(e) {
                var alwaysdisplay = jQuery('#alwaysdisplay').is(":checked");
                var alwaysdisplayvalue = alwaysdisplay ? 1 : 0;

                var donotdisplay = jQuery('#donotdisplay').is(":checked");
                var donotdisplayvalue = donotdisplay ? 1 : 0;

                jQuery.post($widget.options.spConfig.formAction, {
                    pageId   : e.data.pageId,
                    widgetId : e.data.widgetId,
                    id       : jQuery('#websiteids').val(),
                    excludeurl : jQuery('#excludeurl').val(),
                    includeurl : jQuery('#includeurl').val(),
                    alwaysdisplay : alwaysdisplayvalue,
                    donotdisplay: donotdisplayvalue,
                    form_key : $widget.options.spConfig.formKey
                }, function(response) {
                    e.source.postMessage({action : 'setDone'}, $widget.options.spConfig.baseUrl);
                });
            }

            function removeWidget(e) {
                jQuery.get($widget.options.spConfig.removeUrl + '?id=' + e.data.id, function (response) {
                    e.source.postMessage({action : 'removeDone'}, $widget.options.spConfig.baseUrl);
                });
            }

            jQuery(".savesettingsbtn" ).click(function(e) {
                e.preventDefault();
                var alwaysdisplay = jQuery('#alwaysdisplay').is(":checked");
                var alwaysdisplayvalue = alwaysdisplay ? 1 : 0;

                var donotdisplay = jQuery('#donotdisplay').is(":checked");
                var donotdisplayvalue = donotdisplay ? 1 : 0;

                jQuery.post($widget.options.spConfig.formAction, {
                    pageId     : "-1",
                    widgetId   : "-1",
                    id         : jQuery('#websiteids').val(),
                    excludeurl : jQuery('#excludeurl').val(),
                    includeurl : jQuery('#includeurl').val(),
                    alwaysdisplay : alwaysdisplayvalue,
                    donotdisplay: donotdisplayvalue,
                    form_key : $widget.options.spConfig.formKey
                }, function() {
                    alert('Visibility options Saved');
                });
            });

            jQuery('#websiteids').on('change', function() {
                if(this.value == 0){
                    document.getElementById('tawk_widget_customization').src = "";
                    jQuery("#visibility_options").hide();
                }else{
                    jQuery.get($widget.options.spConfig.storeWidget + '?id=' + this.value, function (response) {
                        document.getElementById('tawk_widget_customization').src = 'https://plugins.tawk.to/generic/widgets?currentWidgetId=' + response.widgetid + '&currentPageId='+ response.pageid +'&transparentBackground=1&parentDomain=' + $widget.options.spConfig.mainUrl;
                        jQuery("#visibility_options").show();

                        jQuery('#excludeurl').val(response.excludeurl);
                        jQuery('#includeurl').val(response.includeurl);
                        if(response.alwaysdisplay == 1){
                            jQuery('#alwaysdisplay').prop('checked', true);
                            jQuery("#exlucded_urls_container").show();
                        }else{
                            jQuery('#alwaysdisplay').prop('checked', false);
                            jQuery("#exlucded_urls_container").hide();
                        }
                        if(response.donotdisplay == 1){
                            jQuery('#donotdisplay').prop('checked', true);
                            jQuery("#included_urls_container").show();
                        }else{
                            jQuery('#donotdisplay').prop('checked', false);
                            jQuery("#included_urls_container").hide();
                        }
                    });
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
        }
    });
    return $.tawk.widget;
});