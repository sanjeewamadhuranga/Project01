import React from "react";
import { renderWithIntl } from "../../helpers/testHelpers";
import BoolLabel from "./BoolLabel";

describe("<BoolLabel /> component", () => {
  test("display success badge", () => {
    const { getByText } = renderWithIntl(<BoolLabel value={true} />);

    expect(getByText("Yes")).toHaveClass("badge-soft-success");
  });

  test("display danger badge", () => {
    const { getByText } = renderWithIntl(<BoolLabel value={false} />);

    expect(getByText("No")).toHaveClass("badge-soft-danger");
  });
});
