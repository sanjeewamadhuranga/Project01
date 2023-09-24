import React from "react";
import { cleanup, screen } from "@testing-library/react";
import { renderWithIntl } from "../../../helpers/testHelpers";
import ScreenTranslations from "./ScreenTranslations";
import { LocaleType } from "../../../constants/general";

const LOCALES_LIST: Array<LocaleType> = [
  { code: "en", name: "English" },
  { code: "si", name: "Sinhala" },
  { code: "ta", name: "Tamil" },
];

describe("<ScreenTranslations />", () => {
  afterEach(cleanup);
  it("should render a set of buttons per allowed locale", () => {
    //arrange

    //act
    renderWithIntl(
      <ScreenTranslations
        locales={LOCALES_LIST}
        onChangeLanguage={() => {
          // eslint-disable-next-line no-console
          console.log("test");
        }}
      />
    );
    //assert
    screen;
  });

  it("should show the button with default locale as the active button always", () => {
    //arrange
    //act
    //assert
  });

  it("user should see the content(we can check the values dispathing over props) for the fields if the selected locale translations available", () => {
    //arrange
    //act
    //assert
  });
});
