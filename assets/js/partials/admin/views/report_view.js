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
		"input #hmc_count": "searchBestCount",
		"change input[type=radio]": 'selectCount'
	},

	initialize: function() {
		_.bindAll(this, "render", "_sync_ui", "_sync_model", "close", "save", "destroy");
		this.value = null;
		this.description = null;
		this.category = null;

		this._processing(false,"search");
		this._processing(false,"save");
		this._processing(false,"delete");
	},

	/**
	 * Seaqrch che best count in according of name
	 */
	searchBestCount: function(){
		var me= this;
		if ( this.filter != $("#hmc_count").val() ){

			this.filter=$("#hmc_count").val();

			if (me.currentRequest != null){
				me.currentRequest.abort();
			}

			this.currentRequest=$.ajax({
				url: "/wp-json/hmc/v1/voices",
				method: "GET",
				data: "term="+encodeURIComponent(this.filter),
				dataType: "json",
				beforeSend: function() {

				 me.hideCountList();
				 me._processing(true,"search");
				}
			}).done(function(data){
				data.forEach(function(item){
					$("#acc_"+ item.type ).show();
					$("#acc_label_"+ item.type ).html(HMC.COUNT_TYPE.getLabel(item.type));
					$("#accordion_"+ item.type ).append(
						"<label>"+
						"  <input type='radio' name='selected_count_id' value='' data-id='"+ item.id +"' data-name='"+ item.name +"' data-description='"+ item.description +"' />" +
						"  <span>"+  ( item.description !== null ?   ( item.name +  " - " + item.description ) : item.name   )  + "</span>"+
						"</label><br>"
					);
				});
				me._processing(false,"search");
			});
		}


	},

	hideCountList: function(){
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
	},

	selectCount: function(item){
		var obj=$(item.target);
		$("#hmc_count").val(obj.data('name'));
		$("#hmc_count_id").val(obj.data('id'));
		$("#hmc_count_description").html(obj.data('description'));
	},

	configureButtons: function(){
		"use strict";
		$("#hmc-report-ok").show();
		if (!this.model.isNew()) {
			$("#hmc-report-delete").show();
		}else{
			$("#hmc-report-delete").hide();
		}
		$(".hmc-report-cancel").show();
	},

	configureTitle: function(){
		"use strict";
		$(".hmc-modal-title").text((this.model.isNew() ? "New" : "Edit") + " Report");
	},

	render: function() {
		this._sync_ui();
		this.hideCountList();
		this.configureButtons();
		this.configureTitle();
		$("#hmc_value_date").datepicker();
		this.$el.show();
		return this;
	},

	_processing: function(isInProgress,type) {
		if (isInProgress) {
			this.$("#hmc-"+type+"-processing").show();
		} else {
			this.$("#hmc-"+type+"-processing").hide();
		}
	},

	_sync_ui: function() {
		this._processing(true,"save");

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
		this._processing(false,"save");
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


	close: function() {
		this._processing(false,"delete");
		this._processing(false,"search");
		this._processing(false,"save");
		this.$el.hide();
	},

	save: function() {
		var me= this;
		this._sync_model();
		this._processing(true,"save");
		if (! this.model.isValid()){
			this._processing(false,"save");
			alert('Please fill all the attribute');
		}
		if (this.model.isNew()) {
			this.collection.create(this.model, {success: me.close});
		} else {
			this.model.save(null, {
				success: me.close
			});
		}

	},

	destroy: function() {

		this._processing(true,"delete");
		if (confirm("Are you sure you want to DELETE this Count from database?")) {
			this.model.destroy({success: this.close});
		}
	},

	onNew: function(startDate, collection) {
		this.collection = collection;
		this.onEdit(new HMC.Models.Report({"value_date": startDate}));
	},

	onEdit: function(model) {
		this.model = model;
		this.render();
	}
});
