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
?>
<div class="wrap">
	<!-- <h2><?php echo esc_html( get_admin_page_title() ); ?></h2> -->

	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Statistiche</h2>
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
							<li>
								<canvas id="mouth_stat" style="border-left: 1px solid gray"></canvas>
							</li>
						</lu>
					</div>
		</div>
	</div>



	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Entrate fisse</h2>
			<p>Qui puoi osservare le tue entrate previste</p>
			<!-- The empty table we'll use as the example -->
			<div id="hmc_table_entrate_fisse" ></div>
		<p></p>
		</div>
	</div>


	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Spese fisse</h2>
			<p>Qui puoi osservare le tue spese previste</p>
			<!-- The empty table we'll use as the example -->
			<div id="hmc_table_uscite_fisse" ></div>
			<p></p>
		</div>
	</div>

	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h2>Spese Variabili</h2>
			<p>Qui puoi osservare ed aggiungere le tue registrazioni organizzate per settimana o per mese. E'
				possibile aggiungere una nuova voce di spesa facendo click sulla colonna desiderata</p>
			<div id='hmc_calendar'>
				<p><a class="hmc-add-report button-primary" >Aggiungi</a></p>
			</div>
		</div>
	</div>


	<div id='report_dialog' class='dialog ui-helper-hidden'>
		<table>
			<tr>
				<td>
					<input id="hmc_value" class="large-text" type="number" value="10.00" step="0.5"
					       data-number-to-fixed="2" data-number-stepfactor="100"/>
					<p class="description">Ammount of Value</p>

				</td>
			</tr>
			<tr>
				<td>
					<textarea id="hmc_description" cols="80" rows="5" class=" all-options"></textarea>
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
					<input type="hidden" id="hmc_value_date" >
				</td>
			</tr>
		</table>
		<span id="hmc-processing"><div class="spinner is-active"></div>Processing</span>
	</div>

</div>



<script type="text/template" id="transaction-table-template">
	<!-- The empty table we'll use as the example -->
	<table class="widefat">
		<thead>
		<tr>

			<th class="row-title" >Gruppo</th>
			<th class="row-title">Descrizione</th>
			<th class="row-title">Valore</th>
			<th class="row-title">Data Prevista</th>
			<th class="row-title">Commands</th>
		</tr>
		</thead>
		<!-- We'll attach the PeopleView to this element -->
		<tbody id="hmc_table_content" >
		<tr><td celspan="5">None!</td></tr>
		</tbody>
		<tfoot>
			<tr >
				<th ></th>
				<th >Totale</th>
				<th ><span id="hmc_table_summ" class="enMoney"></span></th>
				<th ></th>
				<th ><a class="hmc-add-report button-primary" >Aggiungi</a></th>
			</tr>
		</tfoot>
	</table>
</script>