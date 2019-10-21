function clearHolderName(field) {
    var  newValue = removeNumbersAndSpecialCharacters(field.value);
    changeFieldValue(field, newValue);
    return newValue;
}

function clearCardNumber(field) {
    var  newValue = removeNotNumericCharacters(field.value);
    changeFieldValue(field, newValue);
    return newValue;
}

function clearCvv(field){
    var  newValue = removeNotNumericCharacters(field.value);
    changeFieldValue(field, newValue);
    return newValue;
}

function removeNumbersAndSpecialCharacters(string) {
    return string.replace(/[^a-zA-Z ]/g, "");
}

function removeNotNumericCharacters(string) {
    return string.replace(/[^0-9]/g, "");
}

function changeFieldValue(field, newValue) {
    field.value = newValue;
}
