define(function (require) {
    "use strict";
    var Backbone = require('backbone'),

    Question = Backbone.Model.extend({
        urlRoot: 'http://api.yaka.nu/questions',
        idAttribute: 'id'
    }),
    Questions = Backbone.Collection.extend({
       url: 'http://api.yaka.nu/questions'
    });

    return {
        Question: Question,
        Questions: Questions
    };
});