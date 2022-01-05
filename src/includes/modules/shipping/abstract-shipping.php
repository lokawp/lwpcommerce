<?php

namespace LokusWP\Commerce\Shipping;

// Checking Test Env and Direct Access File
if ( ! defined( 'WPTEST' ) ) {
	defined( 'ABSPATH' ) or die( "Direct access to files is prohibited" );
}

use LokusWP\Utils\Log;

abstract class Gateway {
	/**
	 * Shipping ID
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * Shipping Name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * Shipping Description
	 *
	 * @var string
	 */
	protected $description = null;

	/**
	 * Shipping Logo
	 *
	 * @var url
	 */
	public $logo = null;

	/**
	 * Shipping Group
	 *
	 * @var string
	 */
	protected $group = null;

	/**
	 * Shipping Group
	 *
	 * @var string
	 */
	protected $group_name = null;

	/**
	 * Shipping Documtenation
	 *
	 * @var array
	 */
	protected $docs_url = null;

	/**
	 * Shipping Template
	 *
	 * @var string
	 */
	protected $template = null;

	/**
	 * Shipping Country
	 *
	 * @var string
	 */
	protected $country = 'WW';

	/**
	 * Instruction
	 *
	 * @var string
	 */
	protected $instruction = "";

	/**
	 * Shipping Zone
	 *
	 * @var array
	 */
	protected $zone = [];

	/**
	 * Shipping Package
	 *
	 * @var array
	 */
	protected $package = [];

	/**
	 * Shipping Zone
	 *
	 * @var string
	 */
	protected $type = "";

	/**
	 * Shipping cost
	 *
	 * @var int
	 */
	protected $cost = 0;

	/**
	 * Api Key  Raja Ongkir
	 */
	protected $api_key = '80aa49704fc30a939124a831882dea72'; // ASdgaisgd

	/**
	 * Estimation date
	 */
	protected $estimation_date = null;

	/**
	 * Payment Service
	 */
	public $service = '';

	/**
	 * Store location base on Raja Ongkir ID
	 */
	public $origin = '';

	/**
	 * Destination shipping location base on Raja Ongkir ID
	 */
	public $destination = '';

	/**
	 * Weight in gram
	 */
	public $weight = 0;

	public function save_as_data(): bool {
		$data                  = array();
		$data['id']            = $this->id;
		$data['name']          = $this->name;
		$data['logo']          = $this->logo;
		$data['desc']          = $this->description;
		$data['group']         = $this->group;
		$data['zone']          = $this->zone;
		$data['package']       = $this->package;
		$data['type']          = $this->type;
		$data['data']          = [];
		$data['cost']          = $this->cost;
		$data['instruction']   = $this->instruction;
		$data['payment_class'] = get_class( $this );


		if ( empty( lwp_get_option( $this->id ) ) ) {
			lwp_update_option( $this->id, $data );
		}
		$this->save_to_shipping_active();
		Log::Info( "shipping gateway " . $this->id . " created and activated" );

		return true;
	}

	/**
	 * Saving All Shipping Gateway to Shipping Active List
	 *
	 * @since 0.5.0
	 */
	private function save_to_shipping_active(): void {
		$shipping_active = lwp_get_option( "shipping_active" );
		$shipping_list   = empty( $shipping_active ) ? array() : $shipping_active;


		if ( ! in_array( $this->id, $shipping_list ) ) {
			$shipping_list[] = $this->id;
			lwp_update_option( "shipping_active", $shipping_list );
		}

	}

	public function reset_data(): bool {
		return lwp_delete_option( $this->id );;
	}

	/**
	 * Get Shipping ID
	 *
	 * @return float|int
	 */
	public function get_ID() {
		return abs( $this->id );
	}

	/**
	 * Get Description Settings
	 *
	 * @return void
	 */
	public function get_description() {
		return esc_attr( $this->description );
	}

	/**
	 * Get Shipping Status based on Shipping Active Data
	 *
	 * @param  string|null  $shipping_id
	 *
	 * @return string
	 */
	public function get_status( string $shipping_id = null ): string {
		$shipping_active = lwp_get_option( "shipping_active" );
		$shipping_list   = empty( $shipping_active ) ? array() : $shipping_active;

		$status = "off";
		if ( in_array( $this->id, $shipping_list ) ) {
			$status = "on";
		}

		return $status;
	}

	/**
	 * Get Shipping Cost
	 */
	public function get_cost() {
		$this->set_cost();

		return $this->cost;
	}

	/**
	 * Set Shipping Cost
	 *
	 * @return void
	 */
	public function set_cost() {
		// get destination from cache
		$destination_cost = get_transient( $this->id . '_cost' );
		$this->cost       = $destination_cost["{$this->origin}_to_{$this->destination}_with_{$this->service}"] ?? false;

		if ( ! $this->cost ) {
			$header = [
				'content-type' => 'application/json',
				'key'          => $this->api_key,
			];

			$body = [
				'origin'      => lwpc_get_settings( 'store', 'district', 'intval' ),
				'destination' => $this->destination,
				'weight'      => $this->weight,
				'courier'     => $this->id,
			];

			$options = [
				'body'    => wp_json_encode( $body ),
				'headers' => $header,
			];

			$request  = wp_remote_post( 'https://api.rajaongkir.com/starter/cost', $options );
			$response = json_decode( wp_remote_retrieve_body( $request ) );
			$costs    = $response->rajaongkir->results[0]->costs;
			$index    = array_search( $this->service, array_column( $costs, 'service' ) );

			$cost = $costs[ $index ]->cost[0]->value;

			// Push new destination to cache
			$destination_cost["{$this->origin}_to_{$this->destination}_with_{$this->service}"] = $cost;

			set_transient( $this->id . '_cost', $destination_cost, DAY_IN_SECONDS );

			$this->cost = $cost;
		}
	}

	/**
	 * Manage Shipping Settings
	 * used for settings shipping methods
	 *
	 * @return void
	 */
	abstract public function admin_manage( string $shipping_id );
}