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

HMC.Views.ReportTableField = Backbone.View.extend({
  // Each person will be shown as a table row
  tagName: 'tr',

  events: {
    "click .hmc-delete-report": "destroy",
    "click .hmc-edit-report": ""
  },


  initialize: function(options) {
    // Ensure our methods keep the `this` reference to the view itself
    _.bindAll(this, 'render');

    // If the model changes we need to re-render
    this.model.bind('change', this.render);
  },

  destroy: function() {
    if (confirm('Are you sure you want to DELETE this Count from database?')) {
      this.model.destroy();
    }
  },

  render: function() {
    // Clear existing row data if needed
    this.$el.empty();

    this.$el.addClass("type_" + this.model.get('category')['type']);

    // Write the table columns
    this.$el.append($('<td>' + moment(this.model.get('value_date')).fromNow() + '</td>'));
    this.$el.append($('<td>' + this.model.get('category')['name'] + '</td>'));
    this.$el.append($('<td>' + this.model.get('description') + '</td>'));
    this.$el.append($('<td> <span class="enMoney">' + this.model.get('value') + '</span> <a class="alignright hmc-delete-report" ><span class="dashicons dashicons-trash"></span></a></td>'));
    return this;
  }
});
