<?php
/**
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
 * Created by PhpStorm.
 * User: astaniscia
 * Date: 22/08/16
 * Time: 08:49
 */
include_once('templates.inc.php');
?>
<div class="wrap">


	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Entrate</h2>
			<p>Si annota qui la data, la voce e l'importo delle entrate relative al mese</p>
			<div id="hmc_table_entrate_fisse"></div>
			<p></p>
		</div>
	</div>


	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Spese fisse</h2>
			<p>Si annota qui tutte le spese che ricorrono regolarmente oppure l'importo di tutti i pagamenti che si deve
				eseguire durante il mese</p>
			<div id="hmc_table_uscite_fisse"></div>
			<p></p>
		</div>
	</div>


	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Obbiettivi</h2>
			<p>Quanto vuoi risparmiare queste mese?</p>
			<!-- div class="hmc_stats">
				<lu>
					<li>
						<canvas id="mouth_stat" style="border-left: 1px solid gray"></canvas>
					</li>
				</lu>
			</div -->
			<div id='hmc_budget'>
					<span class="alignright" style="margin-bottom: 10px;">
						<a class="hmc-last-mount-report "><span class="dashicons dashicons-arrow-left-alt2"
						                                        style="margin-top: 3px;"> </span></a>
						<a class="hmc-this-mount-report "><span class="dashicons dashicons-admin-home"></span><span
								id="stat_range"></span></a>
						<a class="hmc-next-mount-report "><span class="dashicons dashicons-arrow-right-alt2"
						                                        style="margin-top: 3px;"> </span></a>
					</span>
				<!-- p> Entrate <span id="previsione_entrate">10</span> -
					( Previsione Risparmio <span id="previsione_risparmio"> 3 </span> +
					 Uscite previste <span id="previsione_risparmio"> 5 </span> ) =
					Max Spese inpreviste <span id="previsione_risparmio"> 2 </span>
				</p -->


				<canvas id="mouth_budget"></canvas>

				<div id="budget_type_0"></div>
				<div id="budget_type_1"></div>
				<div id="budget_type_2"></div>
				<div id="budget_type_3"></div>
			</div>
			<p></p>
		</div>
	</div>


	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Spese Variabili</h2>
			<p> Osserva l'andamento delle tue spese variabiliper questo mese</p>
			<div class="hmc_stats">
				<lu>
					<li>
						<canvas id="cat_type_0"></canvas>
					</li>
					<li>
						<canvas id="cat_type_1"></canvas>
					</li>
					<li>
						<canvas id="cat_type_2"></canvas>
					</li>
					<li>
						<canvas id="cat_type_3"></canvas>
					</li>
				</lu>
			</div>
			<p>Qui puoi osservare ed aggiungere le tue registrazioni organizzate per settimana o per mese. E'
				possibile aggiungere una nuova voce di spesa facendo click sulla colonna desiderata</p>

			<div id='hmc_calendar'>
				<p><a class="hmc-add-report button-primary">Aggiungi</a></p>
			</div>

		</div>
	</div>

	<!-- The Modal -->
	<div id='report_dialog' class='hmc-modal'>

		<!-- Modal content -->
		<div class="hmc-modal-content">

			<div class="hmc-modal-header">
				<h2 class="hmc-modal-title">Modal Header</h2>
			</div>

			<div class="hmc-modal-body">
				<table class="form-table">
					<tr>
						<td>
							<input id="hmc_value" class="large-text" type="number" value="10.00" step="0.5"
							       data-number-to-fixed="2" data-number-stepfactor="100"/>
							<p class="description">Ammount of Value</p>

						</td>
					</tr>
					<tr>
						<td>
							<textarea id="hmc_description" cols="80" rows="5" class="large-text"></textarea>
							<p class="description">Note</p>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" id="hmc_count" class="large-text">
							<input type="hidden" id="hmc_count_id">
							<p class="description" id="hmc_count_description">Type of actions</p>
						</td>
					</tr>
					<tr>
						<td>
							<!-- <input class="large-text" type="text" name="dates" value="<%- dates %>"/> -->
							<input type="hidden" id="hmc_value_date">
						</td>
					</tr>
				</table>
				<span id="hmc-processing"><div class="spinner is-active"></div>Processing</span>
				<!-- div id="hmc_count_selector"></div-->


				<div id="acc_0">
					<button id="acc_label_0" class="accordion">Section 0</button>
					<div id="accordion_0" class="panel"></div>
				</div>
				<div id="acc_1">
					<button id="acc_label_1" class="accordion">Section 1</button>
					<div id="accordion_1" class="panel"></div>

				</div>
				<div id="acc_2">
					<button id="acc_label_2" class="accordion">Section 2</button>
					<div id="accordion_2" class="panel"></div>

				</div>
				<div id="acc_3">
					<button id="acc_label_3" class="accordion">Section 3</button>
					<div id="accordion_3" class="panel"></div>

				</div>
				<div id="acc_4">
					<button id="acc_label_4" class="accordion">Section 4</button>
					<div id="accordion_4" class="panel"></div>

				</div>
				<div id="acc_5">
					<button id="acc_label_5" class="accordion">Section 5</button>
					<div id="accordion_5" class="panel"></div>


					<script>
						var acc = document.getElementsByClassName("accordion");
						var i;

						for (i = 0; i < acc.length; i++) {
							acc[i].onclick = function() {
								this.classList.toggle("active");
								this.nextElementSibling.classList.toggle("show");
							}
						}
					</script>

				</div>

				<div class="hmc-modal-footer">
					<a class="button-primary" id="hmc-report-ok">Apply</a>
					<a class="button-secondary" id="hmc-report-delete">Delete</a>
					<a class="button-secondary hmc-report-cancel">Cancel</a>
				</div>
			</div>
		</div>



		<script>

			jQuery(document).ready(function($) {
				//var countModels = new HMC.Models.Reports();

				//var reportEditor = new HMC.Views.ReportView();

				var page = new HMC.Views.Calendar().render();

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

				/*var sum = new HMC.Views.SummPieChart({
				 id: 'mouth_stat',
				 name: 'statistic'
				 });*/

				var table1 = new HMC.Views.ReportTable({el: $('#hmc_table_uscite_fisse').get(0), category: "5"});

				var table2 = new HMC.Views.ReportTable({el: $('#hmc_table_entrate_fisse').get(0), category: "4"});

				var budget = new HMC.Views.BudgetView({el: $('#hmc_budget')});
			});
		</script>
