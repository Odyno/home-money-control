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


$result="";

if ( ! empty($_GET["action"]) && $_GET["action"] == "show" ){
	$result_rst=HMC_Category::GET_DB();
	foreach ($result_rst as $item){
		$result .= PHP_EOL . implode(";",$item);
	}
}


if ( ! empty($_GET["action"]) && $_GET["action"] == "upload" ){
	$data=$_POST["csv"];
    $line=explode(PHP_EOL,$data);
	HMC_Category::FILL_DB_LINE($line);
}
if ( ! empty($_GET["action"]) && $_GET["action"] == "new" ){
	HMC_Category::FILL_DB();
}

?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>


	<a href="?page=HMC&action=show"> View It </a>
	<textarea name="export" rows="20"  class="large-text"><?php echo $result; ?></textarea>
	 
	
	
	<form action="?page=HMC&action=upload" method="post" >
		<textarea name="csv" rows="20"  class="large-text"></textarea>
		<input type="submit" value="Upload">
	</form>

</div>


<?php




