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


HMC.Views.ReportView = Backbone.View.extend({

	el: $("#report_dialog"),

	events: {
		"click #hmc-report-ok": "save",
		"click #hmc-report-delete": "destroy",
		"click .hmc-report-cancel": "close",
		"input #hmc_count": "filterCount"
	},

	initialize: function() {
		_.bindAll(this, "render", "_sync_ui", "_sync_model", "open", "close", "save", "destroy");
		this.value = null;
		this.description = null;
		this.category = null;
	},

	filterCount: function(){

		console.log("pippo");
		if ( this.filter != $("#hmc_count").val() ){
			this.filter=$("#hmc_count").val();
			if (this.currentRequest != null){
				this.currentRequest.abort();
			}
			this.currentRequest=$.ajax({
				url: "/wp-json/hmc/v1/voices",
				method: "GET",
				data: "term="+encodeURIComponent(this.filter),
				dataType: "json",
				beforeSend: function() {
					$("#acc_0").hide();
					$("#accordion_0" ).html("");
					$("#acc_1").hide();
					$("#accordion_1" ).html("");
					$("#acc_2").hide();
					$("#accordion_2" ).html("");
					$("#acc_3").hide();
					$("#accordion_3" ).html("");
					$("#acc_4").hide();
					$("#accordion_4" ).html("");
					$("#acc_5").hide();
					$("#accordion_5" ).html("");
				}
			}).done(function(data){
				data.forEach(function(item){
					$("#acc_"+ item.type ).show();
					$("#acc_label_"+ item.type ).html(HMC.COUNT_TYPE.getLabel(item.type));
					$("#accordion_"+ item.type ).append("<input type='radio' name='selected_count_id' value='"+ item.id +"' ><span class='count_type type_"+ item.type+"' ></span> "+ item.name + " " + ( item.description !== null ? item.description : "")+"</input><br><br>");
				});
			});
		}


	},

	selectCount: function(item){
		$("#hmc_count").val(item.name);
		$("#hmc_count_id").val(item.id);
		$("#hmc_count_description").html(item.description);
	},

	render: function() {

		this.open();

		this.$el.show();

		$("#acc_0").hide();
		$("#acc_1").hide();
		$("#acc_2").hide();
		$("#acc_3").hide();
		$("#acc_4").hide();
		$("#acc_5").hide();

		$("#hmc-report-ok").show();

		if (!this.model.isNew()) {
			$("#hmc-report-delete").show();
		}else{
			$("#hmc-report-delete").hide();
		}
		$(".hmc-report-cancel").show();

		$(".hmc-modal-title").text((this.model.isNew() ? "New" : "Edit") + " Report");





      /*
      $("#hmc_count").autocomplete({
        source: "/wp-json/hmc/v1/voices",
        select: function(event, ui) {
          $("#hmc_count").val(ui.item.name);
          $("#hmc_count_id").val(ui.item.id);
          $("#hmc_count_description").html(ui.item.description);
          return false;
        }
      })
        .autocomplete("instance")._renderItem = function(ul, item) {
        return $("<li>")
          .append('<span class="count_type type_' + item.type + '"></span> ' + item.name + ' ' + ( item.description !== null ? item.description : ''))
          .appendTo(ul);
      };
      */

		return this;
	},

	_processing: function(isInProgress) {
		if (isInProgress) {
			this.$("#hmc-processing").show();
		} else {
			this.$("#hmc-processing").hide();
		}
	},

	_sync_ui: function() {
		this._processing(true);

		this.$("#hmc_value_date").val(this.model.get("value_date"));

		if (!this.model.isNew()) {
			this.$("#hmc_value").val(this.model.get("value"));
			this.$("#hmc_description").val(this.model.get("description"));
			var category = this.model.get("category");
			this.$("#hmc_count_id").val(category["id"]);
			this.$("#hmc_count").val(category["name"]);
		} else {
			this.$("#hmc_value").val("");
			this.$("#hmc_description").val("");
			this.$("#hmc_count_id").val("0");
			this.$("#hmc_count").val("");
		}
		this._processing(false);
	},

	_sync_model: function() {

		var category = {};
		category["id"] = this.$("#hmc_count_id").val();

		var report = {};
		report["value_date"] = this.$("#hmc_value_date").val();
		report["posting_date"] = moment().toISOString();
		report["value"] = this.$("#hmc_value").val();
		report["category"] = category;
		report["description"] = this.$("#hmc_description").val();

		this.model.set(report);
	},

	open: function() {
		this._sync_ui();
	},

	close: function() {
		this._processing(false);
		this.$el.hide();
	},

	save: function() {
		this._sync_model();
		this._processing(true);
		if (this.model.isNew()) {
			this.collection.create(this.model, {success: this.close});
		} else {
			this.model.save({}, {success: this.close});
		}

	},

	destroy: function() {
		if (confirm("Are you sure you want to DELETE this Count from database?")) {
			this.model.destroy({success: this.close});
		}
	},

	onEdit: function(model) {
		this.model = model;
		this.render();
	},

	onNew: function(startDate, collection) {
		this.collection = collection;
		this.model = new HMC.Models.Report({"value_date": startDate});
		this._sync_ui();
		this.render();
	}

});
