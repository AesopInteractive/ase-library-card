<?php

/*
* Plugin Name: ASE Library Card Features
*/

class aseLibraryCard {

	function __construct(){
		add_filter( 'cmb_meta_boxes', array($this,'meta') );
		add_shortcode('ase_library_card_features', array($this,'library_card_features'));
		add_shortcode('ase_library_card', array($this,'library_card_shortcode'));
		add_shortcode('ase_library_card_pricing', array($this,'library_card_pricing'));
	}

	function meta( array $meta_boxes ) {
		$meta_boxes[] = array(
			'title' => __('Library Card Features', 'aesop-core'),
			'pages' => 'page',
			'show_on' => array('page-template' => 'template-library-card.php'),
			'fields' => array(
				array(
					'id' 			=> 'ase_library_card_features',
					'name' 			=> __('Library Card Features', 'aesop-core'),
					'type' 			=> 'group',
					'repeatable'     => true,
					'sortable'		=> true,
					'desc'			=> __('Add text and image for each feature.', 'aesop-core'),
					'fields' 		=> array(
						array(
							'id' 	=> 'img',
							'name' 	=> __('Image', 'aesop-core'),
							'type' 	=> 'image',
							'cols'	=> 4
						),
						array(
							'id' 	=> 'text',
							'name' 	=> __('Text', 'aesop-core'),
							'type' 	=> 'wysiwyg',
							'cols'	=> 8,
							'options' => array(
							        'textarea_rows' => 5
							    )
						)
					)
				)
			)
		);
		$meta_boxes[] = array(
			'title' => __('Library Card Pricing', 'aesop-core'),
			'pages' => 'page',
			'show_on' => array('page-template' => 'template-library-card.php'),
			'fields' => array(
				array(
					'id' 			=> 'ase_library_card_pricing',
					'name' 			=> __('Library Card Pricing', 'aesop-core'),
					'type' 			=> 'group',
					'repeatable'     => true,
					'sortable'		=> true,
					'desc'			=> __('Add list items, price, and signup link.', 'aesop-core'),
					'fields' 		=> array(
						array(
							'id' 	=> 'name',
							'name' 	=> __('Name', 'aesop-core'),
							'type' 	=> 'text',
							'cols'	=> 4
						),
						array(
							'id' 	=> 'price',
							'name' 	=> __('Price', 'aesop-core'),
							'type' 	=> 'text',
							'cols'	=> 4
						),
						array(
							'id' 	=> 'monthly',
							'name' 	=> __('Monthly', 'aesop-core'),
							'type' 	=> 'text',
							'cols'	=> 4
						),
						array(
							'id' 	=> 'shortcode',
							'name' 	=> __('Shortcode', 'aesop-core'),
							'type' 	=> 'text',
							'cols'	=> 12
						),
						array(
							'id' 	=> 'features',
							'name' 	=> __('UL List of Features', 'aesop-core'),
							'type' 	=> 'wysiwyg',
							'cols'	=> 12,
							'options' => array(
						        'textarea_rows' => 5
						    )
						)
					)
				)
			)
		);
		return $meta_boxes;

	}

	function library_card_features($atts, $content) {
		ob_start();

		$features = get_post_meta( get_the_ID(), 'ase_library_card_features', false);

		?><ul class="unstyled library-card-feature-list ase-content"><?php
		$index = 0;
		foreach($features as $feature) {

			$index ++;
			$count = count($features);

			$getimg = isset($feature['img']) ? $feature['img'] : null;
			$text = isset($feature['text']) ? $feature['text'] : null;

			$img = $getimg ? wp_get_attachment_url( $getimg ) : false;
			?>
			<li class="library-card-feature-item">
				<?php if ($img) {?>
				<div class="ase-library-card-img ">
					<img class="ase-img" src="<?php echo $img;?>" alt="">
				</div>
				<?php } ?>
				<div class="ase-library-card-text">
					<?php echo wpautop($text); ?>
				</div>
			</li><?php
		}
		?></ul><?php

		return ob_get_clean();
	}

	// draws the users library card
	function library_card_shortcode($atts, $content = null){

		$defaults = array(
			'align' => 'center'
		);

		$atts = shortcode_atts($defaults, $atts);

		$user 		= wp_get_current_user();
		$user_id 	= $user->ID;
		$name  		= $user->display_name ? $user->display_name : 'Jackey Writesalot';
		

		$default_avatar = get_template_directory_uri().'/assets/img/default-avatar.jpg';
		$avatar 		= get_avatar( $user->ID, '100', '','library card user' );

		// logo
		$logo = get_template_directory_uri().'/assets/img/ase-logo-colored.png';

		// barcode
		$barcode = get_template_directory_uri().'/assets/img/barcode.png';

		//recurring id
		$subscription_id = function_exists('get_customer_id') ? get_customer_id( $user->ID ) : rand();

		// expiration date
		$expiration = function_exists('get_customer_expiration') ? get_customer_expiration( $user->ID ) : '02/15';

		ob_start();

		?>
		<div class="ase-library-card <?php echo $atts['align'];?>">
			<div class="ase-library-card-inner">
				<div class="ase-library-card-top">
					<img src="<?php echo $logo;?>" alt="aesop library card">
					<h2>Library Card</h2>
				</div>
				<div class="ase-library-card-middle">
					<?php echo $avatar;?>
					<h6><?php echo $name;?></h6>
				</div>
				<div class="ase-library-card-bottom">
					<div class="ase-library-card-bottom-left ">
						<img src="<?php echo $barcode;?>">
						<?php echo $subscription_id;?>
					</div>
					<div class="ase-library-card-bottom-right">
						<p><span>expires</span> <?php echo $expiration;?></p>
						<a href="<?php get_bloginfo('url');?>">www.aesopstoryengine.com</a>
					</div>	
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	function library_card_pricing( $atts, $content = null ) {

		$defaults = array(

		);
		$atts = shortcode_atts( $defaults, $atts );

		$plans = get_post_meta( get_the_ID(),'ase_library_card_pricing', false );

		ob_start();

		?>
		<ul class="ase-library-card-pricing unstyled">
			<?php foreach($plans as $plan){ ?>
			<li>
				<div class="pricing-top">
					<h4 class="ase-card-plan-name"><?php echo $plan['name'];?></h4>
					<div class="ase-card-plan-price"><span class="denom">$</span><?php echo $plan['price'];?></div>
					<div class="ase-card-plan-sub">+ $<?php echo $plan['monthly'];?> monthly</div>
				</div>
				<div class="ase-card-signup">
					<?php echo do_shortcode($plan['shortcode']);?>
				</div>
				<div class="pricing-bottom">
					<?php echo wpautop($plan['features']);?>
				</div>
			</li>
			<?php } ?>
		</ul>
		<?php

		return ob_get_clean();
	}

}
new aseLibraryCard;