(function(blocks, element) {
  var el = element.createElement;

  fetch("/wp-json/wp/v2/places?settings=true")
    .then(response => {
      return response.json();
    })
    .then(myJson => {
      console.log(myJson.im_api_key);

      // Get the first script element on the page
      var ref = document.querySelector('script[src*="map-ui.js"]');
      //console.log(document.querySelector('script[src*="map-ui.js"]'));
      var script = document.createElement("script");

      script.src =
        "https://maps.googleapis.com/maps/api/js?key=" +
        myJson.im_api_key +
        "&callback=initMap";
      script.async = true;
      script.defer = true;

      // Inject the script into the DOM
      ref.parentNode.insertBefore(script, ref);
    });

  var mapStyles = {
    height: "500px",
    width: "500px",
    margin: "10px 0",
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
  map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: -34.397, lng: 150.644 },
    zoom: 8
  });
}
