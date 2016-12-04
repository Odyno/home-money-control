/*
 * Copyright 2012  Alessandro Staniscia  (email : alessandro@staniscia.net)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

jQuery(document).ready(function ($) {
    

    var Counts = new HMC.Models.Counts;

    HMC.Views.Count = Backbone.View.extend({

        //... is a list tag.
        tagName: "li",

        // Cache the template function for a single item.
        template: _.template($('#item-template').html()),

        // The DOM events specific to an item.
        events: {
            "click a.change": function () {
                this.set_edit_mode(true);
            },
            "dblclick": function () {
                this.set_edit_mode(true);
            },
            "click .close": function () {
                this.set_edit_mode(false);
            },
            "click a.destroy": "clear_model",
            "change input": "changed",
            "change select": "changed",
            "change textarea": "changed",
            "click a.save": "save"
        },

        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function () {
            var json_model=this.model.toJSON();
            this.$el.html(this.template(json_model));
            if (json_model['name'] === "unamed"){
                this.set_edit_mode(true);
            }
            return this;
        },

        set_edit_mode: function (is_edit_mode) {
            if (is_edit_mode) {
                this.$el.addClass("editing");
            } else {
                this.$el.removeClass("editing");
            }
        },


        changed:function (evt) {
            var changed = evt.currentTarget;
            var ivalue = $(evt.currentTarget).val();
            var iname = $(evt.currentTarget).attr('name');
            var obj = {};
            obj[iname] = ivalue;
            this.model.set(obj);

            this.model.save();

        },

        save:function (evt) {
            var changed = evt.currentTarget;
            var ivalue = $(evt.currentTarget).val();
            var iname = $(evt.currentTarget).attr('name');
            var obj = {};
            obj[iname] = ivalue;
            this.model.set(obj);

            this.model.save();

        },


        clear_model: function () {
            if (confirm('Are you sure you want to DELETE this Count from database?')) {
                this.model.destroy();
            }
        }

    });


    // The Application
    // ---------------

    // Our overall **AppView** is the top-level piece of UI.
    HMC.App = Backbone.View.extend({

        // Instead of generating a new element, bind to the existing skeleton of
        // the App already present in the HTML.
        el: $("#countview"),


        // Delegated events for creating new items, and clearing completed ones.
        events: {
            "click a.create": "createOnCount"
        },

        // At initialization we bind to the relevant events on the `Todos`
        // collection, when items are added or changed. Kick things off by
        // loading any preexisting todos that might be saved in *localStorage*.
        initialize: function () {

            this.input = this.$("#new-todo");

            this.listenTo(Counts, 'add', this.addOne);
            this.listenTo(Counts, 'remove', this.notifyRemove);
            this.listenTo(Counts, 'error', this.notifyError);
            this.listenTo(Counts, 'all', this.render);

            this.footer = this.$('footer');
            this.header = this.$('header');
            this.main = $('#main');

            Counts.fetch();
        },

        // Re-rendering the App just means refreshing the statistics -- the rest
        // of the app doesn't change.
        render: function () {

            if (Counts.length) {
                this.main.show();
            } else {
                this.main.hide();
            }

        },

        notifyRemove: function () {
           this.header.append('<div class="notice notice-info is-dismissible"><p>Delete Done!</p></div>')
        },

        notifyError: function () {
            this.header.append('<div class="notice notice-warning is-dismissible"><p>Error appears!</p></div>')
        },


        // Add a single todo item to the list by creating a view for it, and
        // appending its element to the `<ul>`.
        addOne: function (count) {
            console.log("called");
            var view = new HMC.Views.Count({model: count});
            this.$("#count-list").prepend(view.render().el);
        },



        createOnCount: function (e) {
            var data = new HMC.Models.Count();
            data.set({ "name" : "unamed" });
            return Counts.create(data);
        },
        

    });

    // Finally, we kick things off by creating the **App**.
    var PageCountView = new HMC.App;


});