import React, { Dispatch, SetStateAction } from "react";
import { Button } from "react-bootstrap";
import { LocaleType } from "../../../constants/general";

interface Props {
  locales: Array<LocaleType>;
  selectedLocaleCode: string;
  setSelectedLocaleCode: Dispatch<SetStateAction<string>>;
}

const ScreenTranslations = ({
  locales,
  setSelectedLocaleCode,
  selectedLocaleCode,
}: Props) => {
  const onClick = (newLocale, e) => {
    setSelectedLocaleCode(newLocale.code);
  };

  return (
    <span>
      {locales.map((locale, index) => (
        <Button
          className="me-2"
          variant={selectedLocaleCode === locale.code ? "dark" : "light"}
          size="sm"
          key={index}
          onClick={(e) => onClick(locale, e)}
        >
          {locale?.name}
        </Button>
      ))}
    </span>
  );
};

export default ScreenTranslations;
