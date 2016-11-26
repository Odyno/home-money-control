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
 * Time: 11:47
 */

$iHMC_Stat =  new HMC_Statistics();

function hmc_format_me($iArray){
	$iData=array();
	$iLabel=array();
	$summ=0;
	foreach ($iArray as $iElem){
		array_push($iData,$iElem['sum']);
		array_push($iLabel,$iElem['date']);
		$summ += $iElem['sum'];
	}
	return array("data" => $iData, "label" => $iLabel, "sum" => $summ );
}

//Recuperare le entrate dalla storia
$entrate_data_row=$iHMC_Stat->get_mounth_summary(array(4));
$iEntrate=hmc_format_me($entrate_data_row);
$uscite_data_row=$iHMC_Stat->get_mounth_summary(array(5));
$iUscite= hmc_format_me($uscite_data_row);

//crea array dati per chart A
$iSpese= hmc_format_me($iHMC_Stat->get_mounth_summary(array(0,1,2,3)));
$iBudget= hmc_format_me($iHMC_Stat->get_mounth_summary(array(6)));

$iSpese_0= hmc_format_me($iHMC_Stat->get_mounth_summary(array(0)));
$iSpese_1= hmc_format_me($iHMC_Stat->get_mounth_summary(array(1)));
$iSpese_2= hmc_format_me($iHMC_Stat->get_mounth_summary(array(2)));
$iSpese_3= hmc_format_me($iHMC_Stat->get_mounth_summary(array(3)));




