<?php

namespace Wpp\SmartSlider3RestApi\Endpoints;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use N2Loader;
use N2SmartsliderSlidersModel;
use Wpp\SmartSlider3RestApi\Libraries\N2SmartSliderExportExtended;

class WP_REST_Smart_Slider_3_Controller extends WP_REST_Controller {

    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $version = '1';
        $namespace = 'smartslider3/v' . $version;
        $base = 'sliders';
        register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args' => array(
                    'context' => array(
                        'default' => 'view',
                    ),
                ),
            ),
        ));
        register_rest_route($namespace, '/' . $base . '/schema', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_public_item_schema'),
        ));
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item($request) {
        //get parameters from request
        $params = $request->get_params();

        N2Loader::import(array(
            'models.Sliders',
        ), 'smartslider');

        $slidersModel = new N2SmartsliderSlidersModel();
        $slider       = $slidersModel->get($params['id']);

		if ( empty( $slider ) ) {
			return new WP_Error( 'rest_smartslider3_slider_not_found', __( 'Slider not found.' ), array( 'status' => 404 ) );
		}

		$data = $this->prepare_item_for_response( $slider, $request );

		return rest_ensure_response( $data );
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request) {
        return TRUE;
    }

	/**
	 * Prepares a slider array for serialization.
	 *
	 * @param stdClass        $slider  Slider data from database.
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response Post status data.
	 */
	public function prepare_item_for_response( $slider, $request ) {

		$fields = $this->get_fields_for_response( $request );
		$data   = array();

		if ( in_array( 'id', $fields, true ) ) {
			$data['id'] = $slider['id'];
		}

		if ( in_array( 'alias', $fields, true ) ) {
			$data['alias'] = $slider['alias'];
		}

		if ( in_array( 'title', $fields, true ) ) {
			$data['title'] = $slider['title'];
		}

		if ( in_array( 'type', $fields, true ) ) {
			$data['type'] = $slider['type'];
		}

		if ( in_array( 'time', $fields, true ) ) {
			$data['time'] = mysql_to_rfc3339($slider['time']);
		}

		if ( in_array( 'thumbnail', $fields, true ) ) {
			$data['thumbnail'] = $slider['thumbnail'];
		}

		if ( in_array( 'ordering', $fields, true ) ) {
			$data['ordering'] = $slider['ordering'];
		}

		if ( in_array( 'html', $fields, true ) ) {
            N2Loader::import('libraries.export', 'restapi');
            $export = new N2SmartSliderExportExtended($slider['id']);
            $htmlParts = $export->createHTMLParts();
			$data['html'] = array(
                'head' => $htmlParts['head'],
                'body' => $htmlParts['body'],
            );
		}

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data = $this->add_additional_fields_to_object( $data, $request );
		$data = $this->filter_response_by_context( $data, $context );

		$response = rest_ensure_response( $data );

		/**
		 * Filters a slider returned from the REST API.
		 *
		 * Allows modification of the slider data right before it is returned.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object           $status   The original slider data array.
		 * @param WP_REST_Request  $request  Request used to generate the response.
		 */
		return apply_filters( 'rest_prepare_smartslider3_slider', $response, $slider, $request );
	}

	/**
	 * Retrieves the slider's schema, conforming to JSON Schema.
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'              => 'http://json-schema.org/draft-04/schema#',
			'title'                => 'status',
			'type'                 => 'object',
			'properties'           => array(
				'id'             => array(
					'description'  => __( 'Unique identifier for the slider.' ),
					'type'         => 'integer',
					'context'      => array( 'embed', 'view' ),
					'readonly'     => true,
				),
				'title'             => array(
					'description'  => __( 'The title for the slider.' ),
					'type'         => 'string',
					'context'      => array( 'embed', 'view' ),
				),
				'alias'             => array(
					'description'  => __( 'An alphanumeric identifier for the slider.' ),
					'type'         => 'string',
					'context'      => array( 'embed', 'view' ),
				),
				'type'             => array(
					'description'  => __( 'Type for the slider.' ),
					'type'         => 'string',
					'context'      => array( 'embed', 'view' ),
				),
				'time'        => array(
					'description' => __( "The date the slider was created, in the site's timezone." ),
					'type'        => 'string',
					'format'      => 'date-time',
					'context'     => array( 'embed', 'view' ),
					'readonly'    => true,
				),
				'html'        => array(
					'description' => __( "The rendered slider HTML code, split into multiple parts." ),
					'type'        => 'object',
					'context'     => array( 'embed', 'view' ),
					'readonly'    => true,
					'properties'  => array(
						'head'      => array(
							'description' => __( 'Head part of the HTML: CSS & JS.' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view' ),
							'readonly'    => true,
						),
						'body' => array(
							'description' => __( 'Body part: the actual slider HTML.' ),
							'type'        => 'string',
							'context'     => array( 'embed', 'view' ),
							'readonly'    => true,
						),
					),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

}
