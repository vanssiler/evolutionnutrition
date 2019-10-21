/**
 * Created by vanssiler on 22/12/15.
 */

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.5";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

!function(n,r){"function"==typeof define&&define.amd?define(r):"object"==typeof exports?module.exports=r():n.transformicons=r()}(this||window,function(){"use strict";var n={},r="tcon-transform",t={transform:["click"],revert:["click"]},e=function(n){return"string"==typeof n?Array.prototype.slice.call(document.querySelectorAll(n)):"undefined"==typeof n||n instanceof Array?n:[n]},o=function(n){return"string"==typeof n?n.toLowerCase().split(" "):n},f=function(n,r,f){var c=(f?"remove":"add")+"EventListener",u=e(n),s=u.length,a={};for(var l in t)a[l]=r&&r[l]?o(r[l]):t[l];for(;s--;)for(var d in a)for(var v=a[d].length;v--;)u[s][c](a[d][v],i)},i=function(r){n.toggle(r.currentTarget)};return n.add=function(r,t){return f(r,t),n},n.remove=function(r,t){return f(r,t,!0),n},n.transform=function(t){return e(t).forEach(function(n){n.classList.add(r)}),n},n.revert=function(t){return e(t).forEach(function(n){n.classList.remove(r)}),n},n.toggle=function(t){return e(t).forEach(function(t){n[t.classList.contains(r)?"revert":"transform"](t)}),n},n});

ChangeQty = function() {
    jQuery("div.qty-box").append('<button type="button" value="+" id="add1" class="plus"><i class="icon-up"></i></button>').append(' <button type="button" value="-" id="minus1" class="minus"><i class="icon-down"></i></button>');
    jQuery(".plus").click(function()
    {
        var currentVal = parseInt(jQuery(".qty").val());

        if (!currentVal || currentVal=="" || currentVal == "NaN") currentVal = 0;

        jQuery(".qty").val(currentVal + 1);
    });

    jQuery(".minus").click(function()
    {
        var currentVal = parseInt(jQuery(".qty").val());
        if (currentVal == "NaN") currentVal = 0;
        if (currentVal > 0)
        {
            jQuery(".qty").val(currentVal - 1);
        }
    });
}

jQuery( function( $ ) {
    $(document).ready(function(){
        //alert('jQuery OK!');
    });

    $(window).scroll(function() {

        if ($(this).scrollTop() > 250) {
            $('#topnav').fadeIn(400);
        }else{
            $('#topnav').fadeOut(400);
        }
    });

});
