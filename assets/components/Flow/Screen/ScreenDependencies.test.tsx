import React from "react";
import { cleanup, fireEvent } from "@testing-library/react";
import { BUSINESS_TYPE_LK, company } from "../../../constants/general";
import ScreenDependencies from "./ScreenDependencies";
import { renderWithForm } from "../../../helpers/testHelpers";
import { Dependency } from "../../../reducers/types";

describe("<Screen/> dependencies component", () => {
  const _businessTypesLK = {
    field: company.BUSINESS_TYPE_LK.toString(),
    value: [
      BUSINESS_TYPE_LK.INDIVIDUAL.toString(),
      BUSINESS_TYPE_LK.LIMITED_COMPANY.toString(),
    ],
  };

  const _mock = {
    onSwitchChange: (e) => {
      //
    },
    withDependencies: [
      {
        field: _businessTypesLK.field,
        comparison: "IN",
        value: _businessTypesLK.value,
      },
    ],
    withoutDependencies: [],
  };

  interface Props {
    onSwitchChange: (isSwitchOn: boolean) => void;
    dependencies?: Array<Dependency>;
  }

  const onSwitchChange = _mock.onSwitchChange;
  const withdependencies = _mock.withDependencies;
  const witoutDependencies = _mock.withoutDependencies;

  afterEach(cleanup);

  describe("When screen is marked it has no dependencies", () => {
    it("Switch should off", () => {
      // arrange
      // act
      const { container, getByTestId, getByText } = renderWithForm(
        <ScreenDependencies
          onSwitchChange={onSwitchChange}
          dependencies={witoutDependencies}
        />
      );

      // assert
      expect(getByTestId("screen-dependencies-switch")).toBeInTheDocument();
      expect(container).not.toHaveAttribute("Show this screen:");
      expect(container).not.toHaveAttribute("Select", { exact: true });
    });
  });

  describe("When screen is marked it has dependencies", () => {
    it("Switch should on", () => {
      // arrange
      // act
      const { getByTestId, getByText } = renderWithForm(
        <ScreenDependencies
          onSwitchChange={onSwitchChange}
          dependencies={withdependencies}
        />
      );

      // assert
      const checkbox: HTMLElement = getByTestId("screen-dependencies-switch");
      expect(getByText("Show this screen:")).toBeInTheDocument();
      expect(checkbox).toBeInTheDocument();

      // expect(checkbox.props.checked).toEqual(true); // Why its checked is not extractable?
      expect(getByText("Show this screen:")).toBeInTheDocument();
      expect(getByText("Show this screen:")).toBeInTheDocument();
    });

    it("Dropdown should be rendered with list of dependency type and its values", async () => {
      // arrange

      // act
      const { getByRole, getByText, getAllByRole } = renderWithForm(
        <ScreenDependencies
          onSwitchChange={onSwitchChange}
          dependencies={withdependencies}
        />
      );

      // assert
      expect(getByText("Select...")).toBeInTheDocument();
      expect(getByText("Select", { exact: true })).toBeInTheDocument();

      fireEvent.click(getByText("Select"));

      // const options = await getAllByRole("option");
      // expect(options.length).toEqual(
      //   Object.keys(businessTypes.company.BUSINESS_TYPE_UK).length
      // );

      // Object.keys(businessTypes.company.BUSINESS_TYPE_UK).map((val, idx) => {
      //   expect(options[idx]).toHaveValue(
      //     businessTypes.company.BUSINESS_TYPE_UK[val]
      //   );
      // });

      fireEvent.click(getByText("Select..."));
    });
  });

  describe("User remove a value from selected list ", () => {
    it("selected list should rendered correctly witout the removed item", () => {
      // arrange
      // act
      // assert
    });

    describe("then click the update button", () => {
      it("should update the component state properly", () => {
        // arrange
        // act
        // assert
      });
    });
  });

  describe("Update a screen with dependencies as no dependencies", () => {
    it("remove all the existing dependencies", () => {
      // arrange
      // act
      // assert
    });

    it("trigger onSwitchChange callback", () => {
      // arrange
      // act
      // assert
    });
  });
});
