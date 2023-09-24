import React from "react";
import Screen from "./Screen";
import { renderWithIntl } from "../../../helpers/testHelpers";
import { fireEvent, screen } from "@testing-library/react";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";

const mockScreen = {
  key: "DIALOG_TYPE_OF_SERVICE",
  title: "What services do you require?",
  description: "What services do you require?",
};

describe("<Screen /> rendering without dependencies", () => {
  test("render screens", () => {
    //arrange
    //act
    renderWithIntl(
      <DragDropContext>
        <Droppable droppableId="droppable" direction="horizontal">
          {() => (
            <Draggable
              key="1"
              draggableId="screen-DIALOG_TYPE_OF_SERVICE"
              index={0}
            >
              {(provided) => (
                <div ref={provided.innerRef}>
                  <Screen
                    screen={mockScreen}
                    index={"index123"}
                    sectionKey={"Key123"}
                  />
                </div>
              )}
            </Draggable>
          )}
        </Droppable>
      </DragDropContext>
    );
    //assert
    expect(screen.getByText("DIALOG_TYPE_OF_SERVICE")).toBeInTheDocument();
    fireEvent.click(screen.getByText("DIALOG_TYPE_OF_SERVICE"));
  });
});
