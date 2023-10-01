<?php

class Cart_total extends \Elementor\Widget_Base {

	public function get_name() {
		return 'rentmy_cart_total';
	}

	public function get_title() {
		return esc_html__( 'Cart Total (RentMy)', RNTM_TEXT_DOMAIN );
	}

	public function get_icon() {
		// https://elementor.github.io/elementor-icons/
		return 'eicon-cart-light';
	}

	public function get_categories() {
		return [ 'basic', 'RentMy' ];
	}

	public function get_keywords() {
		return [ 'rentmy', 'rntm', 'product', 'add', 'cart' ];
	}

	protected function register_controls() {
		

	}


	// For Realtime Output On Editing Mode
	protected function render() {
		$settings = $this->get_settings_for_display();
		// $title = $settings['title'];
		?>

		<div class="hello-world rentmy-cart-total"></div>

		<?php
	}

	protected function _content_template() {
			
	}
}