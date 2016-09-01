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


    HMC.Views.ReportView = Backbone.View.extend({

        el: $('#report_dialog'),

        initialize: function () {
            _.bindAll(this, 'render', '_sync_ui', '_sync_model',  'open', 'close', 'save', 'destroy');
            this.value = null;
            this.description = null;
            this.category = null;

        },

        render: function () {
            var buttons = {'Ok': this.save};

            if (!this.model.isNew()) {
                _.extend(buttons, {'Delete': this.destroy});
            }
            _.extend(buttons, {'Cancel': this.close});

            this.$el.dialog({
                modal: true,
                title: (this.model.isNew() ? 'New' : 'Edit') + ' Report',
                buttons: buttons,
                open: this.open
            });

            $("#hmc_count").autocomplete({
                    source: "/wp-json/hmc/v1/voices",
                    focus: function (event, ui) {
                        console.log("focus");
                        $("#hmc_count").val(ui.item.name);
                        return false;
                    },
                    select: function (event, ui) {
                        console.log("select");
                        $("#hmc_count").val(ui.item.name);
                        $("#hmc_count_id").val(ui.item.id);
                        $("#hmc_count_description").html(ui.item.description);
                        return false;
                    }
                })
                .autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append('<span class="count_type type_' + item.type + '"></span> ' + item.name + ' ' + ( item.description !== null ? item.description  : ''))
                    .appendTo(ul);
            };
            return this;
        },

        _sync_ui: function () {
            this.$("#hmc_value_date").val(this.model.get('value_date'));

            if (! this.model.isNew()) {
                this.$("#hmc_value").val(this.model.get('value'));
                this.$("#hmc_description").val(this.model.get('description'));
                var category = this.model.get('category');
                this.$('#hmc_count_id').val(category['id']);
                this.$('#hmc_count').val(category['name']);
            }else{
                this.$("#hmc_value").val("15.0");
                this.$("#hmc_description").val("");
                this.$('#hmc_count_id').val("0");
                this.$('#hmc_count').val("");
            }
        },

        _sync_model: function () {

            var category = {};
            category['id'] = this.$("#hmc_count_id").val();

            var report = {};
            report['value_date'] = this.$("#hmc_value_date").val();
            report['posting_date'] = moment().toISOString();
            report['value'] = this.$("#hmc_value").val();
            report['category'] = category;
            report['description'] = this.$("#hmc_description").val();

            this.model.set(report);
        },

        open: function () {
            this._sync_ui();
        },

        close: function () {
            this.$el.dialog('close');
        },

        save: function () {
            this._sync_model();
            if (this.model.isNew()) {
                this.collection.create(this.model, {success: this.close});
            } else {
                this.model.save({}, {success: this.close});
            }

        },

        destroy: function () {
            if (confirm('Are you sure you want to DELETE this Count from database?')) {
                this.model.destroy({success: this.close});
            }
        }
    });


    HMC.Views.Calendar = Backbone.View.extend({

        tag: "div",

        // Instead of generating a new element, bind to the existing skeleton of
        // the App already present in the HTML.
        el: $("#hmc_calendar"),

        initialize: function () {
            this.collection= new HMC.Models.Reports();


            _.bindAll(this, 'render', 'select', '_addOne', '_addAll','eventClick','_fetch_events');
            
            this.listenTo(this.collection, 'reset', this._addAll );
            this.listenTo(this.collection, 'add', this._addOne );
            this.listenTo(this.collection, 'change', this._change);
            this.listenTo(this.collection, 'destroy', this._destroy);

            this.reportView = new HMC.Views.ReportView();


        },

        render: function () {
            this.$el.fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'basicWeek,month'
                },
                lang: navigator.language || navigator.userLanguage,
                eventLimit: true,
                defaultView: 'basicWeek',
                selectable: true,
                selectHelper: true,
                editable: true,
                ignoreTimezone: false,
                select: this.select,
                eventClick: this.eventClick,
                eventDrop: this.eventDropOrResize,
                eventResize: this.eventDropOrResize,
                events: this._fetch_events
            });
        },

        _fetch_events : function(iStart, iEnd, iTimezone, callback) {
            this.collection.fetch({reset: true});
        },

        eventClick: function (fcEvent) {
            this.reportView.model = this.collection.get(fcEvent.id);
            this.reportView.render();
        },

        eventDropOrResize: function (fcEvent) {
            this.collection.get(fcEvent.id).save({start: fcEvent.start, end: fcEvent.end});
        },


        select: function (startDate, endDate, allday) {
            this.reportView.collection = this.collection;
            this.reportView.model = new HMC.Models.Report({'value_date': startDate});
            this.reportView.render();
        },

        _addAll: function(){
            console.log("addAll");

            var objs = this.collection.toJSON();

            objs.forEach(function(_this) {
                return function(obj) {
                    _this._format_object(obj);
                    _this.$el.fullCalendar('renderEvent', obj);
                };
            }(this));

        },

        _addOne: function (model) {
            console.log("addOne");

            var obj = model.toJSON();
            this._format_object(obj);
            this.$el.fullCalendar('renderEvent', obj);
        },

        _format_object: function (obj){
            obj["title"] = obj.value + " € "+ obj.category.name;
            obj["start"] = obj.value_date;
            obj["allDay"] = true;
            obj['className'] = "count_type type_" + obj.category.type
        },

        _change: function (event) {
            console.log("changeOne");

            var obj = this.$el.fullCalendar('clientEvents', event.get('id'))[0];

            this._format_object(obj);

            this.$el.fullCalendar('updateEvent', obj);
            this.$el.fullCalendar('refetchEvents');
        },

        _destroy: function (event) {
            console.log("destroyOne");

            this.$el.fullCalendar('removeEvents', event.id);
        }


    });


    var page = new HMC.Views.Calendar().render();


});