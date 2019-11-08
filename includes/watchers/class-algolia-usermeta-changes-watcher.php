<?php

use AlgoliaSearch\AlgoliaException;

class Algolia_Usermeta_Changes_Watcher implements Algolia_Changes_Watcher {

	/**
	 * @var Algolia_Index
	 */
	private $index;

	/**
	 * @param Algolia_Index $index
	 */
	public function __construct( Algolia_Index $index ) {
		$this->index = $index;
	}

	protected function get_usermeta_by_umetaid( int $umeta_id ) {
		global $wpdb;
		return $wpdb->get_row(
			"SELECT * FROM {$wpdb->usermeta} WHERE umeta_id = {$umeta_id}",
			ARRAY_N
		);
	}

	public function watch() {
		// Fires immediately after an existing user is updated.
		add_action( 'profile_update', array( $this, 'sync_item' ) );

		// Fires immediately after a new user is registered.
		add_action( 'user_register', array( $this, 'sync_item' ) );

		// Fires immediately before a user is deleted.
		add_action( 'delete_user', array( $this, 'delete_item' ) );
	}

	/**
	 * @param $umeta_id
	 */
	public function sync_item( $umeta_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$usermeta = get_usermeta_by_umetaid( $umeta_id );

		if ( ! $usermeta || ! $this->index->supports( $usermeta ) ) {
			return;
		}

		try {
			$this->index->sync( $usermeta );
		} catch ( AlgoliaException $exception ) {
			error_log( $exception->getMessage() );
		}
	}

	/**
	 * @param int $umeta_id
	 */
	public function delete_item( $umeta_id ) {
		$usermeta = get_usermeta_by_umetaid( $umeta_id );

		if ( ! $usermeta || ! $this->index->supports( $usermeta ) ) {
			return;
		}

		try {
			$this->index->delete_item( $usermeta );
		} catch ( AlgoliaException $exception ) {
			error_log( $exception->getMessage() );
		}
	}
}
