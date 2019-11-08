<?php

final class Algolia_Usermeta_Index extends Algolia_Index {

	/**
	 * @var string
	 */
	protected $contains_only = 'usermeta';

	/**
	 * @return string The name displayed in the admin UI.
	 */
	public function get_admin_name() {
		return __( 'Usermeta' );
	}

	protected function get_all_usermeta( $args ) {
		// $args = array(
		// 	'order'   => 'ASC',
		// 	'orderby' => 'umeta_id',
		// 	'offset'  => $offset,
		// 	'number'  => $batch_size,
		// );

		global $wpdb;
		$result = $wpdb->get_results(
			"
			SELECT * FROM {$wpdb->usermeta}
			ORDER BY {$args->orderby} {$args->order}
			OFFSET {$args->offset}
			LIMIT {$args->number}
			",
			ARRAY_N
		);
		return $result;
	}

	protected function count_all_usermeta() {
		global $wpdb;
		$result = $wpdb->get_row(
			"
			SELECT umeta_id, COUNT(umeta_id) as c
			FROM {$wpdb->usermeta}",
			ARRAY_N
		);
		return $result->c;
	}

	/**
	 * @param $item
	 *
	 * @return bool
	 */
	protected function should_index( $item ) {
		$should_index = (int) count(get_user_meta( $item->umeta_id )) > 0;
		return (bool) apply_filters( 'algolia_should_index_usermeta', $should_index, $item );
	}

	/**
	 * @param $item
	 *
	 * @return array
	 */
	protected function get_records( $item ) {
		$record                 = array();
		$record['objectID']     = $item->umeta_id;
		$record['user_id']      = $item->user_id;
		$record['meta_key']      = $item->meta_key;
		$record['meta_value']      = $item->meta_value;

		$record = (array) apply_filters( 'algolia_usermeta_record', $record, $item );

		return array( $record );
	}

	/**
	 * @return int
	 */
	protected function get_re_index_items_count() {
		$usermeta_count = count_all_usermeta();
		return (int) $usermeta_count;
	}

	/**
	 * @return array
	 */

	 # TODO
	protected function get_settings() {
		$settings = array(
			'attributesToIndex' => array(
				'unordered(user_id)',
			),
			'customRanking'     => array(
				'desc(user_id)',
			),
		);

		return (array) apply_filters( 'algolia_usermeta_index_settings', $settings );
	}

	/**
	 * @return array
	 */
	protected function get_synonyms() {
		return (array) apply_filters( 'algolia_usermeta_index_synonyms', array() );
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return 'usersmeta';
	}


	/**
	 * @param int $page
	 * @param int $batch_size
	 *
	 * @return array
	 */
	protected function get_items( $page, $batch_size ) {
		$offset = $batch_size * ( $page - 1 );

		$args = array(
			'order'   => 'ASC',
			'orderby' => 'umeta_id',
			'offset'  => $offset,
			'number'  => $batch_size,
		);

		// We use prior to 4.5 syntax for BC purposes, no `paged` arg.
		return get_all_usermeta( $args );
	}

	/**
	 * A performing function that return true if the item can potentially
	 * be subject for indexation or not. This will be used to determine if an item is part of the index
	 * As this function will be called synchronously during other operations,
	 * it has to be as lightweight as possible. No db calls or huge loops.
	 *
	 * @param mixed $item
	 *
	 * @return bool
	 */
	public function supports( $item ) {
		return $item instanceof WP_User;
	}

	public function get_default_autocomplete_config() {
		$config = array(
			'position'        => 30,
			'max_suggestions' => 3,
			'tmpl_suggestion' => 'autocomplete-usermeta-suggestion',
		);

		return array_merge( parent::get_default_autocomplete_config(), $config );
	}

	/**
	 * @param mixed $item
	 */
	public function delete_item( $item ) {
		$this->assert_is_supported( $item );
		$this->get_index()->deleteObject( $item->umeta_id );
	}
}
