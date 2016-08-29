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
            "click a.save": "new"
        },

        initialize: function () {

            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },

        render: function () {
            this.$el.html(this.template(this.model.toJSON()));
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

        new:function (evt) {
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
            alert("Done!");

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
            "keypress #new-todo": "createOnEnter"
        },

        // At initialization we bind to the relevant events on the `Todos`
        // collection, when items are added or changed. Kick things off by
        // loading any preexisting todos that might be saved in *localStorage*.
        initialize: function () {

            this.input = this.$("#new-todo");

            this.listenTo(Counts, 'add', this.addOne);
            this.listenTo(Counts, 'all', this.render);

            this.footer = this.$('footer');
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

        // Add a single todo item to the list by creating a view for it, and
        // appending its element to the `<ul>`.
        addOne: function (count) {
            var view = new HMC.Views.Count({model: count});
            this.$("#count-list").append(view.render().el);
        },


        // If you hit return in the main input field, create new **Todo** model,
        // persisting it to *localStorage*.
        createOnEnter: function (e) {
            if (e.keyCode != 13) return;
            if (!this.input.val()) return;

            Todos.create({title: this.input.val()});
            this.input.val('');
        },

        // Clear all done todo items, destroying their models.
        clearCompleted: function () {
            _.invoke(Todos.done(), 'destroy');
            return false;
        },

        toggleAllComplete: function () {
            var done = this.allCheckbox.checked;
            Todos.each(function (todo) {
                todo.save({'done': done});
            });
        }

    });

    // Finally, we kick things off by creating the **App**.
    var PageCountView = new HMC.App;


});