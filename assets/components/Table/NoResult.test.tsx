import React from "react";
import { render } from "@testing-library/react";
import NoResult, { Props } from "./NoResult";
import Chance from "chance";

const chance = new Chance();

describe("<NoResult /> component", () => {
  test("tests default props", () => {
    const { getByRole } = render(<NoResult />);
    expect(getByRole("heading", { level: 1, name: "No results" }));
  });

  describe("tests with own props", () => {
    let props: Props;
    beforeAll(() => {
      props = {
        header: chance.name(),
        body: chance.paragraph(),
      };
    });

    test("display component with props (without buttonText)", () => {
      const { getByRole, getByText, queryByRole } = render(
        <NoResult {...props} />
      );

      expect(getByRole("heading", { level: 1, name: props.header }));
      expect(getByText(props.body));
      expect(queryByRole("button")).toBeNull();
    });

    test("display component with all allowed props", () => {
      props = {
        ...props,
        buttonText: chance.domain(),
        buttonLink: chance.url(),
      };
      const { getByRole, getByText } = render(<NoResult {...props} />);

      expect(getByRole("heading", { level: 1, name: props.header }));
      expect(getByText(props.body));
      expect(getByRole("button", { name: props.buttonText })).toHaveAttribute(
        "href",
        props.buttonLink
      );
    });
  });
});
