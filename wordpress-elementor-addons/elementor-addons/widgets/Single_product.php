<?php

class Single_product extends \Elementor\Widget_Base {

	public function get_name() {
		return 'rentmy_add_product';
	}

	public function get_title() {
		return esc_html__( 'Single Product (RentMy)', RNTM_TEXT_DOMAIN );
	}

	public function get_icon() {
		// https://elementor.github.io/elementor-icons/
		return 'eicon-single-product';
	}

	public function get_categories() {
		return [ 'basic', 'RentMy' ];
	}

	public function get_keywords() {
		return [ 'rentmy', 'rntm', 'product', 'add' ];
	}

	protected function register_controls() {

		// Content Tab Start

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Single product settings', RNTM_TEXT_DOMAIN ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', RNTM_TEXT_DOMAIN ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Hello world', RNTM_TEXT_DOMAIN ),
			]
		);
		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text Align', RNTM_TEXT_DOMAIN ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'default' => 'left',
				'options' => [
					'left' => __('Left', RNTM_TEXT_DOMAIN),
					'right' => __('Right', RNTM_TEXT_DOMAIN),
					'center' => __('Center', RNTM_TEXT_DOMAIN),
					'justify' => __('Justify', RNTM_TEXT_DOMAIN),
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


	// For Realtime Output On Editing Mode
	protected function render() {
		$settings = $this->get_settings_for_display();
		$title = $settings['title'];
		// $description = $settings['description']; 
		?>

		<div rentmy-product class="hello-world">
			<?php //echo $title ?>
			<?php //echo esc_html($description) /** Escape HTML */ ?>
			<?php //echo wp_kses_post($description)  /** Allow HTML */ ?>
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
		<h1 class="heading">{{{settings.heading}}}</h1>
		<p class="description">
			{{{ settings.description }}}
		</p>
		<?php		
	}
}