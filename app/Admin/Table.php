<?php

namespace AFB\Admin;
use AFB\Helper\I18n;
use AFB\Helper\Input;
use AFB\Model\FeedBacks;

/**
 * Static Table
 *
 * @package AFB\Admin
 * @property-read I18n $i18n
 * @property-read Input $input
 * @property-read FeedBacks $feedbacks
 */
class Table extends \WP_List_Table {


	/**
	 * Post Type object
	 *
	 * @var \stdClass
	 */
	protected $post_type = null;


	/**
	 * Per page
	 *
	 * @var int
	 */
	protected $per_page = 10;

	/**
	 * Constructor
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {

		$this->post_type = get_post_type_object( $args['post_type'] );

		parent::__construct( array(
			'singular' => $this->post_type->name,
			'plural'   => $this->post_type->name,
			'ajax'     => false,
		) );
	}

	/**
	 * Get column name
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'title'     => $this->i18n->_( 'Title' ),
			'positive'  => $this->i18n->_( 'Good' ),
			'ratio'     => '&nbsp;',
			'negative'  => $this->i18n->_( 'Bad' ),
			'published' => $this->i18n->_( 'Published' ),
			'updated'   => $this->i18n->_( 'Updated' ),
		);
		return $columns;
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$columns = array(
			'title'     => array( 'title', false ),
			'positive'  => array( 'positive', false ),
			'negative'  => array( 'negative', false ),
			'published' => array( 'published', false ),
			'updated'   => array( 'updated', false ),
		);
		return $columns;
	}

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		// Set header
		$this->_column_headers = array(
			$this->get_columns(),
			array(), // Column to hide
			$this->get_sortable_columns(),
		);

		$args = array(
			'post_type' => $this->post_type->name,
			'order'     => strtoupper( $this->input->get( 'order' ) ),
			'orderby'   => $this->input->get( 'orderby' ),
			's'         => $this->input->get( 's' ),
		);

		// Get data
		$this->items = $this->feedbacks->search( $args, $this->input->get( 'paged' ) );

		// Total count
		$total_items = $this->feedbacks->total();

		//
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $this->per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $this->per_page ),   //WE have to calculate the total number of pages
		) );
	}

	/**
	 * Show column
	 *
	 * @param \stdClass $item
	 * @param string $column_name
	 *
	 * @return bool|int|mixed|string|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'positive':
			case 'negative':
				return number_format( $item->{$column_name} );
			case 'title':
				$title = esc_html( $item->post_title ) . ' - ';
				switch ( $item->post_status ) {
					case 'publish':
						$title .= sprintf( '<strong>%s</strong>', __( 'Published' ) );
						break;
					case 'future':
						$title .= sprintf( '<small>%s</small>', __( 'Future' ) );
						break;
					case 'draft':
						$title .= sprintf( '<small>%s</small>', __( 'Draft' ) );
						break;
					case 'trash':
						$title .= sprintf( '<small>%s</small>', __( 'Trash' ) );
						break;
					case 'private':
						$title .= sprintf( '<small>%s</small>', __( ':Private' ) );
						break;
				}
				$actions = array(
					'view' => sprintf( '<a href="%s">%s</a>', get_permalink( $item->ID ), __( 'View' ) ),
				);
				if ( current_user_can( 'edit_post', $item->ID ) ) {
					$actions['edit'] = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $item->ID ), __( 'Edit' ) );
				}
				$title .= $this->row_actions( $actions, false );
				return $title;
				break;
			case 'published':
				$date = mysql2date( get_option( 'date_format' ), $item->post_date );
				return $date;
				break;
			case 'updated':
				if ( $item->updated ) {
					return mysql2date( get_option( 'date_format' ), $item->updated );
				} else {
					return '---';
				}
				break;
			case 'ratio':
				$total = $item->positive + $item->negative;
				if ( $total ) {
					$positive = floor( $item->positive / $total * 100 );
					return sprintf( '<div class="chart-ratio"><div style="width: %d%%"></div></div>', $positive );
				} else {
					return '<div class="chart-ratio empty"></div>';
				}
				break;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}


	/**
	 * Table class
	 *
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', $this->_args['plural'] );
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'feedbacks':
				return FeedBacks::get_instance();
				break;
			case 'i18n':
				return I18n::get_instance();
				break;
			case 'input':
				return Input::get_instance();
				break;
			default:
				return parent::__get( $name );
				break;
		}
	}

}
