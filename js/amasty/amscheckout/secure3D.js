Amasty3DSecure = Class.create();
Amasty3DSecure.prototype = {
    is3DSecureEnabled: false,
    noSkip: false,

    get CVV_CODELENGTH() {
        return 3;
    },

    initialize: function (is3DSecureEnabled) {
        this.is3DSecureEnabled = is3DSecureEnabled;
    },

    request3DSecure: function () {
        var formKey = '',
            self = this,
            url = $('centinel_url').value;

        if ($$('input[name="form_key"]').length) {
            formKey = $$('input[name="form_key"]')[0].value;
        }

        new Ajax.Request(url, {
            method: 'get',
            parameters: {form_key: formKey, isFrame: 1},
            onSuccess: function (response) {
                var centinelAuthBlock = $('centinel_authenticate_block'),
                    popup = $('amasty_before_centinel');

                if (centinelAuthBlock) {
                    centinelAuthBlock.remove();
                }
                popup.insert(response.responseText.evalJSON().html);

                if (popup.children.length === 0) {
                    return self.hidePopup();
                }

                popup.show();
                $("amscheckout-loading").hide();
            }
        });
    },

    showPopup: function () {
        if (!this.is3DSecureEnabled) {
            return;
        }
        updateCheckout('payment_method', this.request3DSecure.bind(this));
    },

    hidePopup: function () {
        $('amasty_before_centinel').hide();
        this.noSkip = false;

        completeCheckout();

        this.noSkip = true;
    }
};
