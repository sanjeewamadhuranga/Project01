import { FormikValues } from "formik";
import { SectionType } from "../models/onboarding";
import { AddScreenType, ScreenTranslation } from "../reducers/types";

export const isScreenBelongToTenant = (screen: AddScreenType) => {
  const isBelong = screen?.tenant_keys?.includes(
    window?.APP_SETTINGS?.branding?.theme
  );
  return isBelong;
};

interface NewScreen {
  title: string;
  description: string | JSX.Element;
  key: string;
  businessTypeKey?: string;
  businessTypes?: Array<string>;
  titleTranslations: { [locale: string]: string };
  descriptionTranslations: { [locale: string]: string };
}

export const prepareNewScreenPayload = (
  newScreen: NewScreen,
  isSwitchOn: boolean
) => {
  const dependencies = {
    field: newScreen?.businessTypeKey,
    value: newScreen?.businessTypes,
    comparison: "IN",
  };

  const payload = {
    title: newScreen?.title,
    description: newScreen?.description,
    key: newScreen?.key,
    titleTranslations: newScreen.titleTranslations,
    descriptionTranslations: newScreen.descriptionTranslations,
  };

  return isSwitchOn
    ? { ...payload, ...{ dependencies: [dependencies] } }
    : payload;
};

export const isValidDependencies = (props, isSwitchOn) => {
  if (isSwitchOn) {
    return (
      // eslint-disable-next-line react/prop-types
      props?.values?.businessTypes && props?.values?.businessTypes?.length > 0
    );
  } else {
    return true;
  }
};

export const prepareScreenTranslations = (
  currentLocaleCode: string,
  values: FormikValues | undefined,
  translations: ScreenTranslation
) => {
  return {
    titleTranslations: {
      ...translations.titleTranslations,
      [currentLocaleCode]: values?.title,
    },
    descriptionTranslations: {
      ...translations.descriptionTranslations,
      [currentLocaleCode]: values?.description,
    },
  };
};

export const isPresetKey = (key: string) => {
  return Object.keys(SectionType)
    .filter((item) => item !== "NEW")
    .includes(key);
};
