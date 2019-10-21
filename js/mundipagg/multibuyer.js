function getState(obj) {

    var baseUrl = jQuery(".baseUrl").val();

    var url = baseUrl + "/mp-paymentmodule/multibuyer/getRegions/";                                                                                                                        
        url += "?country_id=" + obj.value;

    var elementId = obj.id.split('multi')[0];

    jQuery.get(url)
    .done(function (data){
        var html = buildStatesOptions(data);
        var id = "#" + elementId + "multi_buyer_state";

        jQuery("#" + elementId + "multi_buyer_state").html(html);
        jQuery("#" + elementId + "multi_buyer_state").prop("disabled", false);
    })
    .fail(function (err) {
         jQuery("#" + elementId + "multi_buyer_state").html("");
    });
}

function buildStatesOptions(data) {
    var json = jQuery.parseJSON(data);
    var html = "<option value=''> Select </option>";

    Object.keys(json).forEach(function(k){
        html += "<option ";
        html += "value='" + json[k].code + "'>";
        html += " " + json[k].name + " ";
        html += "</option>";
    });

    return html;
}


function validateTaxVat(element) {
    $(element).value = $(element).value.replace(/[^0-9]/g, '');
} 
