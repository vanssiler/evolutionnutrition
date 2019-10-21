 var MundiPagg = {
    paymentMethods: {},
    grandTotal: ''
};

MundiPagg.initPaymentMethod = function (methodCode, orderTotal) {
    if (typeof this.paymentMethods[methodCode] === 'undefined') {

        this.selectedInstallments = {};

        this.paymentMethods[methodCode] =
            new MundiPaggCheckoutHandler(methodCode);
        this.paymentMethods[methodCode].setSavePaymentInterceptor();

        this.paymentMethods[methodCode].init();

        initPaymentMethod(methodCode, orderTotal);

        this.paymentMethods[methodCode].setValueInputAutobalanceEvents();
        this.paymentMethods[methodCode].updateInputBalanceValues();
    }
};

MundiPagg.init = function(posInitializationCallback)
{
    MundiPagg.Locale.init(posInitializationCallback);
};

MundiPagg.Locale = {
    translactionTable: false,
    init: function (posInitializationCallback)
    {
        if (!this.translactionTable) {
            var baseUrl = '';
            var url = baseUrl + '/mp-paymentmodule/i18n/getTable';
            apiRequest(url,'',function(data){
                if(data !== false) {
                    this.translactionTable = JSON.parse(data);
                    posInitializationCallback();
                }
            }.bind(this));
        }
    },
    getTranslaction: function (text)
    {
        var translaction = this.translactionTable[text];
        if (typeof translaction === 'undefined') {
            translaction = text;
        }
        return translaction;
    }
};

var toTokenApi = {};

var brandName = false;

function initSavedCreditCardInstallments() {
    jQuery(".savedCreditCardSelect").each(function () {
        var elementId = jQuery(this).attr("elementId");
        fillSavedCreditCardInstallments(elementId)
    });
}

function fillSavedCreditCardInstallments(elementId) {
    var brandName = jQuery("#" + elementId + "_mundicheckout-SavedCreditCard")
        .children("option:selected")
        .attr("data-brand");
    if (brandName == "") {
        brandName = MundiPagg.brand;
    }

    var baseUrl = jQuery(".baseUrl").val();
    var value = jQuery("#" + elementId + "_value").val();

    if (value == "" || value == "0.00") {
        value = (jQuery("span.paymentmodule_subtotal").data('value') / 2).toString();
    }

    var argsObj = {
        elementId: elementId,
        installmentsBaseValue: value
    };

    var fillCardValue = MundiPagg.Locale.getTranslaction('Fill the value for this card');
    var fillCardNumber = MundiPagg.Locale.getTranslaction('Fill the card number');

    var html = '';
    if(brandName == "") {
        html = "<option value=''>"+fillCardNumber+"</option>";
    }
    if(value == "") {
        html = "<option value=''>"+fillCardValue+"</option>";
    }
    if (html !== '') {
        jQuery("#"+argsObj.elementId+"_mundicheckout-creditCard-installments").html(html);
        return;
    }
    getInstallments(baseUrl, brandName, argsObj);
}

/**
 * Call API
 * @param url string
 * @param data
 * @returns {XMLHttpRequest}
 */
function apiRequest(url, data, callback, method, json, callbackArgsObj) {

    if (typeof method == 'undefined') {
        method = 'GET';
    }
    var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    xhr.open(method, url);

    if (json) {
        xhr.setRequestHeader("content-type", "application/json");
    }

    if (callback) {
        apiCallback(xhr, callback, callbackArgsObj);
    }

    xhr.send(JSON.stringify(data));

    return xhr;
}

function apiCallback(xhr, callback, callbackArgsObj) {
    xhr.onreadystatechange = function () {
        if (xhr.readyState > 3 && xhr.status == 200) {
            callback(JSON.parse(xhr.responseText), callbackArgsObj);
        }else{
            callback(false, callbackArgsObj);
        }
    };
}

function getCreditCardToken(pkKey, elementId, callback) {
    if (pkKey == 'undefined' || pkKey == "") {
        alert("Payment module Mundipagg is not integrated!");
        return false;
    }

    if(validateCreditCardData(elementId)){
        apiRequest(
            'https://api.mundipagg.com/core/v1/tokens?appId=' + pkKey,
            toTokenApi[elementId],
            callback,
            "POST",
            true
        );
    }
    return false;
}

/**
 * Validate input data
 * @returns {boolean}
 */
