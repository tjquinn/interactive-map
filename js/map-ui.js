(function(blocks, element) {
  var el = element.createElement;

  fetch("/wp-json/wp/v2/places?settings=true")
    .then(response => {
      return response.json();
    })
    .then(settings => {
      var ref = document.querySelector('script[src*="map-ui.js"]');
      var script = document.createElement("script");

      script.src =
        "https://maps.googleapis.com/maps/api/js?key=" +
        settings.im_api_key +
        "&callback=initMap";
      script.async = true;
      script.defer = true;
      ref.parentNode.insertBefore(script, ref);
    });

  var mapStyles = {
    height: "500px",
    width: "500px",
    margin: "2em auto",
    background: "red",
    display: "block"
  };

  blocks.registerBlockType("layouts/im-ui-block-js", {
    title: "Interactive Map",
    icon: "location-alt",
    category: "layout",
    edit: function(props) {
      return el("div", { id: "map", style: mapStyles }, "");
    },
    save: function(props) {
      return el("div", { id: "map", style: mapStyles }, "");
    }
  });
})(window.wp.blocks, window.wp.element);

var map;
function initMap() {
  fetch("/wp-json/wp/v2/places")
    .then(response => {
      return response.json();
    })
    .then(places => {
      const markers = places.map(place => {
        return {
          title: place.post_title,
          position: new google.maps.LatLng(place.lat_field, place.lng_field)
        };
      });

      map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 40.0992294, lng: -83.1140771 },
        zoom: 12
      });

      for (i in markers) {
        var marker = new google.maps.Marker({
          position: markers[i].position,
          map: map
        });
      }
    });
}
