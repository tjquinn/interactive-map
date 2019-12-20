(function(blocks, element) {
  var el = element.createElement;

  var mapStyles = {
    height: "500",
    width: "500",
    margin: "10px 0"
  };

  blocks.registerBlockType("layouts/im-ui-block-js", {
    title: "Interactive Map",
    icon: "location-alt",
    category: "layout",
    example: {},
    edit: function() {
      return el("p", { style: mapStyles }, "Map will render here.");
    },
    save: function() {
      return el("p", { style: mapStyles }, "Map will render here.");
    }
  });
})(window.wp.blocks, window.wp.element);
