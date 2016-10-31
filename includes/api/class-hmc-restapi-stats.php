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


if ( ! class_exists( 'HMC_RestAPI_Stats' ) ) {

	/**
	 * Class HMC_RestAPI_Transaction
	 */
	class HMC_RestAPI_Stats extends WP_REST_Controller {


		/**
		 * Object
		 *
		 * @var HMC_Statistics $stats_handler
		 */
		protected $stats_handler;



		/**
		 * HMC_RestAPI_Transaction constructor.
		 *
		 * @param HMC_Statistics $stat The Statisitc.
		 */
		public function __construct( HMC_Statistics $stat ) {
			$this->stats_handler    = $stat;
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Register the routes for the objects of the controller.
		 */
		public function register_routes() {
			$version   = '1';
			$namespace = 'hmc/v' . $version;
			$base      = 'stats';

			register_rest_route( $namespace, '/all' . $base , array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_summary' ),
					'permission_callback' => array( $this, 'get_permissions_check' )
				)
			) );

			register_rest_route( $namespace, '/' . $base . '/(?P<id>[\w|-]+)', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_category_type_summary' ),
					'permission_callback' => array( $this, 'get_permissions_check' )
				)
			) );

		}

		/**
		 * Get a collection of items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_category_type_summary( $request ) {

			if ( isset( $request->get_params()['from']) ){
				$from = $request->get_params()['from'];
			}else{
				$from = date( "Y-m", time() ) . "-01";
			}

			if ( isset($request->get_params()['to']) ){
				$to = $request->get_params()['to'];
			}else{
				$to = date( "Y-m-t", time() );
			}


			$id = $request->get_params()['id'];

			$out=array();
			$out['type']=$id;
			$out['from']=$from;
			$out['to']=$to;
			$out['total']=0;
			$items = $this->stats_handler->get_category_type_summary( $id, $from, $to );
			foreach ( $items as $item){
				$out['total'] += $item['total'];
			}
			$out['count']=count($items);
			$out['items']=$items;

			return new WP_REST_Response( $out, 200 );
		}

		/**
		 * Get a collection of items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_summary( $request ) {

			if ( isset( $request->get_params()['from']) ){
				$from = $request->get_params()['from'];
			}else{
				$from = date( "Y-m", time() ) . "-01";
			}

			if ( isset($request->get_params()['to']) ){
				$to = $request->get_params()['to'];
			}else{
				$to = date( "Y-m-t", time() );
			}


			$out=array();
			$out['from']=$from;
			$out['to']=$to;
			$items = $this->stats_handler->get_summary(  $from, $to );
			$out['count']=count($items);
			$out['items']=$items;

			return new WP_REST_Response( $out, 200 );
		}


		/**
		 * Check if a given request has access to get a specific item
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function get_permissions_check( $request ) {
			return true;
			return current_user_can( 'read' );
		}



	}

}