<?php

namespace WPcomSpecialProjects\MDAC;

class Admin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @return self
	 */
	public static function init(): self {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Register the hooks of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   void
	 */
	public function define_hooks(): void {

		// Get site post types
		$post_types = get_post_types(
			array(
				'public' => true,
			)
		);

		// Add filter to add custom columns to the post list
		foreach ( $post_types as $post_type ) {
			add_filter( 'manage_edit-' . $post_type . '_columns', array( $this, 'add_date_column' ), 100 );
			add_filter( 'manage_edit-' . $post_type . '_sortable_columns', array( $this, 'add_date_sortable' ), 100 );
		}

		// Add filter to show custom columns in the post list
		add_action( 'manage_pages_custom_column', array( $this, 'print_date_column' ), 100, 2 );
		add_action( 'manage_posts_custom_column', array( $this, 'print_date_column' ), 100, 2 );
	}

	/**
	 * Insert an element before certain array key of an associative array.
	 * If no key is found,the element will be inserted at the end
	 *
	 * @since 1.0.0
	 *
	 * @param array $origin The original array where the element will be inserted
	 * @param mixed $element The element to insert (could be an array)
	 * @param string $key The key that will be searched to insert the element before.
	 * @return array
	 */
	public function insert_element_before( $origin, $element, $key ): array {

		# Get key position
		$keys  = array_keys( $origin );
		$index = array_search( $key, $keys, true );
		$pos   = false === $index ? count( $origin ) : $index;

		return array_merge( array_slice( $origin, 0, $pos ), $element, array_slice( $origin, $pos ) );
	}

	/**
	 * Add date column to post list
	 *
	 * @param $columns
	 *
	 * @return array
	 * @see    manage_edit-{$post_type}_columns
	 * @access public
	 * @since  1.0.0
	 */
	public function add_date_column( $columns ): array {

		// Get date key
		$date_key = isset( $columns['date'] ) ? 'date' : 'order_date';

		// Add new date columns
		$date_columns = array(
			'create_date' => __( 'Publication Date', 'wpcomsp-mdac' ),
			'modify_date' => __( 'Modification Date', 'wpcomsp-mdac' ),
		);

		$columns = $this->insert_element_before( $columns, $date_columns, $date_key );

		// Remove current date column
		unset( $columns[ $date_key ] );

		return $columns;
	}

	/**
	 * Add date columns to the sortable array
	 *
	 * @param $columns
	 *
	 * @return array
	 * @see    manage_edit-{$post_type}_sortable_columns
	 * @access public
	 * @since  1.0.0
	 */
	public function add_date_sortable( $columns ): array {

		// Add new date columns
		$date_columns = array(
			'create_date' => array( 'date', true ),
			'modify_date' => array( 'modified', true ),
		);

		$columns = $this->insert_element_before( $columns, $date_columns, 'date' );

		// Remove current date column
		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Print date column
	 *
	 * @since 1.0.0
	 * @see manage_pages_custom_column
	 * @see manage_posts_custom_column
	 * @access public
	 * @return void
	 */
	public function print_date_column( $column, $post_id ): void {
		$date_format       = get_option( 'date_format' );
		$time_format       = get_option( 'time_format' );
		$author_link       = get_the_author_link();
		$the_date          = get_the_date( $date_format, $post_id );
		$the_time          = get_the_date( $time_format, $post_id );
		$the_modified_date = get_the_modified_date( $date_format, $post_id );
		$the_modified_time = get_the_modified_date( $time_format, $post_id );
		$at                = esc_html__( 'at', 'wpcomsp-mdac' );
		$by                = esc_html__( 'by', 'wpcomsp-mdac' );

		if ( 'create_date' === $column ) {
			$this->print_date_column_contents( $the_date, $the_time, $at, $by, $author_link );
		} elseif ( 'modify_date' === $column ) {
			$this->print_date_column_contents( $the_modified_date, $the_modified_time, $at, $by, $author_link );
		}
	}

	/**
	 * Print date column content.
	 *
	 * @param string $date
	 * @param string $time
	 * @param string $at
	 * @param string $by
	 * @param string $author_link
	 * @return void
	 * @since 1.0.0
	 */
	public function print_date_column_contents( $date, $time, $at, $by, $author_link ): void {
		printf(
			'%s %s %s<br /><em>%s %s</em>',
			esc_html( $date ),
			esc_html( $at ),
			esc_html( $time ),
			esc_html( $by ),
			wp_kses(
				$author_link,
				array(
					'a' => array(
						'href'  => array(),
						'title' => array(),
						'rel'   => array(),
					),
				)
			)
		);
	}

}
