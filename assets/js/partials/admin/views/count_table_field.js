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

HMC.Views.CountTableField = Backbone.View.extend({

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