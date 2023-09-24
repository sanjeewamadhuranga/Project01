import React, { useReducer } from "react";
import flatten from "flat";
import { IntlProvider } from "react-intl";
import defaultSettings from "../settings";
import messages from "./messages";
import * as Sentry from "@sentry/react";
import AppContext from "../../context/Context";
import { settings } from "../../config/general";
import { setItemToStore, getItemFromStore } from "../../helpers/utils";

export const configReducer = (state, action) => {
  const { type, payload } = action;
  switch (type) {
    case "SET_CONFIG":
      if (payload.setInStore) {
        setItemToStore(payload.key, payload.value);
      }
      return {
        ...state,
        [payload.key]: payload.value,
      };
    case "RESET":
      return {
        ...state,
      };
    default:
      return state;
  }
};

export default function withIntl(WrappedComponent) {
  const configState = {
    isFluid: getItemFromStore("isFluid", settings.isFluid),
    isRTL: getItemFromStore("isRTL", settings.isRTL),
    isDark: getItemFromStore("isDark", settings.isDark),
    navbarPosition: getItemFromStore("navbarPosition", settings.navbarPosition),
    isNavbarVerticalCollapsed: getItemFromStore(
      "isNavbarVerticalCollapsed",
      settings.isNavbarVerticalCollapsed
    ),
    navbarStyle: getItemFromStore("navbarStyle", settings.navbarStyle),
    currency: settings.currency,
    showBurgerMenu: settings.showBurgerMenu,
    showSettingPanel: false,
    navbarCollapsed: false,
  };

  return function WithIntl(props) {
    const [config, configDispatch] = useReducer(configReducer, configState);

    const setConfig = (key, value) => {
      configDispatch({
        type: "SET_CONFIG",
        payload: {
          key,
          value,
          setInStore: [
            "isFluid",
            "isRTL",
            "isDark",
            "navbarPosition",
            "isNavbarVerticalCollapsed",
            "navbarStyle",
          ].includes(key),
        },
      });
    };
    return (
      <Sentry.ErrorBoundary
        fallback={
          <div>
            <h5>Something went wrong. Please try again later!</h5>
          </div>
        }
        beforeCapture={(scope) => {
          // Add any tags to be present on the errors logs due to an error boundary
          scope.setTag("location", "frontend:error_boundary");
        }}
      >
        <IntlProvider
          locale={defaultSettings.userLocale}
          timeZone={defaultSettings.timezone}
          defaultLocale={defaultSettings.systemLocale}
          messages={flatten(
            messages[defaultSettings.userLocale] ??
              messages[defaultSettings.systemLocale] ??
              messages["en"]
          )}
        >
          <AppContext.Provider value={{ config, setConfig }}>
            <WrappedComponent {...props} />
          </AppContext.Provider>
        </IntlProvider>
      </Sentry.ErrorBoundary>
    );
  };
}
