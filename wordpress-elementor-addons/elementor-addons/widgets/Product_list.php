<?php

class Product_list extends \Elementor\Widget_Base {

	public function get_name() {
		return 'rentmy_product_list';
	}

	public function get_title() {
		return esc_html__( 'Product List (RentMy)', RNTM_TEXT_DOMAIN );
	}

	public function get_icon() {
		//https://elementor.github.io/elementor-icons/
		return 'eicon-products-archive';
	}

	public function get_categories() {
		return [ 'basic', 'RentMy' ];
	}

	public function get_keywords() {
		return [ 'rentmy', 'rntm', 'product', 'add', 'prodcut-list' ];
	}

	protected function register_controls() {

		// Content Tab Start

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Product List Settings', RNTM_TEXT_DOMAIN ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'select_products',
			[
				'label' => esc_html__( 'Select Product', RNTM_TEXT_DOMAIN ),
				'label_block' => true,
				'multiple' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				// 'default' => 'left',
				'options' => [
					'1' => __('Product 1', RNTM_TEXT_DOMAIN),
					'2' => __('Product 2', RNTM_TEXT_DOMAIN),
					'3' => __('Product 3', RNTM_TEXT_DOMAIN),
					'4' => __('Product 4', RNTM_TEXT_DOMAIN),
					'5' => __('Product 5', RNTM_TEXT_DOMAIN),
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text Align', RNTM_TEXT_DOMAIN ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __('Left', RNTM_TEXT_DOMAIN),
					'right' => __('Right', RNTM_TEXT_DOMAIN),
					'center' => __('Center', RNTM_TEXT_DOMAIN),
				],
				'selectors' => [
					'{{WRAPPER}} p' => 'text-align: {{value}}'
				],
			]
		);
		$this->add_control(
			'button_color',
			[
				'label' => esc_html__( 'Button Text Color', RNTM_TEXT_DOMAIN ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => 'black',
				'selectors' => [
					'{{WRAPPER}} p' => 'color: {{value}}'
				],
			]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$products = $settings['select_products'];
		?>
		<div rentmy-product-list ids="<?php echo implode(',', array_values($products)) ?>" class="hello-world">
		</div>

		<?php
	}

	protected function _content_template() {
		?>
		<!-- JavaScript Block -->
		<#
			console.log(settings)
		#>
		<!-- HTML Output Block -->
		<!-- <p class="description">
			{{{ settings.description }}}
		</p> -->
		<div rentmy-product-list class="hello-world" uid="{{{ settings.select_products }}}">
		</div>

		<?php		
	}
}