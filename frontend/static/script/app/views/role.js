define(function (require) {
    "use strict";
    var $                   = require('jquery'),
        _                   = require('underscore'),
        Backbone            = require('backbone'),
        tpl                 = require('text!template/role.html');

    return Backbone.View.extend({
        el: $('.content'),
        initialize: function(){
            if(!(localStorage.getItem("adi") && localStorage.getItem("soyadi"))) {
                window.location = "#";
            }
        },
        render: function(options){
            $.get("http://api.yaka.nu/role/"+options.id, function(data) {
                var RoleQuestion    = Backbone.Model.extend();
                var RoleQuestions   = Backbone.Collection.extend({
                    model : RoleQuestion
                });

                var roleQuestions = new RoleQuestions(JSON.parse(data));

                $('.content').html(_.template(tpl,{roleQuestions : roleQuestions.models}));
                $("#content").focus();
            });
        },
        events:{
            'click #tamam' : 'ok'
        },
        ok: function() {
            var total = 0;
            for(var i=1;i<9;i++) {
                total += parseInt($("#i"+i).val());
            } if(total!=10) {
                alert("10 puanı hatalı dağıttınız.");
            } else {
                var id = $('#id').val();
                if(parseInt(id)==7) {
                    window.location = "#p/1";
                } else {
                    window.location = "#r/"+(parseInt(id)+1);
                }
            }
        }
    });
});