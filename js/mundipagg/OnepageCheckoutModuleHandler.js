var OnepageCheckoutModuleHandler = function (methodCode) {
    AbstractCheckoutModuleHandler.call(this,methodCode);
};
var MundiPaggCheckoutHandler = OnepageCheckoutModuleHandler;

OnepageCheckoutModuleHandler.prototype =
    Object.create(AbstractCheckoutModuleHandler.prototype, {
        'constructor': OnepageCheckoutModuleHandler
    });

OnepageCheckoutModuleHandler.prototype.getCurrentPaymentMethod = function() {
    return payment.currentMethod;
};

OnepageCheckoutModuleHandler.prototype.init = function() {
};

OnepageCheckoutModuleHandler.prototype.setSavePaymentInterceptor = function () {
    var _self = this;
    Payment.prototype.save = Payment.prototype.save.wrap(function(save) {
        _self.resetBeforeCheckout(save);

        code = _self.methodCode.split('_');
        methodName = code[1];
        if(!_self.isHandlingNeeded() || !_self.hasCardInfo()) {
            return _self.placeOrderFunction();
        }

        _self.updateInputBalanceValues();

        //for each of creditcard forms
        var type = (_self.methodCode.indexOf("voucher") >= 0) ? 'voucher' : 'creditcard';
        jQuery('.' + _self.methodCode + '_' + type + '_tokenDiv').each(function(index, element) {
            var elementId = element.id.replace('_tokenDiv', '');
            if (isNewCard( elementId) ) {
                var key = document.getElementById(element.id)
                    .getAttribute('data-mundicheckout-app-id');
                var validator = new Validation(payment.form);
                if (payment.validate() && validator.validate()) {
                    getCreditCardToken(key, elementId, function(response){
                        _self.handleTokenGenerationResponse(response,element);
                    }.bind(_self));
                }
                return;
            }
            _self.tokenCheckTable[element.id] = true;
            return _self.placeOrderFunction();
        }.bind(_self));
    }.bind(_self));
};


