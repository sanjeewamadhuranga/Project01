import React from "react";
import { FormattedDate, FormattedTime } from "react-intl";

type Props = {
  date: string | null;
  displayTime?: boolean;
};

const DateFormatter = ({ date, displayTime = true }: Props) =>
  date ? (
    <span>
      <FormattedDate
        value={date}
        day="2-digit"
        month="2-digit"
        year="numeric"
      />{" "}
      {displayTime && (
        <FormattedTime
          value={date}
          hour="numeric"
          minute="numeric"
          second="numeric"
        />
      )}
    </span>
  ) : (
    <span>-</span>
  );

export default DateFormatter;
