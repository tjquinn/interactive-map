import { registerBlockType } from "@wordpress/blocks";

const mapStyles = {
  height: "500",
  width: "500",
  margin: "10px 0"
};

registerBlockType("im-ui-block", {
  title: "Interactive Map",
  icon: "location-alt",
  category: "layout",
  example: {},
  edit() {
    return <div style={mapStyles}>The map will render here.</div>;
  },
  save() {
    return <div style={mapStyles}>The map will render here.</div>;
  }
});
