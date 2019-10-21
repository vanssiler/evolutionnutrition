function initHub(hubPublicAppKey, languageCode, installId = null, storeId = 0) {
    var baseUrl = window.mundipagg_base_url;
    var redirectUrl =  window.mundipagg_hub_validate;
    var statusUrl = window.mundipagg_hub_status;
    var generateIntegrarion = window.mundipagg_hub_generate_integration;

    // hub config
    var config = {
        publicAppKey : hubPublicAppKey,
        redirectUrl :  encodeURIComponent(redirectUrl),
        language: languageCode
    };

    if (installId !== null) {
        config.installId = installId;
    }

    Hub(config);
    var integrationButtonSelector = 'span#mundipagg-hub button';

    //doing the magic
    try {
        jQuery(integrationButtonSelector).attr("onclick", "").unbind("click");
    }catch(e){}
    try {
        jQuery(integrationButtonSelector).prop("onclick", null).off("click");
    }catch(e){}
    //disable settings form submit.

    // document.getElementById("mundipagg-hub").children[1]
    jQuery(integrationButtonSelector).attr('form','null');
    jQuery(integrationButtonSelector).click(function(){
        //getting formUrl;
        var hubUrl = (function(config){
            var hub = new Hub(config);
            var url = (hub.language == "pt-br") ? hub.urlToIntegratePtBr : hub.urlToIntegrate;
            url = url.replace("{language}", hub.locations[hub.language].language);
            url = url.replace("{publicAppKey}", config.publicAppKey);
            url = url.replace("{redirectUrl}", config.redirectUrl);

            if (config.installId) {
                url = (hub.language == "pt-br") ? hub.urlToViewPtBr : hub.urlToView;
                url = url.replace("{language}", hub.language);
                url = url.replace("{publicAppKey}", config.publicAppKey);
                url = url.replace("{installId}", config.installId);
            }
            return url;
        })(config);

        jQuery('<i> </i>').addClass('fa fa-spinner fa-spin')
            .attr('id','mundipagg-integration-load-icon')
            .prependTo(jQuery(integrationButtonSelector));
        jQuery(integrationButtonSelector).attr('disabled','');

        if (installId === null) {
            jQuery.ajax({
                url:  generateIntegrarion + "?storeId=" + storeId,
                success: function(result){
                    hubUrl += '%26install_token%2F' + result;
                    hubUrl += '%2FstoreId%2F' + storeId;
                    var popUp = window.open(hubUrl, null,'height=600,width=800');
                    if (window.focus) {popUp.focus()}

                    window.onbeforeunload = function(e) {
                        popUp.close();
                    };

                    //check hub status.
                    var checkHubStatus = function(){
                        jQuery.ajax({
                            url: statusUrl + "?storeId=" + storeId,
                            success: function(result) {
                                if (result === 'enabled') {
                                    window.location.reload(true);
                                    return;
                                }
                                setTimeout(checkHubStatus, 500);
                            }
                        });
                    };
                    setTimeout(checkHubStatus, 500);
                }
            });
        }
        else {

            var popUp = window.open(hubUrl, null,'height=600,width=800');
            if (window.focus) {popUp.focus()}

            window.onbeforeunload = function(e) {
                popUp.close();
            };
        }
    });
}