function validateCreditCardData(elementId) {
    if(
        validateCCNumberLength(toTokenApi[elementId].card.number) &&
        validateHolderNameLength(toTokenApi[elementId].card.holder_name) &&
        validateExpMonth(toTokenApi[elementId].card.exp_month) &&
        validateExpYear(toTokenApi[elementId].card.exp_year) &&
        validateCVVLength(toTokenApi[elementId].card.cvv)
    ){
        return true;
    }else{
        return false;
    }
}

function validateCCNumberLength(value)
{
    return (
        value.length > 14 &&
        value.length < 22
    );
}

function validateHolderNameLength(value)
{
    return (
        value.length > 2 &&
        value.length < 51
    );
}

function validateExpMonth(value)
{
    return (
        value > 0 &&
        value < 13
    );
}

function validateExpYear(value)
{
    return (
        value >= getCurrentYear()
    );
}

function validateCVVLength(value)
{
    return (
        value.length > 2 &&
        value.length < 5
    );
}

function getCurrentYear() {
    var date = new Date();
    return date.getFullYear();
}

//validations
function initPaymentMethod(methodCode, orderTotal)
{
    MundiPagg.init(function(){
        MundiPagg.grandTotal = orderTotal;
        initSavedCreditCardInstallments();
        Validation.add(
            methodCode + '_boleto_validate-mundipagg-cpf',
            MundiPagg.Locale.getTranslaction('Invalid CPF'),
            function(cpf) {
                return validateCPF(cpf);
            }
        );

        Validation.add(
            methodCode + '_creditcard_validate-mundipagg-creditcard-exp',
            MundiPagg.Locale.getTranslaction('Invalid Date'),
            function(v,element) {
                var triggerId = element.id;
                var elementIndex = triggerId
                    .replace(methodCode + '_creditcard_','')
                    .replace('_mundicheckout-expiration-date','');
                var elementId = methodCode + "_creditcard_" + elementIndex;

                if (!isNewCard(elementId)) {
                    return true;
                }

                var month = document.getElementById(elementId + '_mundicheckout-expmonth');
                var year = document.getElementById(elementId + '_mundicheckout-expyear');
                return validateCreditCardExpiration(year.value, month.value);
            }
        );

        //value balance
        var amountInputs = jQuery('#payment_form_' + methodCode).find('.multipayment-value-input');

        verifyFilledCreditCardFields();

        //distribute amount through amount inputs;
        if (amountInputs.length > 1) {
            var distributedAmount = parseFloat(MundiPagg.grandTotal);
            distributedAmount /= amountInputs.length;
            jQuery(amountInputs).each(function(index,element) {
                var formatted = parseFloat(distributedAmount).toFixed(2);
                jQuery(element).val(formatted);
            });
        }
    });

    verifyFilledCreditCardFields();

    //trigger change events on certain inputs
    var paymentMethodForm = jQuery('#payment_form_' + methodCode);
    //on saved creditCards select.
    paymentMethodForm.find('.savedCreditCardSelect').change();
}

function isNewCard(elementId)
{
    var isNew = false;

    try {
        isNew = jQuery('#' + elementId + '_mundicheckout-SavedCreditCard');

        isNew =
            isNew.children("option:selected").val() === 'new' ||
            typeof isNew.children("option:selected").val() === 'undefined';
    }
    catch(e) {
        isNew = true;
    }

    return isNew;
}

