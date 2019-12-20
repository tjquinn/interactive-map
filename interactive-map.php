<?php

/**
 * Plugin Name: Interactive Map
 * Plugin URI: add git hub link here
 * Description: Create an interactive map of locations.
 * Version: 0.1
 * Author: Taylor Quinn
 * Author URI: https://tquinn.dev
 */

//  For the sake of brevity this plugin will live in one file. 
//  Usually I would create a best practice folder architecture.


class IM_Plugins
{
    public function __construct()
    {
        $this->version = 0.1;
        $this->api_key = 'XXXXXXXXX';
        add_action('init', array($this, 'im_register_post_type'));
        register_activation_hook(__FILE__, array($this, 'im_plugin_install'));
        add_filter('use_block_editor_for_post_type', array($this, 'im_disable_gutenberg'), 10, 2);
        register_deactivation_hook(__FILE__, array($this, 'im_plugin_deactivation'));
        add_action('add_meta_boxes', array($this, 'im_setup_admin_meta_collection'));
        add_action('save_post_places', array($this, 'im_save_admin_meta'));
        add_action('admin_footer', array($this, 'im_enqueue_autocomplete'));
    }

    public static function im_register_post_type()
    {
        $args = array(
            'labels'             => array('name' => 'Places'),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'show_in_rest'       => true,
            'rest_base'          => 'places',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'supports'           => array('title', 'thumbnail')
        );
        register_post_type('places', $args);
    }


    public static function im_plugin_install()
    {
        im_register_post_type();
        flush_rewrite_rules();
    }



    public static function im_plugin_deactivation()
    {
        unregister_post_type('places');
        flush_rewrite_rules();
    }

    public static function im_disable_gutenberg($current_status, $post_type)
    {
        if ($post_type === 'places') return false;
        return $current_status;
    }

    public static function im_setup_admin_meta_collection()
    {
        add_meta_box('place_field', 'Place', array($this, 'im_place_meta_box'), 'places', 'normal', 'default', $this->api_key);
    }

    public static function im_place_meta_box($post, $meta)
    {
        $place = get_post_meta($post->ID);
        $place_field = ($place['place_field'][0]) ? $place['place_field'][0] : '';
        $lat_field = ($place['lat_field'][0]) ? $place['lat_field'][0] : '';
        $lng_field = ($place['lng_field'][0]) ? $place['lng_field'][0] : '';

        echo '<div id="locationField">';
        echo '<label for="' . $meta['id'] . '">Search Places</label><br>';
        echo '<input id="autocomplete" name="' . $meta['id'] . '" placeholder="Enter a city" type="text" onFocus="geolocate()" value="' . $place_field . '" />';
        echo '<input id="lat" name="lat_field" type="hidden" value="' . $lat_field . '" />';
        echo '<input id="lng" name="lng_field" type="hidden" value="' . $lng_field . '" />';
        echo '</div>';
    }

    public static function im_save_admin_meta($post_id)
    {


        if (array_key_exists('place_field', $_POST)) {
            update_post_meta($post_id, 'place_field', $_POST['place_field']);
        }
        if (array_key_exists('lat_field', $_POST)) {
            update_post_meta($post_id, 'lat_field', $_POST['lat_field']);
        }
        if (array_key_exists('lng_field', $_POST)) {
            update_post_meta($post_id, 'lng_field', $_POST['lng_field']);
        }
    }

    public static function im_enqueue_autocomplete()
    {
?>
        <script>
            var placeSearch, autocomplete;

            var componentForm = {
                street_number: "short_name",
                route: "long_name",
                locality: "long_name",
                administrative_area_level_1: "short_name",
                country: "long_name",
                postal_code: "short_name"
            };

            function initAutocomplete() {
                // Create the autocomplete object, restricting the search predictions to
                // geographical location types.
                autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById("autocomplete"), {
                        types: ["geocode"]
                    }
                );

                // Avoid paying for data that you don't need by restricting the set of
                // place fields that are returned to just the address components.
                autocomplete.setFields(["geometry", "name", "address_component"]);

                // When the user selects an address from the drop-down, populate the
                // address fields in the form.
                autocomplete.addListener("place_changed", fillInAddress);
            }

            function fillInAddress() {
                // Get the place details from the autocomplete object.
                let place = autocomplete.getPlace(),
                    title = document.getElementById("title"),
                    placeholder = document.getElementById("title-prompt-text"),
                    lat = document.getElementById("lat"),
                    lng = document.getElementById("lng");

                lat.value = place.geometry.location.lat();
                lng.value = place.geometry.location.lng();

                if (title.value === "") {
                    title.value = place.name;
                    placeholder.style.display = "none";
                }
            }

            // Bias the autocomplete object to the user's geographical location,
            // as supplied by the browser's 'navigator.geolocation' object.
            function geolocate() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var geolocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        var circle = new google.maps.Circle({
                            center: geolocation,
                            radius: position.coords.accuracy
                        });
                        autocomplete.setBounds(circle.getBounds());
                    });
                }
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $this->api_key; ?>&libraries=places&callback=initAutocomplete" async defer></script>
<?php
    }
}

new IM_Plugins;
