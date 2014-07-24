define(function (require) {
    "use strict";
    var $                   = require('jquery'),
        _                   = require('underscore'),
        Backbone            = require('backbone'),
        ZeroClipboard       = require('ZeroClipboard'),
        models              = require('app/models/question'),
        tpl                 = require('text!template/login.html');

    return Backbone.View.extend({
        el: $('.content'),
        initialize: function(){
        },
        render: function(){
            $('.content').html(_.template(tpl));
        },
        events:{
            'click #giris' : 'login'
        },
        login: function() {
            if($("#adi").val()!="" && $("#soyadi").val()!="") {
                localStorage.setItem("adi",$("#adi").val());
                localStorage.setItem("soyadi",$("#soyadi").val());
                window.location = "#q/1";
            } else {
                alert("lütfen alanları doldurunuz.");
            }
        }
    });
});