var AbstractCheckoutModuleHandler = function (methodCode) {
    if (this.constructor === AbstractCheckoutModuleHandler) {
        throw new Error(
            "Abstract class '" + this.constructor.name + "' can't be instantiated!"
        );
    }

    this.methodCode = methodCode;
    this.resetBeforeCheckout();
};

AbstractCheckoutModuleHandler.prototype.resetBeforeCheckout = function (placeOrderFunction,callerObject) {
    this.tokenCheckTable = {};
    this.tokenCheckTable = {};
    this.placeOrderFunction = placeOrderFunction;
    this.callerObject = callerObject;
};

AbstractCheckoutModuleHandler.prototype.callPlaceOrderFunction = function() {
  if(typeof this.callerObject === 'undefined') {
      return this.placeOrderFunction();
  }
  this.callerObject[this.placeOrderFunction.name]();
};

AbstractCheckoutModuleHandler.prototype.isHandlingNeeded = function () {
    return !(
        this.getCurrentPaymentMethod() !== this.methodCode
    );
};

//@Todo this method do not belongs to this class...
AbstractCheckoutModuleHandler.prototype.hasCardInfo = function () {

    var type = (this.methodCode.indexOf("voucher") >= 0) ? 'voucher' : 'creditcard';
    var creditCardTokenDiv = '.'  + this.methodCode + "_" + type + "_tokenDiv";
    var hasCardInfo = false;
    var _self = this;

    jQuery(creditCardTokenDiv).each(function(index, element) {
        _self.tokenCheckTable[element.id] = false;
        hasCardInfo = true;
    }.bind(_self));

    return hasCardInfo;
};

//@Todo this method do not belongs to this class...
AbstractCheckoutModuleHandler.prototype.handleTokenGenerationResponse = function(response,element) {
    var _self = this;
    var elementId = element.id.replace('_tokenDiv', '');
    var tokenElement = document.getElementById( elementId + '_mundicheckout-token');
    if (response !== false) {
        tokenElement.value = response.id;

        jQuery("#"+elementId+"_mundipagg-invalid-credit-card").hide();
        jQuery("#"+elementId+"_brand_name").val(response.card.brand);
        this.tokenCheckTable[element.id] = true;

        //check if all tokens are generated.
        var canSave = true;
        var type = (this.methodCode.indexOf("voucher") >= 0) ? 'voucher' : 'creditcard';
        jQuery('.' + this.methodCode + '_' + type + '_tokenDiv').each(function(index,_element) {
            if (_self.tokenCheckTable[_element.id] === false) {
                canSave = false;
            }
        }.bind(_self));
        if (canSave) {
            this.callPlaceOrderFunction();
        }
        return;
    }
    tokenElement.value = "";
    jQuery("#"+elementId+"_mundipagg-invalid-credit-card").show();
};

//@Todo this method do not belongs to this class...
AbstractCheckoutModuleHandler.prototype.updateInputBalanceValues = function() {
    //foreach value input of the paymentMethod
    //update input balance values
    jQuery('#payment_form_' + this.methodCode)
        .find('.multipayment-value-input')
        .each(
            function(index,element)
            {
                jQuery(element).change();
            }
        );
};

//@Todo this method do not belongs to this class...
AbstractCheckoutModuleHandler.prototype.setValueInputAutobalanceEvents = function () {
    //value balance
    var amountInputs = jQuery('#payment_form_' + this.methodCode).find('.multipayment-value-input');

    //setting autobalance;
    if (amountInputs.length === 2) { //needs amount auto balance
        jQuery(amountInputs).each(function(index,element) {
            var oppositeIndex = index === 0 ? 1 : 0;
            var oppositeInput = amountInputs[oppositeIndex];

            element.lastValue = jQuery(element).val();
            jQuery(element).on('input',function(){

                setTimeout(function(){
                    if (jQuery(element).val() !== element.lastValue) {
                        element.lastValue = jQuery(element).val();
                        jQuery(element).change();
                    }
                }.bind(element),2000);

            }.bind(element));

            jQuery(element).on('change',function(){

                if (MundiPagg.grandTotal == "") {
                    return;
                }

                var max = parseFloat(MundiPagg.grandTotal);
                var elementValue = parseFloat(jQuery(element).val());

                if (isNaN(elementValue) || elementValue == 0) {
                    elementValue = max / 2;
                }

                if (elementValue > max) {
                    elementValue = max;
                }

                var oppositeValue = max - elementValue;

                jQuery(oppositeInput).val(oppositeValue.toFixed(2));
                jQuery(element).val(elementValue.toFixed(2));

                var elementId = element.id.split('_');
                elementId.pop();
                getBrandWithDelay(elementId.join('_'));

                var oppositeInputId = oppositeInput.id.split('_');
                oppositeInputId.pop();
                getBrandWithDelay(oppositeInputId.join('_'));

            }.bind(element));
        });
    }
};


AbstractCheckoutModuleHandler.prototype.init = function() {
    throw new Error("'init' is abstract!");
};


AbstractCheckoutModuleHandler.prototype.setSavePaymentInterceptor = function() {
    throw new Error("'setSavePaymentInterceptor' is abstract!");
};

AbstractCheckoutModuleHandler.prototype.getCurrentPaymentMethod = function() {
    throw new Error("'getCurrentPaymentMethod' is abstract!");
};

