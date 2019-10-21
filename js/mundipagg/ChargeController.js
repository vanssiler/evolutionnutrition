var dialogIsInited = false;
var currentCharge = {};
var currentOrderId = '';
var currentUsername = '';
var currentDialogHtml = '';

var confirmChargeOperation = function() {
    currentCharge.credential =
        document.getElementById('charge-operation-credential').value;
    currentCharge.operationValue =
        document.getElementById('charge-operation-value').value;
    currentCharge.operationValue =
        parseFloat(currentCharge.operationValue) * 100;
    currentCharge.username = currentUsername;

    currentDialogHtml = document.getElementById('charge-dialog').innerHTML;
    document.getElementById('charge-dialog').innerHTML =
        document.getElementById('charge-dialog-wait-modal').innerHTML;

    apiRequest('/mp-paymentmodule/charge',currentCharge,function(data){
        if(data !== false) {
            switch(data.status) {
                case 200:
                    reloadAndGoToCharges();
                break;
                default:
                    document.getElementById('charge-dialog').innerHTML =
                        currentDialogHtml;
                    initDialog();
                    resetChargeDialog({
                        operation: currentCharge.operation,
                        charge: currentCharge
                    });
                    showChargeDialogError(data.message,data.details);
            }
        }
    },'POST');
};

var reloadAndGoToCharges = function() {
    var url = new URL(window.location.href);
    url.searchParams.set("mp-gotocharges",'');
    window.location = url.href;
};

var goToCharges = function() {
    var url = new URL(window.location.href);
    if (url.searchParams.get('mp-gotocharges') !== null) {
        document.getElementById('sales_order_view_tabs_order_charges').click();
    }
};

function apiRequest(url, data, callback, method, json, callbackArgsObj) {
    var xhr = window.XMLHttpRequest ? 
        new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    xhr.open(method, url);

    if (json) {
        xhr.setRequestHeader("content-type", "application/json");
    }

    xhr.onreadystatechange = function () {
        if (xhr.readyState > 3 && xhr.status == 200) {
            callback(JSON.parse(xhr.responseText),callbackArgsObj);
        }else{
            callback(false);
        }
    };

    xhr.send(JSON.stringify(data));

    return xhr;
}

var showChargeDialog = function(operation,element) {
    if (!dialogIsInited) {
        dialogIsInited = true;
        initDialog();
    }

    var charge = getChargeDataFromElement(element);
    resetChargeDialog({
        operation,
        charge
    });
    var popup = document.getElementById('charge-dialog');
    var modal = document.getElementById('message-popup-window-mask');
    modal.show();
    popup.show();
};

var showChargeDialogError = function(error,details) {
    var message = error;
    if (typeof details !== 'undefined') {
        message += ' : ' + details;
    }
    var errorDiv = document.getElementById('charge-dialog-errors')
    errorDiv.innerHTML = message;
    errorDiv.show();
};

var initDialog = function() {
    var nodes = document.getElementsByName('total_or_partial');
    for (var i = 0, l = nodes.length; i < l; i++)
    {
        nodes[i].onchange = checkTotalOrPartial;
    }
};

var hideChargeDialog = function() {
    document.getElementById('charge-dialog').hide();
    document.getElementById('charge-dialog-errors').hide();
    document.getElementById('message-popup-window-mask').hide();
};

var getChargeDataFromElement =  function(element) {
    var tableRowTDs = element.parentElement.parentElement.childElements();
    return {
        id: tableRowTDs[0].innerHTML.trim(),
        operationName: element.innerHTML.trim(),
        stringValue: tableRowTDs[1].innerHTML.trim(),
        centsValue: tableRowTDs[1].innerHTML.trim().replace(/\D/g, ''),
        capturedValue: tableRowTDs[2].innerHTML.trim().replace(/\D/g, ''),
        canceledValue: tableRowTDs[3].innerHTML.trim().replace(/\D/g, ''),
        typeName: tableRowTDs[5].innerHTML.trim(),
        orderId: currentOrderId
    };
};

var resetChargeDialog = function (data) {
    currentCharge = data.charge;
    currentCharge.operation = data.operation;

    document.getElementById('charge-operation-value').value = '';
    document.getElementById('charge-operation-credential').value = '';

    document.getElementById('charge-id').innerHTML = data.charge.id;
    document.getElementById('charge-stringValue').innerHTML = data.charge.stringValue;
    document.getElementById('charge-typeName').innerHTML = data.charge.typeName;

    var valueInput = document.getElementById('charge-operation-value');
    valueInput.value = parseInt(data.charge.centsValue) / 100;
    valueInput.max = valueInput.value;

    var elements = document.getElementsByClassName('charge-operation');
    for (var i = 0, l = elements.length; i < l; i++)
    {
        elements[i].innerHTML=data.charge.operationName;
    }

    elements = document.getElementsByName('total_or_partial');
    for (var i = 0, l = elements.length; i < l; i++)
    {
        elements[i].checked = false;
    }
    elements[0].checked = true;

    checkTotalOrPartial();

};

var checkTotalOrPartial = function() {
    var elements = document.getElementsByName('total_or_partial');
    var value = '';
    var valueWrapper = document.getElementById('charge-operation-value-wrapper');
    for (var i = 0, l = elements.length; i < l; i++)
    {
        if (elements[i].checked)
        {
            value = elements[i].value;
        }
    }
    valueWrapper.hide();
    if (value == 'partial') {
        valueWrapper.show();
    }
    currentCharge.operationType = value;
};