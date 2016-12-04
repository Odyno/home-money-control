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

/**
 * Created by astaniscia on 03/12/16.
 */
// The Application
// ---------------

// Our overall **AppView** is the top-level piece of UI.
HMC.Views.CountsTable = Backbone.View.extend({

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
    this.counts = new HMC.Models.Counts();


    this.listenTo(this.counts, 'add', this.addOne);
    this.listenTo(this.counts, 'remove', this.notifyRemove);
    this.listenTo(this.counts, 'error', this.notifyError);
    this.listenTo(this.counts, 'all', this.render);

    this.footer = this.$('footer');
    this.header = this.$('header');
    this.main = $('#main');

    this.counts.fetch();
  },

  // Re-rendering the App just means refreshing the statistics -- the rest
  // of the app doesn't change.
  render: function () {

    if (this.counts.length) {
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
    var view = new HMC.Views.CountTableField({model: count});
    this.$("#count-list").prepend(view.render().el);
  },



  createOnCount: function (e) {
    var data = new HMC.Models.Count();
    data.set({ "name" : "unamed" });
    return this.counts.create(data);
  },


});

