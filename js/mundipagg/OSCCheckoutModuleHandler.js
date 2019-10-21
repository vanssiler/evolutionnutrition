var OSCCheckoutModuleHandler = function (methodCode) {
    AbstractCheckoutModuleHandler.call(this,methodCode);
};
var MundiPaggCheckoutHandler = OSCCheckoutModuleHandler;

OSCCheckoutModuleHandler.prototype =
    Object.create(AbstractCheckoutModuleHandler.prototype, {
        'constructor': OSCCheckoutModuleHandler
    });

OSCCheckoutModuleHandler.prototype.getCurrentPaymentMethod = function() {
    return OSCPayment.currentMethod;
};

OSCCheckoutModuleHandler.prototype.init = function() {

    var shipmentSelected = jQuery('input[name=shipping_method]:checked', '#onestepcheckout-general-form').val();

    if (shipmentSelected == undefined) {
        shipmentSelected = "";
    }

    OSCShipment.switchToMethod(shipmentSelected, true);

    OnestepcheckoutCore.updater.onRequestCompleteFn = function (transport) {
        try {
            var response = JSON.parse(transport.responseText.replace(/\n/g,""));

        } catch(e) {
            //error
            var response = {
                blocks: {}
            };
        }

        var action = this._getActionFromUrl(transport.request.url);
        this.removeActionBlocksFromQueue(action, response);
        this.currentRequest = null;
        if (this.requestQueue.length > 0) {
            this._clearQueue();
            var args = this.requestQueue.shift();
            this.runRequest(args[0], args[1]);
        }

        // payment form reload fix
        OSCPayment.initObservers();

        // for Discount purpose...
        // OSCShipment.switchToMethod();

        if (Object.keys(response).includes('grand_total')) {
            var grandTotal = response.grand_total.replace(/\D/g, '');
            grandTotal = parseFloat(grandTotal/100).toFixed(2);
            jQuery('.mundipaggMultiPaymentSubtotal span').html(response.grand_total);

            jQuery('.mundipagg-grand-total').each(function () {
                if(grandTotal > 0) {
                    jQuery(this).val(grandTotal);
                }
            });

            jQuery('.savedCreditCardSelect').each(function () {
                jQuery(this).change();
            });

            MundiPagg.grandTotal = grandTotal;
            Object.keys(MundiPagg.paymentMethods).each(function(method){
                MundiPagg.paymentMethods[method].setValueInputAutobalanceEvents();
                MundiPagg.paymentMethods[method].updateInputBalanceValues();
            });
        }
    };
};

OSCCheckoutModuleHandler.prototype.setSavePaymentInterceptor = function () {
    var _self = this;
    if (Object.keys(MundiPagg.paymentMethods).length === 1) {
        MundiPagg.paymentSent = false;
        var originalResponders = Element.retrieve($(OSCForm.placeOrderButton), 'prototype_event_registry').get('click');
        OSCForm.placeOrderButton.stopObserving('click');
        originalResponders.each(function(e){
            OSCForm.placeOrderButton.observe('seeked',e.handler);
        });
    }
    OSCForm.placeOrderButton.observe('click', function(){
        var isMundipaggPaymentMethod = _self.getCurrentPaymentMethod().indexOf('paymentmodule_') > -1;

        if (!isMundipaggPaymentMethod) {
            if (!MundiPagg.paymentSent) {
                MundiPagg.paymentSent = true;
                OSCForm.placeOrderButton.dispatchEvent(new Event('seeked'));
            }
            return;
        }

        if (OSCForm.validate()) {

            _self.resetBeforeCheckout(OSCForm.placeOrder,OSCForm);
            if(!_self.isHandlingNeeded()) {
                return;
            }

            var code = _self.methodCode.split('_');
            methodName = code[1];

            if (!_self.hasCardInfo()) {
                return OSCForm.placeOrder();
            }

            _self.updateInputBalanceValues();

            //for each of creditcard forms
            var type = (this.methodCode.indexOf("voucher") >= 0) ? 'voucher' : 'creditcard';
            jQuery('.' + this.methodCode + '_' + type + '_tokenDiv').each(function(index, element) {

                var elementId = element.id.replace('_tokenDiv', '');
                if (isNewCard(elementId)) {
                    var key = document.getElementById(element.id)
                        .getAttribute('data-mundicheckout-app-id');
                    getCreditCardToken(key, elementId, function(response){
                        _self.handleTokenGenerationResponse(response, element);
                    }.bind(_self));
                    return;
                }
                _self.tokenCheckTable[element.id] = true;
                return;
            }.bind(_self));
            var canSend = true;
            Object.keys(_self.tokenCheckTable).each(function(key){
                if (_self.tokenCheckTable[key] === false) {
                    canSend = false;
                }
            });
            if (canSend) {
                return OSCForm.placeOrder();
            }
        }
    }.bind(_self));
};

