import React from "react";
import ToastMsg from "./ToastMsg";
import { renderWithIntl } from "../../helpers/testHelpers";

describe("<ToastMsg /> component", () => {
  test("should show the provided lable and message error", () => {
    const { getAllByText } = renderWithIntl(
      <ToastMsg
        labelId="common.actions.errorLabelMsg"
        msgId="common.actions.errorDescriptionMsg"
      />
    );
    const lable = getAllByText("Oops! Occurred an error.");
    const msg = getAllByText("Something went wrong.");
    expect(lable.length).toBe(1);
    expect(msg.length).toBe(1);
    expect(lable[0]).toHaveClass("text-white");
  });
});
