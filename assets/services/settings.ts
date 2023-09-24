import { getLocalStorageData } from "./search";

interface Config {
  systemLocale: string;
  userLocale: string;
  currencies: Array<string>;
  defaultCurrency: string;
  branding: {
    owner: string;
    theme: string;
  };
  layout: {
    condensed: boolean;
    fluid: boolean;
  };
  timezone: string;
  dashboard: string;
  features: Array<string>;
  version: string;
  environment: string;
  user?: string;
  permissions?: string[];
  enabledLanguages?: string[];
}

declare global {
  interface Window {
    SENTRY_DSN?: string;
    APP_SETTINGS: Config;
    showFeedbackDialog: (eventId: string) => void;
  }
}

const config: Config = window["APP_SETTINGS"];
export function setPaginationLimit(limit: number): void {
  localStorage.setItem("paginationLimit", limit.toString());
}

// is open sidebar
export function setNavbarVertical(newState: boolean): void {
  localStorage.setItem("isNavbarVerticalCollapsed", newState.toString());
}

const paginationLimit = Number(localStorage.getItem("paginationLimit"));
const isNavbarVerticalCollapsed = getLocalStorageData(
  "isNavbarVerticalCollapsed"
);

export default {
  ...config,
  isNavbarVerticalCollapsed,
  paginationLimit:
    !isNaN(paginationLimit) && paginationLimit > 0 && isFinite(paginationLimit)
      ? paginationLimit
      : 25,
};
