import React from "react";
import { flow } from "../../dummyData/flow";
import { renderWithIntl } from "../../helpers/testHelpers";
import Section from "./Section";
import { DragDropContext } from "react-beautiful-dnd";
import {
  fireEvent,
  getAllByText,
  getByRole,
  getByText,
} from "@testing-library/react";

describe("<Section /> component", () => {
  let dom;

  beforeEach(() => {
    dom = renderWithIntl(
      <DragDropContext>
        <Section section={flow.sections[0]} />
      </DragDropContext>
    );
  });

  test("Render a section", () => {
    const items = getAllByText(dom.container, "Tell us about you");
    expect(items).toHaveLength(1);
    expect(getByText(dom.container, "USER_NAME")).toBeInTheDocument();
  });

  test("Click 'Add new screen'", async () => {
    expect(
      getByRole(dom.container, "button", { name: "Add another screen" })
    ).toBeInTheDocument();
    const clicked = fireEvent.click(
      getByText(dom.container, "Add another screen")
    );
    expect(clicked).toBeTruthy();
    /** Wait for popup to add Screen and check for that exist in the document */
    // await waitFor(() =>
    //   expect(getByRole(dom.container, "dialog")).toBeInTheDocument()
    // );
  });

  // it("Delete an existing screen", async () => {
  //   const clicked = fireEvent.click(
  //     getByRole(dom.container, "button", { name: "" })
  //   );
  //   expect(clicked).toBeTruthy();

  //   /** Wait for popup to add Screen and check for that exist in the document */
  //   // await waitFor(() =>
  //   //   expect(getByRole(dom.container, "dialog")).toBeInTheDocument()
  //   // );
  //   const clickedDeleteBtn = fireEvent.click(
  //     getByText(dom.container, "Delete Screen")
  //   );
  //   expect(clickedDeleteBtn).toBeTruthy();

  //   /** Fire an event to click the "Yes, delete Screen" and check that particular screen is not in the dom */
  // });
});
