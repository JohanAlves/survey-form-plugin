<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode('sport_survey_form','showSportSurvey');
function showSportSurvey(){    
    wp_enqueue_script('main_sports_survey_js', HTTPS_WP_PLUGIN_URL.'/sport-survey/sport-survey.js',array('jquery'),'1.0',true);
    wp_localize_script('main_sports_survey_js','ssData',array(
        'siteurl' =>  HTTPS_SITE_URL       
    ));

    //clear cart to see plugin in action
    global $woocommerce;
    $woocommerce->cart->empty_cart();

    $products = new WP_Query(array(
        'post_type' => 'product'
    ));

    $att_workout_level = get_terms( 'pa_workout-level' );
    foreach($att_workout_level as $term){
        $all_att_wl[$term->slug] = $term->term_id;
    }

    $att_workout_type = get_terms( 'pa_workout-type' );
    foreach($att_workout_type as $term){
        $all_att_wt[$term->slug] = $term->term_id;
    }

    
?>
    <section class="sports_survey">
        <form id="sports_survey_form" action="">
            <div class="Ssurvey_overlay disabled"><div class="lds-ripple"><div></div><div></div></div></div>


            <div class="form_class" data-order=1> <!-- FORM CLASS -->
                <label for="ss_name">Welcome to our Sports Survey! First, what's your name?</label>                
                <input type="text" name="ss_name" class="ss_name text_field" id="ss_name">
                <div class="error_container">
                    <small class="error"></small>
                </div>   
                <div class="control-links">
                    <a class="" href="#"></a>
                    <a class="ss_nextform" href="#">Let's Start</a>                    
                </div>
            </div>


            <div class="form_class hide" data-order=2> <!-- FORM CLASS -->
                <legend>Nice to meet you {name}! What type of exercises do you like to do?</legend>
                <div class="checkbox_wrapper">               
                    <input type="checkbox" class="pa_workout-type checkable_answer" name="pa_workout-type" value="bodyweight" >
                    <div class="checkbox_container">
                        <span>Bodyweight exercises</span>
                    </div>
                </div> 
                <div class="checkbox_wrapper">   
                    <input type="checkbox" class="pa_workout-type checkable_answer" name="pa_workout-type" value="dumbbell">
                    <div class="checkbox_container">
                        <span>Dumbbell Sequences</span>
                    </div>
                </div> 
                <div class="checkbox_wrapper">   
                    <input type="checkbox" class="pa_workout-type checkable_answer" name="pa_workout-type" value="mixed">
                    <div class="checkbox_container">
                        <span>Mixed Training</span>
                    </div>
                </div> 
                <div class="error_container">
                    <small class="error"></small>
                </div>                
                <div class="control-links">
                    <a class="" href="#"></a>
                    <a class="ss_nextform" href="#">Next</a>                    
                </div>
            </div>


            <div class="form_class hide" data-order=3> <!-- FORM CLASS -->
                <legend>Awesome! How often do you workout?</legend>
                <div class="radio_wrapper">               
                    <input type="radio" class="pa_workout-level radioable_answer" name="pa_workout-level" value="beginner">
                    <div class="radio_container">
                        <span>1 to 2 times per week (Beginner)</span>
                    </div>
                </div> 
                <div class="radio_wrapper">   
                    <input type="radio" class="pa_workout-level radioable_answer" name="pa_workout-level" value="intermediate">
                    <div class="radio_container">
                        <span>3-5 times per week  (Intermediate)</span>
                    </div>
                </div> 
                <div class="radio_wrapper">   
                    <input type="radio" class="pa_workout-level radioable_answer" name="pa_workout-level" value="advanced">
                    <div class="radio_container">
                        <span>5+ times per week (Advanced)</span>
                    </div>
                </div>  
                <div class="radio_wrapper">   
                    <input type="radio" class="pa_workout-level radioable_answer" name="pa_workout-level" value="beginner">
                    <div class="radio_container">
                        <span>I don't :(  (Beginner)</span>
                    </div>
                </div>  
                <div class="error_container">
                    <small class="error"></small>
                </div>   
                <div class="control-links">
                    <a id="sports_survey_filter" href="#">Help me Decide!</a>                                      
                </div>
            </div>

            <div class="form_class hide" data-order=4><!-- FORM CLASS -->
                <legend>Based on our expertise, these are the products that you need to increase your performance, {name}:</legend>
                <div id="products_display" class="products_display">
                <?php while ($products->have_posts()):
                    $products->the_post();
                    $product = wc_get_product($products->get_the_id());
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'single-post-thumbnail' );?>

                    <div class="checkbox_wrapper">           
                        <input type="checkbox" class="product_to_cart_checkbox id-<?php echo get_the_id(); ?>" name="product_to_cart" id="" value="<?php echo get_the_id(); ?>">
                        <div class="product_card" data-id=<?php echo get_the_id(); ?> data-nonce="<?php echo wp_create_nonce('wc_store_api') ?>">
                            <img src="<?php  echo $image[0]; ?>" alt="">
                            <h4 class="product_title"><?php echo get_the_title(); ?></h4>
                            <span class="workout-level"><?php echo $product->get_attribute('pa_workout-level'); ?></span>
                            <span class="workout-times"><?php echo $product->get_attribute('pa_workout-type'); ?></span>
                            <p class="product_price"><?php echo $product->get_price_html(); ?></p>                                            
                        </div>
                        <span class="dashicons dashicons-saved">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="20px" height="19px" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path d="M9 19.414l-6.707-6.707l1.414-1.414L9 16.586L20.293 5.293l1.414 1.414z" fill="white"/></svg>
                        </span> 
                    </div>

                <?php endwhile; wp_reset_postdata() ?>


                    
                </div>
                <button disabled id="sports_survey_submit" type="submit">Select at least one</button>                
            </div>
            
        </form>



        
    </section>



    <script>
        var _nonce = "<?php echo wp_create_nonce( 'wc_store_api' ); ?>";

    </script>
   
    
    <?php

     

}

