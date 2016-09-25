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




    HMC.Views.SummPieChart =  Backbone.View.extend({

        elementId: null,
        iChart: null,
        chartId: null,
        dataset: null,
        count_name: null,
        count_type: null,
        title: null,
        color: null,

        initialize: function (options) {
            this.elementId = options.id;
            this.count_name = options.name ;
            $.ajax({
                context: this, url: "/wp-json/hmc/v1/stats/", success: function (result) {
                    this.parseData(result);
                    this.render();
                }
            });
            this
        },

        parseData: function (data_result) {
            var entrate=0;
            var uscite=0;


            var labels = new Array();
            var data = new Array();
            var backgroundColor = new Array();

            data_result.items.forEach(function (entry) {
                console.log(entry.type);
                if (entry.type != HMC.COUNT_TYPE.ENTRATE.id ){
                    labels.push(entry.name);
                    data.push(entry.total);
                    backgroundColor.push(HMC.COUNT_TYPE.USCITE_FISSE.color);
                    uscite += parseFloat(entry.total);
                }else{
                    entrate += parseFloat(entry.total);
                }
            }, this);

            labels.push("Cassa");
            data.push(entrate - uscite);
            backgroundColor.push("#efefef");

            this.dataset = {
                labels: labels,
                datasets: [{data: data, backgroundColor: backgroundColor}]
            }
            this.title = this.count_name + " € " + data_result.total;
        },
        render: function () {
            var private_options = {
                responsive: false,
                padding: 2,
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: this.title
                }
            };

            var ctx = document.getElementById(this.elementId).getContext("2d");
            this.iChart = new Chart(ctx, {
                type: 'doughnut',
                data: this.dataset,
                options: private_options
            });
            this;
        }
    });


    HMC.Views.PieChart = Backbone.View.extend({

        elementId: null,
        iChart: null,
        chartId: null,
        dataset: null,
        count_name: null,
        count_type: null,
        title: null,
        color: null,


        initialize: function (options) {
            this.elementId = options.id;
            this.count_name = options.name || "unamed";
            this.count_type = options.type_id || "0";
            this.color = options.color || '#' + (Math.random().toString(16) + '0000000').slice(2, 8)
            $.ajax({
                context: this, url: "/wp-json/hmc/v1/stats/" + this.count_type, success: function (result) {
                    this.parseData(result);
                    this.render();
                }
            });
            this
        },
        parseData: function (data_result) {
            var labels = new Array();
            var data = new Array();
            var backgroundColor = new Array();

            data_result.items.forEach(function (entry) {
                labels.push(entry.name);
                data.push(entry.total);
                backgroundColor.push(this.change_brightness(this.color, ( 100 - (entry.total * 100) / data_result.total)));
            }, this);


            this.dataset = {
                labels: labels,
                datasets: [{data: data, backgroundColor: backgroundColor}]
            }
            this.title = this.count_name + " € " + data_result.total;
        },

        change_brightness: function (hex, percent) {
            // strip the leading # if it's there
            hex = hex.replace(/^\s*#|\s*$/g, '');

            // convert 3 char codes --> 6, e.g. `E0F` --> `EE00FF`
            if (hex.length == 3) {
                hex = hex.replace(/(.)/g, '$1$1');
            }

            var r = parseInt(hex.substr(0, 2), 16),
                g = parseInt(hex.substr(2, 2), 16),
                b = parseInt(hex.substr(4, 2), 16);

            return '#' +
                ((0 | (1 << 8) + r + (256 - r) * percent / 100).toString(16)).substr(1) +
                ((0 | (1 << 8) + g + (256 - g) * percent / 100).toString(16)).substr(1) +
                ((0 | (1 << 8) + b + (256 - b) * percent / 100).toString(16)).substr(1);
        },

        render: function () {
            var private_options = {
                responsive: false,
                padding: 2,
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: this.title
                }
            };

            var ctx = document.getElementById(this.elementId).getContext("2d");
            this.iChart = new Chart(ctx, {
                type: 'pie',
                data: this.dataset,
                options: private_options
            });
            this;
        }
    });


    var type_0 = new HMC.Views.PieChart({
        id: 'cat_type_0',
        name: HMC.COUNT_TYPE.HOBBIES_TEMPO_LIBERO.label,
        type_id: HMC.COUNT_TYPE.HOBBIES_TEMPO_LIBERO.id,
        color: HMC.COUNT_TYPE.HOBBIES_TEMPO_LIBERO.color
    });
    var type_1 = new HMC.Views.PieChart({
        id: 'cat_type_1',
        name: HMC.COUNT_TYPE.IMPREVISTI_EXTRA.label,
        type_id: HMC.COUNT_TYPE.IMPREVISTI_EXTRA.id,
        color: HMC.COUNT_TYPE.IMPREVISTI_EXTRA.color
    });
    var type_2 = new HMC.Views.PieChart({
        id: 'cat_type_2',
        name: HMC.COUNT_TYPE.SERVIZI_OPTIONAL.label,
        type_id: HMC.COUNT_TYPE.SERVIZI_OPTIONAL.id,
        color: HMC.COUNT_TYPE.SERVIZI_OPTIONAL.color
    });
    var type_3 = new HMC.Views.PieChart({
        id: 'cat_type_3',
        name: HMC.COUNT_TYPE.SOPRAVVIVENZA.label,
        type_id: HMC.COUNT_TYPE.SOPRAVVIVENZA.id,
        color: HMC.COUNT_TYPE.SOPRAVVIVENZA.color
    });

    var sum= new HMC.Views.SummPieChart({
        id: 'mouth_stat',
        name: 'statistic'
    });


    HMC.Views.ReportView = Backbone.View.extend({

        el: $('#report_dialog'),

        initialize: function () {
            _.bindAll(this, 'render', '_sync_ui', '_sync_model', 'open', 'close', 'save', 'destroy');
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
                    .append('<span class="count_type type_' + item.type + '"></span> ' + item.name + ' ' + ( item.description !== null ? item.description : ''))
                    .appendTo(ul);
            };
            return this;
        },

        _processing: function (isInProgress) {
            console.log("aa");
            if (isInProgress) {
                this.$("#hmc-processing").show();
            } else {
                this.$("#hmc-processing").hide();
            }
        },

        _sync_ui: function () {
            this._processing(true);

            this.$("#hmc_value_date").val(this.model.get('value_date'));

            if (!this.model.isNew()) {
                this.$("#hmc_value").val(this.model.get('value'));
                this.$("#hmc_description").val(this.model.get('description'));
                var category = this.model.get('category');
                this.$('#hmc_count_id').val(category['id']);
                this.$('#hmc_count').val(category['name']);
            } else {
                this.$("#hmc_value").val("");
                this.$("#hmc_description").val("");
                this.$('#hmc_count_id').val("0");
                this.$('#hmc_count').val("");
            }
            this._processing(false);
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
            this._processing(false);
            this.$el.dialog('close');
        },

        save: function () {
            this._sync_model();
            this._processing(true);
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
            this.collection = new HMC.Models.Reports();


            _.bindAll(this, 'render', 'select', '_addOne', '_addAll', 'eventClick', '_fetch_events');

            this.listenTo(this.collection, 'reset', this._addAll);
            this.listenTo(this.collection, 'add', this._addOne);
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

        _fetch_events: function (iStart, iEnd, iTimezone, callback) {
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

        _addAll: function () {
            console.log("addAll");

            var objs = this.collection.toJSON();

            objs.forEach(function (_this) {
                return function (obj) {
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

        _format_object: function (obj) {
            obj["title"] = obj.value + " € " + obj.category.name;
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