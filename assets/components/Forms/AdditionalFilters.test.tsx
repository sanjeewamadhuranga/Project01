import React, { PropsWithChildren } from "react";
import { renderWithIntl } from "../../helpers/testHelpers";
import AdditionalFilters, { Props } from "./AdditionalFilters";
import { fireEvent } from "@testing-library/react";
import Chance from "chance";

const chance = new Chance();

const renderComponent = (props: PropsWithChildren<Props>) =>
  renderWithIntl(<AdditionalFilters {...props} />);

describe("<AdditionalFilters /> component", () => {
  test("should display children props", () => {
    const title = chance.name();
    const { getByRole } = renderComponent({ children: <h2>{title}</h2> });
    expect(getByRole("heading", { level: 2 })).toHaveTextContent(title);
  });

  test("collapse should be toggled off, without passed props", () => {
    const { getByText } = renderComponent({});
    expect(getByText("Show Additional Filters")).toBeInTheDocument();
  });

  test("collapse should be toggled on, with passed props", () => {
    const { getByText } = renderComponent({ open: true });
    expect(getByText("Hide Additional Filters")).toBeInTheDocument();
  });

  test("attempt to toggle the collapse, by clicking on text", async () => {
    const { getByText } = renderComponent({});
    fireEvent.click(getByText("Show Additional Filters"));
    expect(getByText("Hide Additional Filters")).toBeInTheDocument();
    fireEvent.click(getByText("Hide Additional Filters"));
    expect(getByText("Show Additional Filters")).toBeInTheDocument();
  });
});
