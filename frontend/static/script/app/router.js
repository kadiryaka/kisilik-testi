define(function (require) {
    "use strict";
    var Backbone        = require('backbone'),
        IndexView       = require('app/views/index'),
        QuestionView    = require('app/views/question'),
        RoleView        = require('app/views/role'),
        ProfileView     = require('app/views/profile'),
        ResultView      = require('app/views/result'),

        questionView    = new QuestionView(),
        indexView       = new IndexView(),
        roleView        = new RoleView(),
        profileView     = new ProfileView(),
        resultView      = new ResultView();

    return Backbone.Router.extend({
        routes: {
            '' : 'index',
            'q/:id' : 'question',
            'r/:id' : 'role',
            'p/:id' : 'profile',
            'result': 'result'
        },
        index : function() {
            indexView.render();
        },
        question : function (id) {
            questionView.render({id:id});
        },
        role : function(id) {
            roleView.render({id:id});
        },
        profile : function(id) {
            profileView.render({id:id});
        },
        result : function() {
            resultView.render();
        }
    });
});