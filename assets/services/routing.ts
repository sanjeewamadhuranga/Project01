import Routing from "../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router";
declare global {
  interface Window {
    APP_ROUTES: Record<string, unknown>;
  }
}

Routing.setRoutingData(
  process.env.NODE_ENV === "development"
    ? window?.APP_ROUTES
    : require("../../public/fos_js_routes.json")
);

export default Routing;
