define(function (require) {
    "use strict";
    var $                   = require('jquery'),
        _                   = require('underscore'),
        Backbone            = require('backbone'),
        models              = require('app/models/question'),
        tpl                 = require('text!template/question.html');

    return Backbone.View.extend({
        el: $('.content'),
        initialize: function(){
            if(!(localStorage.getItem("adi") && localStorage.getItem("soyadi"))) {
                window.location = "#";
            }
        },
        render: function(options){
            var question = new models.Question({id: options.id});
            question.fetch({
                success: function(){
                    $('.content').html(_.template(tpl,{question : question}));
                }
            });

        },
        events:{
            'click #evet' : 'yes',
            'click #hayir' : 'no'
        },
        yes: function() {
            var id = $('#id').val();
            localStorage.setItem(id,"E");
            if(parseInt(id)==419) {
                window.location = "#r/1";
            } else {
                window.location = "#q/"+(parseInt(id)+1);
            }
        },
        no: function() {
            var id = $('#id').val();
            localStorage.setItem(id,"H");
            if(parseInt(id)==419) {
                window.location = "#r/1";
            } else {
                window.location = "#q/"+(parseInt(id)+1);
            }
        }
    });
});