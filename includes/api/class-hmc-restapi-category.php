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


if ( ! class_exists( 'HMC_RestAPI_Category' ) ) {

	/**
	 * Class HMC_RestAPI_Category
	 */
	class HMC_RestAPI_Category extends WP_REST_Controller {


		/**
		 * Server object
		 *
		 * @var HMC_Category $category_handler
		 */
		protected $category_handler;


		/**
		 * HMC_RestAPI_Category constructor.
		 *
		 * @param HMC_Category $category The category.
		 */
		public function __construct( HMC_Category $category ) {
			$this->category_handler = $category;
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Register the routes for the objects of the controller.
		 */
		public function register_routes() {
			$version   = '1';
			$namespace = 'hmc/v' . $version;
			$base      = 'voices';
			register_rest_route( $namespace, '/' . $base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( true ),
				),
			) );

			register_rest_route( $namespace, '/' . $base . '/(?P<id>[\w|-]+)', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => array(
							'default' => 'view',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( false ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default' => false,
						),
					),
				),
			) );


			register_rest_route( $namespace, '/' . $base . '-types', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item_types' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(),
				),
			) );
			register_rest_route( $namespace, '/' . $base . '-types/(?P<id>[\w|-]+)', array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item_type' ),
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
					),
				)
			);
			register_rest_route( $namespace, '/' . $base . '/schema', array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_public_item_schema' ),
			) );
		}


		/**
		 * Get all possible type of Item
		 *
		 * @param WP_REST_Request $request The request.
		 *
		 * @return WP_REST_Response|WP_REST_Response
		 */
		public function get_item_types( $request ) {
			try {
				return new WP_REST_Response( HMC_Voice_Type::getAll() );
			} catch ( Exception $e ) {
				return new WP_Error( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage(), __( 'message', 'text-domain' ) );
			}
		}

		/**
		 * The type description
		 *
		 * @param WP_REST_Request $request The request.
		 *
		 * @return WP_REST_Response|WP_REST_Response
		 */
		public function get_item_type( $request ) {
			try {
				$id = $request->get_params()['id'];

				return new WP_REST_Response( HMC_Voice_Type::get( $id ) );
			} catch ( Exception $e ) {
				return new WP_Error( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage(), __( 'message', 'text-domain' ) );
			}
		}

		/**
		 * Get a collection of items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_items( $request ) {
			$items = $this->category_handler->getVoices( null,'name', 'asc', $request->get_param( 'term' ) );
			$data  = array();
			foreach ( $items as $item ) {
				$itemdata = $this->prepare_item_for_response( $item, $request );
				$data[]   = $this->prepare_response_for_collection( $itemdata );
			}

			return new WP_REST_Response( $data, 200 );
		}

		/**
		 * Get one item from the collection
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Response
		 */
		public function get_item( $request ) {

			// Get parameters from request.
			$params = $request->get_params();
			try {
				$item = $this->category_handler->getVoice( $params['id'] );
				$data = $this->prepare_item_for_response( $item, $request );

				// Return a response or error based on some conditional.
				return new WP_REST_Response( $data, 200 );
			} catch ( Exception $e ) {
				return new WP_Error( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage(), __( 'message', 'text-domain' ) );
			}
		}

		/**
		 * Create one item from the collection
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Request
		 */
		public function create_item( $request ) {

			try {
				$item = $this->prepare_item_for_database( $request, true );

				if ( ! is_wp_error( $item ) ) {

					$data     = $this->category_handler->updateVoice( $item );
					$response = new WP_REST_Response( $data );
					$response->set_status( 201 );

					// $response->header('Location', json_url('/hmc/voices/' . $data));
					return $response;
				} else {

					return $item;
				}
			} catch ( Exception $e ) {
				return new WP_Error( 'cant-create: ' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage() ), __( 'message', 'text-domain' ) );

			}

		}

		/**
		 * Update one item from the collection
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Request
		 */
		public function update_item( $request ) {

			try {
				$item = $this->prepare_item_for_database( $request );

				if ( ! is_wp_error( $item ) ) {

					$data     = $this->category_handler->updateVoice( $item );
					$response = new WP_REST_Response( $data );
					$response->set_status( 200 );

					// $response->header('Location', json_url('/hmc/voices/' . $data));
					return $response;
				} else {

					return $item;
				}
			} catch ( Exception $e ) {
				return new WP_Error( 'cant-update: ' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage() ), __( 'message', 'text-domain' ) );

			}

		}

		/**
		 * Delete one item from the collection
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|WP_REST_Request
		 */
		public function delete_item( $request ) {
			try {
				$id = $request->get_param( 'id' );
				$result = $this->category_handler->removeVoice( $id );

				if ( $result ) {
					return new WP_REST_Response( true, 200 );
				} else {
					throw new Exception( 'Not Deleted!' );
				}
			} catch ( Exception $e ) {
				// Return new WP_Error("c-4", "Error on delete: " . $e->getMessage(), $e);
				return new WP_Error( 'cant-delete' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage() ), __( 'message', 'text-domain' ), array( 'status' => 500 ) );
			}

		}

		/**
		 * Check if a given request has access to get items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function get_items_permissions_check( $request ) {
			return true;
			return current_user_can( 'read' );
		}

		/**
		 * Check if a given request has access to get a specific item
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function get_item_permissions_check( $request ) {
			return $this->get_items_permissions_check( $request );
		}

		/**
		 * Check if a given request has access to create items
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function create_item_permissions_check( $request ) {
			return true;
			return current_user_can( 'edit_posts' );
		}

		/**
		 * Check if a given request has access to update a specific item
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function update_item_permissions_check( $request ) {
			return $this->create_item_permissions_check( $request );
		}

		/**
		 * Check if a given request has access to delete a specific item
		 *
		 * @param WP_REST_Request $request Full data about the request.
		 *
		 * @return WP_Error|bool
		 */
		public function delete_item_permissions_check( $request ) {
			return $this->create_item_permissions_check( $request );
		}

		/**
		 * Prepare the item for create or update operation
		 *
		 * @param WP_REST_Request $request      Request object.
		 * @param bool            $is_creation  Specify if it's a new elements.
		 *
		 * @return WP_Error|object $prepared_item
		 */
		protected function prepare_item_for_database( $request, $is_creation = false ) {

			try {
				$data = json_decode( $request->get_body(), true );

				if ( ! $is_creation ) {
					$id = HMC_UTILS::check_array_value( 'id', $data, true );
				} else {
					$id = null;
				}

				$name = HMC_UTILS::check_array_value( 'name', $data, true );

				$description = HMC_UTILS::check_array_value( 'description', $data, false );

				$type = HMC_UTILS::check_array_value( 'type', $data, false );

				return HMC_Voice::make_it( $id, $name, $description, $type );

			} catch ( Exception $e ) {
				return new WP_Error( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' . $e : $e->getMessage(), __( 'message', 'text-domain' ) );
			}
		}

		/**
		 * Prepare the item for the REST response
		 *
		 * @param mixed           $item     WordPress representation of the item.
		 * @param WP_REST_Request $request  Request object.
		 *
		 * @return mixed
		 */
		public function prepare_item_for_response( $item, $request ) {
			return $item->toArray();
		}

		/**
		 * Get the query params for collections
		 *
		 * @return array
		 */
		public function get_collection_params() {
			return array(
				'page'     => array(
					'description'       => 'Current page of the collection.',
					'type'              => 'integer',
					'default'           => 1,
					'sanitize_callback' => 'absint',
				),
				'per_page' => array(
					'description'       => 'Maximum number of items to be returned in result set.',
					'type'              => 'integer',
					'default'           => 10,
					'sanitize_callback' => 'absint',
				),
				'search'   => array(
					'description'       => 'Limit results to those matching a string.',
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
				),
			);
		}
	}

}