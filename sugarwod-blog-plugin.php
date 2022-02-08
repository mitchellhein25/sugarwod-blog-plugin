<?php
/*
Plugin Name: SugarWod Blog Plugin
Description: Show SugarWod workouts in a blog post
Version: 0.1
*/

// Register and load the widget
function sugarwod_load_widget() {
    register_widget( 'sugarwod_blog_widget' );
}
add_action( 'sugarwod_init', 'sugarwod_load_widget' );

// The widget Class
class sugarwod_widget extends WP_Widget {

  function __construct() {
    parent::__construct(

      // Base ID of your widget
      'sugarwod_widget',

      // Widget name will appear in UI
      __('SugarWod Widget', 'sugarwod_widget_domain'),

      // Widget description
      array( 'description' => __( 'Show SugarWod workouts in a blog post', 'sugarwod_widget_domain' ), )
    );
  }

  // Creating widget front-end view
  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );

    //Only show to me during testing - replace the Xs with your IP address if you want to use this
    //if ($_SERVER['REMOTE_ADDR']==="xxx.xxx.xxx.xxx") {

      // before and after widget arguments are defined by themes
      echo $args['before_widget'];
      if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];

      // This is where you run the code and display the output
      $curl = curl_init();
      $url = "https://api.sugarwod.com/v2/box/workouts?dates=20220208&track_id=workout-of-the-day";

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: c02ca252-b2c7-49ad-b2be-eb77c786f1ce",
            "Content-Type: application/json"
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        //Only show errors while testing
        //echo "cURL Error #:" . $err;
      } else {
        //The API returns data in JSON format, so first convert that to an array of data objects
        $responseObj = json_decode($response);

        //Gather the workout information
        $title = $responseObj[0]->title;
        $description = $responseObj[0]->description;
        $score_type = $responseObj[0]->score_type;;

        //This is the content that gets populated into the widget on your site
        echo "$title <br>";
        echo "$description <br>";
        echo "$score_type <br>";
      }

      echo $args['after_widget'];

    //} // end check for IP address for testing
  } // end public function widget

  // Widget Backend - this controls what you see in the Widget UI
  //  For this example we are just allowing the widget title to be entered
  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    } else {
      $title = __( 'New title', 'wpb_widget_domain' );
    }
    // Widget admin form
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
  }

  // Updating widget - replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
  }
} // Class sugarwod_widget ends here
?>