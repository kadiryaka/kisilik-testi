define(function (require) {
    "use strict";
    var $                   = require('jquery'),
        _                   = require('underscore'),
        Backbone            = require('backbone'),
        tpl                 = require('text!template/profile.html');

    return Backbone.View.extend({
        el: $('.content'),
        initialize: function(){
            if(!(localStorage.getItem("adi") && localStorage.getItem("soyadi"))) {
                window.location = "#";
            }
        },
        render: function(options){
            $.get("http://api.yaka.nu/profile/"+options.id, function(data) {
                var ProfileQuestion    = Backbone.Model.extend();
                var ProfileQuestions   = Backbone.Collection.extend({
                    model : ProfileQuestion
                });

                var profileQuestions = new ProfileQuestions(JSON.parse(data));

                $('.content').html(_.template(tpl,{profileQuestions : profileQuestions.models}));
                $("#content").focus();
            });
        },
        events:{
            'click #bitir' : 'done'
        },
        done: function() {
            var id = $('#id').val();
            var total = 0;
            if(id==1) {
                for(var i=1;i<=20;i++) {
                    if($("input[name='"+i+"']").is(':checked')) {
                        total = total + 1;
                    }
                }
            }
            if(id==2) {
                for(var i=21;i<=40;i++) {
                    if($("input[name='"+i+"']").is(':checked')) {
                        total = total + 1;
                    }
                }
            }
            if(total!=20) {
                alert("Testinizde isaretlenmemis sorular var!");
            } else {
                if(parseInt(id)==2) {
                    for(var i=21;i<=40;i++) {
                        localStorage.setItem("p"+i,$('input[name='+i+']:checked').val());
                    }
                    window.location = "#result";
                } else {
                    for(var i=1;i<=20;i++) {
                        localStorage.setItem("p"+i,$('input[name='+i+']:checked').val());
                    }
                    window.location = "#p/"+(parseInt(id)+1);
                }
            }
        }
    });
});