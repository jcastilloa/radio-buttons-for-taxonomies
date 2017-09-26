<?php
//
// Category Radio Lists
//

/**
 * Walker to output an unordered list of category radio <input> elements.
 * Mimics Walker_Category_Checklist excerpt for the radio input
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 * @since 2.5.1
 */
class Walker_Category_Radio extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);

		if ( empty($taxonomy) )
			$taxonomy = 'category';

		$name = 'radio_tax_input['.$taxonomy.']';

		//get first term object
		$post_terms = $selected_cats;
		$current_term = ! empty( $selected_cats ) && ! is_wp_error( $selected_cats ) ? array_pop( $selected_cats ) : false;

		// if no term, match the 0 "no term" option
		$current_id = ( $current_term ) ? $current_term : 0;

		// switching radio tags to "hierarchical" so we'll always use ID
		$value = $category->term_id;

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';

		$allow_multiple = apply_filters( 'radio_buttons_for_taxonomies_allow_multiple_terms', [$category->term_id]);
		if ($allow_multiple !== true) $allow_multiple = false;

		if ($allow_multiple) {
			$input_type = 'checkbox';
			$checked = in_array($category->term_id, $post_terms) ? 'checked' : '';
		} else {
			$input_type = 'radio';
			$checked = ($current_id == $category->term_id) ? 'checked' : '';
		}

		$output .= sprintf( "\n" . '<li id="%1$s-%2$s" %3$s><label class="selectit"><input id="%4$s" type="%5$s" name="%6$s" value="%7$s" %8$s %9$s/> %10$s</label>' ,
			$taxonomy, //1
			$value, //2
			$class, //3
			"in-{$taxonomy}-{$category->term_id}", //4
			$input_type, //5
			$name . '[]', //6
			esc_attr( trim( $value ) ), //7
			$checked, //8
			disabled( empty( $args['disabled'] ), false, false ), //9
			esc_html( apply_filters( 'the_category', $category->name ) ) //10
		);

	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 2.5.1
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	function end_el( &$output, $term, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