function validateCPF(cpf)
{
    var numeros, digitos, soma, i, resultado, digitos_iguais;
    digitos_iguais = 1;
    if (cpf.length < 11)
        return false;
    for (i = 0; i < cpf.length - 1; i++)
        if (cpf.charAt(i) != cpf.charAt(i + 1))
        {
            digitos_iguais = 0;
            break;
        }
    if (!digitos_iguais)
    {
        numeros = cpf.substring(0,9);
        digitos = cpf.substring(9);
        soma = 0;
        for (i = 10; i > 1; i--)
            soma += numeros.charAt(10 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;
        numeros = cpf.substring(0,10);
        soma = 0;
        for (i = 11; i > 1; i--)
            soma += numeros.charAt(11 - i) * i;
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
            return false;
        return true;
    }
    else
        return false;
}

function validateCreditCardExpiration(year, month) {
    var date = new Date();
    var expDate = new Date(year, month - 1, 1);
    var today = new Date(date.getFullYear(), date.getMonth(), 1);
    if (expDate < today) {
        return false;
    }
    return true;
}

function updateInstallmentCache(elementId, selectedInstallment)
{
    if (
        typeof MundiPagg.installmentCache === 'undefined' ||
        selectedInstallment === ""
    ) {
        return;
    }

    jQuery.each(MundiPagg.installmentCache, function(index, value) {
        if (value.indexOf(elementId) !== -1) {
            MundiPagg.installmentCache[value]['selectedInstallment'] = selectedInstallment;
        }

    }. bind(elementId, selectedInstallment));
}

//form data
function getFormData(elementId) {

    if (typeof toTokenApi[elementId] === 'undefined') {
        toTokenApi[elementId] = { card:{} };
    }

    var selector =  "#"+elementId+"_mundicheckout-creditCard-installments";
    var selectedInstallment = jQuery(selector).val();

    if (typeof MundiPagg.installmentCache !== 'undefined' && selectedInstallment === '') {
        jQuery.each(MundiPagg.installmentCache, function(index, value) {
            if (value.indexOf(elementId) !== -1) {
                selectedInstallment = MundiPagg.installmentCache[value]['selectedInstallment'];
                jQuery(selector).val(selectedInstallment);
            }

        }. bind(elementId, selectedInstallment));
    }

    MundiPagg.selectedInstallments[elementId] = selectedInstallment;
    updateInstallmentCache(elementId, selectedInstallment);

    var customerDoc = getCustomerDocument(elementId);
    if (customerDoc) {
        customerDoc = customerDoc.replace(/[\/.-]/g, "")
    }

    toTokenApi[elementId].card = {
        type: getCardType(elementId),
        holder_name: clearHolderName(document.getElementById(elementId + '_mundicheckout-holdername')),
        number: clearCardNumber(document.getElementById(elementId + '_mundicheckout-number')),
        exp_month: document.getElementById(elementId + '_mundicheckout-expmonth').value,
        exp_year: document.getElementById(elementId + '_mundicheckout-expyear').value,
        cvv: clearCvv(document.getElementById(elementId + '_mundicheckout-cvv')),
        holder_document: customerDoc
    };

    if (!isNewCard(elementId)) {
        var brandName = jQuery('#' + elementId + '_mundicheckout-SavedCreditCard')
            .find('option:selected').attr('data-brand');
        jQuery("#" + elementId + "_brand_name").val(brandName);
    }
}

var isElementValueBusy = {};
function getBrandWithDelay(elementId) {

    if (typeof isElementValueBusy[elementId] === 'undefined') {
        isElementValueBusy[elementId] = false;
    }

    if (!isElementValueBusy[elementId]) {
        var lastValue = jQuery("#" + elementId + "_value").val();

        setTimeout(function(){
            var currentValue = jQuery("#" + elementId + "_value").val();
            isElementValueBusy[elementId] = false;
            if (currentValue === lastValue) {
                getBrand(elementId);
                return;
            }
            getBrandWithDelay(elementId);
        }.bind(lastValue,elementId,isElementValueBusy),300);

        isElementValueBusy[elementId] = true;
    }
}

function getBrand(elementId) {

    var brandName = jQuery("#" + elementId +"_mundipaggBrandName").val();
    var baseUrl = jQuery(".baseUrl").val();
    var creditCardNumber = jQuery("#" + elementId +"_mundicheckout-number").val();
    jQuery("#" + elementId +"_mundicheckout-number").attr('value', creditCardNumber);
    var value = jQuery("#" + elementId +"_value").val();

    var argsObj = {
        elementId : elementId,
        installmentsBaseValue: value
    };

    if (!isNewCard(elementId)) {
        brandName = jQuery("#" + elementId + "_mundicheckout-SavedCreditCard")
            .children("option:selected").attr("data-brand");
        getInstallments(baseUrl, brandName, argsObj);

        showBrandImage(brandName, elementId);
    }

    if (typeof creditCardNumber !== 'undefined') {
        if (
            creditCardNumber.length > 5 &&
            (brandName === "" || typeof value !== 'undefined')
        ) {
            var bin = creditCardNumber.substring(0, 6);

            apiRequest(
                "https://api.mundipagg.com/bin/v1/" + bin,
                "",
                fillBrandData,
                "GET",
                false,
                argsObj
            );
        }

        if (creditCardNumber.length < 6) {
            clearBrand(elementId);
        }
    }
}

function fillBrandData(data, argsObj) {

    brandList = getBrandList(argsObj);

    if (
        data.brand != "" &&
        data.brand != undefined &&
        brandList.indexOf(data.brand) >= 0
    ) {
        clearBrand(argsObj.elementId);
        showBrandImage(data.brand, argsObj.elementId);

        installmentsSelect =
            "#" + argsObj.elementId +
            "_mundicheckout-creditCard-installments";

        if(jQuery(installmentsSelect).html() != undefined) {
            var selected = jQuery(installmentsSelect).val();
            jQuery(installmentsSelect).html('');
            getInstallments(jQuery(".baseUrl").val(), data.brandName, argsObj);
            jQuery(installmentsSelect).val(selected);
        }

        jQuery("#" + argsObj.elementId + "_brand_name").val(data.brandName);
        jQuery("#" + argsObj.elementId + '_disabled_brand_message').hide();
        return;
    }

    jQuery("#" + argsObj.elementId + '_disabled_brand_message').show();
}

function getBrandList(argsObj) {

    var brandList = [];
    jQuery("#" + argsObj.elementId + "_tokenDiv")
        .children('.input-box')
        .children('.mundipagg-brand-image')
        .each(function () {
            brandList.push(jQuery(this).attr('brand-name'));
        });

    return brandList;
}

function clearBrand(elementId){
    MundiPagg.brand = null;

    try {
        if (jQuery("#" + elementId + "_mundicheckout-SavedCreditCard").val() !== "new") {
            return;
        }
    }catch(e) {

    }

    if(jQuery("#" + elementId + "_mundicheckout-number").val().length < 6) {
        jQuery("#" + elementId + "_brandDiv").children('img').removeClass("half-opacity");
        jQuery("#"+elementId+"_mundicheckout-creditCard-installments").html("");
    }
    jQuery("#" + elementId + '_disabled_brand_message').hide();
}

function showBrandImage(brandName, elementId) {
    MundiPagg.brand = brandName;
    brandName = brandName.toLowerCase();

    jQuery("#" + elementId + "_brandDiv").children('img').addClass("half-opacity");
    jQuery("#" + elementId + "_tokenDiv")
        .find('.' + brandName)
        .removeClass('half-opacity')
    ;
}

/**
 * @param baseUrl
 * @param brandName
 * @param argsObj
 * var argsObj = {
 *      elementId: elementId,
 *      installmentsBaseValue: value
 *  };
 */
function getInstallments(baseUrl, brandName, argsObj) {
    var value = '';
    if(typeof argsObj.installmentsBaseValue !== 'undefined'){
        var tmp = parseFloat(argsObj.installmentsBaseValue.replace(',','.'));
        if (isNaN(tmp)) {
            tmp = 0;
        }
        value = '?value=' + tmp;
    }

    if (MundiPagg.selectedInstallments == undefined) {
        MundiPagg.selectedInstallments = {};
    }
    
    var selectedInstallment = jQuery("#" + argsObj.elementId + "_mundicheckout-creditCard-installments").val();
    MundiPagg.selectedInstallments[argsObj.elementId] = selectedInstallment;

    if (tmp <= 0) {
        return;
    }

    if (typeof MundiPagg.installmentCache === 'undefined') {
        MundiPagg.installmentCache = {};
    }

    var installmentCacheKey = getInstallmentCacheKey(brandName, value);
    argsObj.installmentCacheKey = installmentCacheKey;

    if (typeof MundiPagg.installmentCache[installmentCacheKey] !== 'undefined') {
        var data = MundiPagg.installmentCache[installmentCacheKey];
        switchInstallments(data, argsObj);
        return;
    }

    apiRequest(
        baseUrl + '/mp-paymentmodule/creditcard/getinstallments/' + brandName + value,
        '',
        switchInstallments,
        "GET",
        false,
        argsObj
    );
}

function getInstallmentCacheKey(brandName, value)
{
    return brandName + '_' + value;
}

function switchInstallments(data, argsObj) {
    if (typeof MundiPagg.installmentCache === 'undefined') {
        MundiPagg.installmentCache = {};
    }

    MundiPagg.installmentCache[argsObj.installmentCacheKey] = data;

    jQuery('.disabledBrandMessage').hide();

    if (data) {

        if (typeof MundiPagg.installmentCache === 'undefined') {
            MundiPagg.installmentCache = {};
        }

        var hash =
            argsObj.elementId + "_" +
            argsObj.installmentsBaseValue +
            (jQuery('#' + argsObj.elementId + '_mundicheckout-number').val());

        MundiPagg.installmentCache[hash] = { data };

        var installment = MundiPagg.selectedInstallments[argsObj.elementId];
        if (installment !== null && installment !== "") {
            MundiPagg.installmentCache[hash].selectedInstallment = installment;
        }

        var html;

        html = fillInstallments(data);

        jQuery("#"+ argsObj.elementId + "_mundicheckout-creditCard-installments").html(html);

        if (
            MundiPagg.selectedInstallments[argsObj.elementId] != "" &&
            MundiPagg.selectedInstallments[argsObj.elementId] != null
        ) {
            jQuery("#"+ argsObj.elementId + "_mundicheckout-creditCard-installments")
                .val(MundiPagg.selectedInstallments[argsObj.elementId]);
        }
    } else {
        if(argsObj !== undefined && argsObj.elementId != undefined) {
            jQuery("#" + argsObj.elementId + '_disabled_brand_message').show();
        }
    }
}

function fillInstallments(data) {
    var html = '';
    var withoutInterest = MundiPagg.Locale.getTranslaction("without interest");
    var interestPercent = MundiPagg.Locale.getTranslaction("% of interest");
    var of = MundiPagg.Locale.getTranslaction("of");

    for (i=0; i< data.length; i++) {
        data[i].interestMessage = ' ' + withoutInterest + " , Total: " + data[i].totalAmount ;

        if (data[i].interest > 0) {
            data[i].interestMessage =
                " " + MundiPagg.Locale.getTranslaction("with") + " " +
                parseFloat(data[i].interest) +
                interestPercent + " , Total: " + data[i].totalAmount ;
        }

         html +=
            "<option value='"+data[i].times+"'>" +
            data[i].times +
            "x " + of + " " +
            data[i].amount +
            data[i].interestMessage +
            "</option>";
    }

    return html;
}

function switchNewSaved(value, elementId) {
    if(value == "new") {
        jQuery(".newCreditCard-" + elementId).show();
        jQuery(".savedCreditCard-" + elementId).hide();
    } else {
        jQuery(".newCreditCard-" + elementId).hide();
        jQuery(".savedCreditCard-" + elementId).show();
    }
}

function toggleMultiBuyerForm(elementId)
{
    var isEnabled =
        jQuery('#' + elementId + '_multi_buyer_enabled:checked').length > 0;
    if (isEnabled) {
        enableMultibuyerForm(elementId);
        return;
    }
    disableMultibuyerForm(elementId);
}

function enableMultibuyerForm(elementId)
{
    jQuery('#' + elementId + '_multi_buyer_enabled').attr('checked', true);
    jQuery("#" + elementId + '_multi_buyer_form_div').show();

    //enabling all children input
    jQuery('#' + elementId + '_multi_buyer_form_div').find('[name]')
        .attr('disabled',false);


    //if multibuyer is enabled, save credit card should be disabled.
    jQuery('#' + elementId + '_mundicheckout-save-credit-card')
        .attr('disabled',true);
}

function disableMultibuyerForm(elementId)
{
    jQuery('#' + elementId + '_multi_buyer_enabled').attr('checked', false);
    jQuery("#" + elementId + '_multi_buyer_form_div').hide();

    //disabling all children input
    jQuery('#' + elementId + '_multi_buyer_form_div').find('[name]')
        .attr('disabled',true);

    //enable editing of save credit-card checkbox
    jQuery('#' + elementId + '_mundicheckout-save-credit-card')
        .attr('disabled',false);
}

function toogleSavedCreditCard(elementId) {
    var isEnabled =
        jQuery('#' + elementId + '_mundicheckout-save-credit-card:checked')
            .length > 0;

    //if isEnabled, it should disable multibuyer checkbox
    if(isEnabled) {
        disableMultibuyerForm(elementId);
        jQuery('#' + elementId + '_multi_buyer_enabled').attr('disabled', true);
        return;
    }

    jQuery('#' + elementId + '_multi_buyer_enabled').attr('disabled', false);
}

function getCustomerDocument(elementId) {
    if (
        jQuery("input[name='billing[taxvat]']").val() != undefined &&
        jQuery("input[name='billing[taxvat]']").val().length > 10
    ) {
        return jQuery("input[name='billing[taxvat]']").val();
    }

    if (
        jQuery("input[name='billing[vat_id]']").val() != undefined &&
        jQuery("input[name='billing[vat_id]']").val().length > 10
    ) {
        return jQuery("input[name='billing[vat_id]']").val();
    }

    if (
        jQuery('#' + elementId + '_mundicheckout-cpf').val() != undefined &&
        jQuery('#' + elementId + '_mundicheckout-cpf').val().length > 10
    ) {
        return jQuery('#' + elementId + '_mundicheckout-cpf').val();
    }
    return "";
}

function verifyFilledCreditCardFields() {
    jQuery('.validate-cc-number').each(function () {
        getBrand(jQuery(this).attr('id'));
    });
}

function getCardType(elementId) {
    if (elementId.indexOf("voucher") >= 0) {
        return "voucher";
    }
    return "credit";
}