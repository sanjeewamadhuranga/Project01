import React from "react";
import CrudActions, { Props } from "./CrudActions";
import { renderWithIntl } from "../../helpers/testHelpers";
import { fireEvent, act, waitFor } from "@testing-library/react";
import axios from "axios";
import Chance from "chance";

const chance = new Chance();

jest.mock("../../services/routing", () => ({
  generate: (path: string, option: { id?: string }) => {
    const splittedPath = path.split("_");
    if (
      option.id &&
      (splittedPath[1] === "edit" || splittedPath[1] === "delete")
    ) {
      return `${splittedPath[0]}/${option.id}/${splittedPath[1]}`;
    }

    return `${splittedPath[0]}/${splittedPath[1]}`;
  },
}));

const renderComponent = (props: Props) =>
  renderWithIntl(<CrudActions {...props} />);

describe("<CrudActions /> component", () => {
  const childrenTitle = chance.word();
  const props = {
    children: <p>{childrenTitle}</p>,
    routeNamePrefix: chance.domain(),
    title: chance.word(),
    id: chance.guid(),
  };
  test("should render edit, delete btn with passed props", () => {
    const { getAllByRole, getByText } = renderComponent(props);

    // check passed children props
    expect(getByText(childrenTitle)).toBeInTheDocument();

    // check edit btn
    expect(getAllByRole("link")[0]).toHaveAttribute(
      "href",
      `${props.routeNamePrefix}/${props.id}/edit`
    );
    expect(getAllByRole("link")[0]).toHaveClass("btn btn-sm");
    getByText((content, element) => {
      return (
        element.tagName.toLowerCase() === "span" &&
        element.className === "fas fa-pencil-alt table-edit-button"
      );
    });

    // check delete btn
    expect(getAllByRole("button")[0]).toHaveClass("btn btn-sm");
    getByText((content, element) => {
      return (
        element.tagName.toLowerCase() === "span" &&
        element.className === "fas fa-trash-alt table-delete-button"
      );
    });
  });

  describe("test user interactions, clicking on delete btn", () => {
    test("attempt close modal by clicking on cancel btn", async () => {
      const { getByRole, getAllByRole, getByText } = renderComponent(props);

      // click delete btn
      const deleteBtn = getAllByRole("button")[0];
      expect(deleteBtn).toBeInTheDocument();
      await act(async () => {
        fireEvent.click(deleteBtn);
      });

      // waiting for open modal
      const modal = getByRole("dialog");
      await waitFor(() => {
        expect(modal).toBeInTheDocument();
        expect(modal).toHaveClass("fade modal show");
        expect(modal).toHaveAttribute("aria-modal", "true");
      });

      // check texts
      expect(getByText(`Delete ${props.title}`)).toBeInTheDocument();
      expect(
        getByText(
          new RegExp(`Are you sure you want to delete ${props.title}?`, "i")
        )
      );

      // check btns
      const cancelBtn = getByRole("button", { name: "No, go back" });
      expect(cancelBtn).toBeInTheDocument();
      expect(
        getByRole("button", { name: `Yes, delete ${props.title}` })
      ).toBeInTheDocument();

      // attept to dismiss modal
      await act(async () => {
        fireEvent.click(cancelBtn);
      });

      await waitFor(() => {
        expect(modal).not.toBeInTheDocument();
      });
    });

    test("attept close modal by clicking outside", async () => {
      const { getByRole, getAllByRole } = renderComponent(props);

      // click delete btn
      const deleteBtn = getAllByRole("button")[0];
      expect(deleteBtn).toBeInTheDocument();
      await act(async () => {
        fireEvent.click(deleteBtn);
      });

      // waiting for open modal
      const modal = getByRole("dialog");
      await waitFor(() => {
        expect(modal).toBeInTheDocument();
        expect(modal).toHaveClass("fade modal show");
        expect(modal).toHaveAttribute("aria-modal", "true");
      });

      // attept to dismiss modal
      await act(async () => {
        fireEvent.click(modal);
      });

      await waitFor(() => {
        expect(modal).not.toBeInTheDocument();
      });
    });

    test("attept delete item by clicking confirm btn", async () => {
      const axiosSpy: jest.SpyInstance = jest
        .spyOn(axios, "delete")
        .mockResolvedValue("");

      const { getByRole, getAllByRole } = renderComponent(props);

      // click delete btn
      const deleteBtn = getAllByRole("button")[0];
      expect(deleteBtn).toBeInTheDocument();
      await act(async () => {
        fireEvent.click(deleteBtn);
      });

      // waiting for open modal
      const modal = getByRole("dialog");
      await waitFor(() => {
        expect(modal).toBeInTheDocument();
        expect(modal).toHaveClass("fade modal show");
        expect(modal).toHaveAttribute("aria-modal", "true");
      });

      // attept to dismiss modal
      await act(async () => {
        fireEvent.click(
          getByRole("button", { name: `Yes, delete ${props.title}` })
        );
      });

      await waitFor(() => {
        expect(axiosSpy).toHaveBeenCalledWith(
          `${props.routeNamePrefix}/${props.id}/delete`
        );
        expect(modal).not.toBeInTheDocument();
      });
    });
  });
});
