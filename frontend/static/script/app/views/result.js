define(function (require) {
    "use strict";
    var $                   = require('jquery'),
        _                   = require('underscore'),
        Backbone            = require('backbone'),
        tpl                 = require('text!template/result.html');

    return Backbone.View.extend({
        el: $('.content'),
        initialize: function(){
            if(!(localStorage.getItem("adi") && localStorage.getItem("soyadi"))) {
                window.location = "#";
            }
        },
        render: function(){

                var data = [];
                var error = false;
                if(localStorage.getItem('adi') && localStorage.getItem('soyadi')) {
                    var obj = {};
                    obj["adi"] = localStorage.getItem('adi');
                    data.push(obj);
                    var obj2 = {};
                    obj2["soyadi"] = localStorage.getItem('soyadi');
                    data.push(obj2);
                } else {
                    error = true;
                }
                for(var i=1;i<=419;i++) {
                    if(localStorage.getItem(''+i)) {
                        var obj = {};
                        obj[i] = localStorage.getItem(''+i);
                        data.push(obj);
                    } else {
                        error = true;
                    }
                }
                for(var i=1;i<=40;i++) {
                    if(localStorage.getItem('p'+i)) {
                        var obj = {};
                        obj['p'+i] = localStorage.getItem('p'+i);
                        data.push(obj);
                    } else {
                        error = true;
                    }
                }
                if(error) {
                    alert("Lütfen test aşamalarını kontrol ediniz.");
                } else {

                    $.ajax({
                        type: "POST",
                        url: "http://api.yaka.nu/save",
                        data: JSON.stringify(data),
                        success: function(data) {

                            $('.content').html(_.template(tpl,{results : data}));
                            $("#content").focus();
                        },
                        error: function() {
                            alert("Hata.");
                        },
                        dataType: "json"
                    });

                }
        },
        events:{
            'click #bye' : 'bye'
        },
        bye: function() {
            localStorage.clear();
            window.location = "#";
        }
    });
});