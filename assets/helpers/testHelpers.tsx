/* eslint-disable react/display-name */
import { render } from "@testing-library/react";
import React from "react";
import flatten from "flat";
import { Formik } from "formik";
import { IntlProvider } from "react-intl";
import enMessages from "../i18n/en.json";
import "./mocks";

export const renderWithForm = (
  ui: React.ReactNode,
  options?: { onSubmit?: () => void; initialValues: object }
) => {
  return {
    ...renderWithIntl(
      <Formik
        onSubmit={(options && options.onSubmit) ?? jest.fn()}
        initialValues={(options && options.initialValues) ?? {}}
      >
        {ui}
      </Formik>
    ),
  };
};

export const renderWithIntl = (ui: React.ReactNode) =>
  render(
    <IntlProvider locale="en" messages={flatten(enMessages)}>
      {ui}
    </IntlProvider>
  );
