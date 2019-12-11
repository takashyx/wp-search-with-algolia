<div class="input-">
	<label>
		<input type="checkbox" value="test1"
			name="chk_test" <?php checked( $value, 'native' ); ?>>
		<?php esc_html_e( 'test title 1', 'wp-search-with-algolia' ); ?>
	</label>
	<div class="checkbox-info">
		<?php
		echo wp_kses(
			__(
				'test 1 description.',
				'wp-search-with-algolia'
			),
			[
				'br' => [],
			]
		);
		?>
	</div>

	<label>
		<input type="checkbox" value="test2"
			name="algolia_override_native_search" <?php checked( $value, 'test2' ); ?>>
		<?php esc_html_e( 'test2', 'wp-search-with-algolia' ); ?>
	</label>
</div>