?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<header>
		<span class="align-right"><a class="button dashicons-before dashicons-tag "
		                             href="<?php echo admin_url( '/admin.php?page=HMC-id-menu-reports-list' ); ?>">Reports</a> <a
				class="button dashicons-before dashicons-category "
				href="<?php echo admin_url( '/admin.php?page=HMC-id-menu-counts' ); ?>">Counts</a> </span>
	</header>

	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h3>Entrate vs Uscite Fisse</h3>
			<canvas id="lineEntrateUscite"></canvas>
		</div>
	</div>

	<div class="welcome-panel">
		<div class="welcome-panel-content">

			<h3>Budget vs Spese Mensili</h3>
			<canvas id="lineBudgetSpese"></canvas>
		</div>
	</div>

	<div class="welcome-panel">
		<div class="welcome-panel-content">
			<h3>Uscite Per Categorie</h3>
			<!-- <table class="widefat">
				<thead>
				<tr>
					<th class="row-title">A</th>
					<th>B</th>
				</tr>
				</thead>
				<tbody>

				<tr>
					<td>A</td>
					<td>B</td>
				</tr>
				<tr>
					<td>A</td>
					<td>B</td>
				</tr>
				<tr>
					<td>A</td>
					<td>B</td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<th class="row-title">CC</th>
					<th>CC</th>
				</tr>
				</tfoot>
			</table> -->

			<canvas id="pieDistribuzioneSpese"></canvas>
		</div>
	</div>


	<script>
		var ctx = document.getElementById('lineEntrateUscite').getContext('2d');

		var dataEntrateUscite = {
			labels: [ <?php echo '"'.implode("\",\"", array_unique(array_merge($iEntrate['label'],$iUscite['label']), SORT_REGULAR)).'"'; ?> ],
			datasets: [
				{
					label: "Entrata",
					fill: false,
					lineTension: 0.1,
					backgroundColor: "rgba(75, 192, 192, 1)",
					borderColor: "rgba(75, 192, 192, 1)",
					borderCapStyle: 'butt',
					borderDash: [],
					borderDashOffset: 0.0,
					borderJoinStyle: 'miter',
					pointBorderColor: "rgba(75, 192, 192, 1)",
					pointBackgroundColor: "#fff",
					pointBorderWidth: 1,
					pointHoverRadius: 5,
					pointHoverBackgroundColor: "rgba(75, 192, 192, 1)",
					pointHoverBorderColor: "rgba(75, 192, 192, 1)",
					pointHoverBorderWidth: 2,
					pointRadius: 1,
					pointHitRadius: 10,
					data: [<?php echo implode(",", $iEntrate['data']); ?> ],
					spanGaps: false
				},
				{
					label: "Uscite",
					fill: false,
					lineTension: 0.1,
					backgroundColor: "rgba(255,0,0,1)",
					borderColor: "rgba(255,99,132,1)",
					borderCapStyle: 'butt',
					borderDash: [],
					borderDashOffset: 0.0,
					borderJoinStyle: 'miter',
					pointBorderColor: "rgba(255,0,0,1)",
					pointBackgroundColor: "#fff",
					pointBorderWidth: 1,
					pointHoverRadius: 5,
					pointHoverBorderWidth: 2,
					pointRadius: 1,
					pointHitRadius: 10,
					data: [<?php echo implode(",", $iUscite['data']); ?> ],
					spanGaps: false
				}
			]
		};
		var lineEntrateUscite = new Chart(ctx, {
			type: 'line',
			data: dataEntrateUscite
		});


		//** -------------- */

		var ctx = document.getElementById('lineBudgetSpese').getContext('2d');
		var dataBudgetSpese = {
			labels: [ <?php echo '"'.implode("\",\"", array_unique(array_merge($iBudget['label'],$iSpese['label']), SORT_REGULAR)).'"'; ?>],
			datasets: [
				{
					label: "Budget",
					fill: false,
					lineTension: 0.1,
					backgroundColor: "rgba(255, 206, 86, 1)",
					borderColor: "rgba(255, 206, 86, 1)",
					borderCapStyle: 'butt',
					borderDash: [],
					borderDashOffset: 0.0,
					borderJoinStyle: 'miter',
					pointBorderColor: "rgba(255, 206, 86, 1)",
					pointBackgroundColor: "#fff",
					pointBorderWidth: 1,
					pointHoverRadius: 5,
					pointHoverBackgroundColor: "rgba(255, 206, 86, 1)",
					pointHoverBorderColor: "rgba(255, 206, 86, 1)",
					pointHoverBorderWidth: 2,
					pointRadius: 1,
					pointHitRadius: 10,
					data: [<?php echo implode(",", $iBudget['data']); ?>],
					spanGaps: false
				},
				{
					label: "Spese",
					fill: false,
					lineTension: 0.1,
					backgroundColor: "rgba(255,0,0,1)",
					borderColor: "rgba(255,99,132,1)",
					borderCapStyle: 'butt',
					borderDash: [],
					borderDashOffset: 0.0,
					borderJoinStyle: 'miter',
					pointBorderColor: "rgba(255,0,0,1)",
					pointBackgroundColor: "#fff",
					pointBorderWidth: 1,
					pointHoverRadius: 5,
					pointHoverBorderWidth: 2,
					pointRadius: 1,
					pointHitRadius: 10,
					data: [<?php echo implode(",", $iSpese['data']); ?>],
					spanGaps: false
				}
			]
		};
		var lineBudgetSpese = new Chart(ctx, {
			type: 'line',
			data: dataBudgetSpese
		});

	</script>


	<script>
		var ctx = document.getElementById('pieDistribuzioneSpese').getContext('2d');
		var dataPieDistribuzioneSpese = {
			datasets: [{
				data: [
					<?php echo ($iSpese["sum"] / 100) * $iSpese_0["sum"] ?>,
					<?php echo ($iSpese["sum"] / 100) * $iSpese_1["sum"] ?>,
					<?php echo ($iSpese["sum"] / 100) * $iSpese_2["sum"] ?>,
					<?php echo ($iSpese["sum"] / 100) * $iSpese_3["sum"] ?>
				],
				backgroundColor: [
					<?php echo "'".HMC_Voice_Type::toColor(0)."'" ?>,
					<?php echo "'".HMC_Voice_Type::toColor(1)."'" ?>,
					<?php echo "'".HMC_Voice_Type::toColor(2)."'" ?>,
					<?php echo "'".HMC_Voice_Type::toColor(3)."'" ?>
				],
				label: 'My dataset' // for legend
			}],
			labels: [
				<?php echo "'".HMC_Voice_Type::toString(0)."'" ?>,
				<?php echo "'".HMC_Voice_Type::toString(1)."'" ?>,
				<?php echo "'".HMC_Voice_Type::toString(2)."'" ?>,
				<?php echo "'".HMC_Voice_Type::toString(3)."'" ?>
			]
		};
		// For a pie chart
		var pieDistribuzioneSpese = new Chart(ctx, {
			type: 'pie',
			data: dataPieDistribuzioneSpese
		});
	</script>


</div>