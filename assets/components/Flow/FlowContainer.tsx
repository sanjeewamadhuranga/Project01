import React, { useContext, useEffect, useRef } from "react";
import Section from "./Section";
import IconButton from "../Common/IconButton";
import is from "is_js";
import { FlowContext } from "../../context/Context";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import AddSection from "./Modals/AddSection";
import FlowModal from "./Modals/FlowModal";
import { FormattedMessage } from "react-intl";

const intlPrefix = "onboarding.flows";

const grid = 8;

const getListStyle = (isDraggingOver) => ({
  background: isDraggingOver ? "lightblue" : "lightgrey",
  display: "flex",
  padding: grid,
  overflow: "auto",
});

const getItemStyle = (isDragging: boolean, draggableStyle) => ({
  userSelect: "none",
  padding: grid * 2,
  margin: `0 ${grid}px 0 0`,

  /** change background colour if dragging */
  background: isDragging ? "lightgreen" : "grey",

  /** styles we need to apply on draggables */
  ...draggableStyle,
});

const FlowContainer = () => {
  const {
    flowState: { sections },
    flowDispatch: flowDispatch,
  } = useContext(FlowContext);

  const containerRef = useRef<null | HTMLDivElement>(null);

  useEffect(() => {
    if (is.ipad()) {
      containerRef?.current?.classList.add("ipad");
    }

    if (is.mobile()) {
      containerRef?.current?.classList.add("mobile");
      if (is.safari()) {
        containerRef?.current?.classList.add("safari");
      }
      if (is.chrome()) {
        containerRef?.current?.classList.add("chrome");
      }
    }
  }, []);

  const getColumn = (key) => {
    return sections?.find((item) => item.key === key);
  };

  const reorderArray = (array, fromIndex, toIndex) => {
    const newArr = [...array];

    const chosenItem = newArr.splice(fromIndex, 1)[0];
    newArr.splice(toIndex, 0, chosenItem);

    return newArr;
  };

  const move = (source, destination) => {
    const sourceColumn = getColumn(source.droppableId);
    const targetColumn = getColumn(destination.droppableId);
    const sourceItemsClone = [
      ...(!!sourceColumn && sourceColumn.screens ? sourceColumn.screens : []),
    ];
    const destItemsClone = [
      ...(!!targetColumn && targetColumn.screens ? targetColumn.screens : []),
    ];

    const [removedItem] = sourceItemsClone.splice(source.index, 1);
    destItemsClone.splice(destination.index, 0, removedItem);

    return {
      updatedDestItems: destItemsClone,
      updatedSourceItems: sourceItemsClone,
    };
  };

  const handleDragEnd = (result) => {
    const { source, destination } = result;

    if (!destination) {
      return;
    }

    /** If dragged within the same column */
    if (
      source.droppableId === destination.droppableId &&
      source?.droppableId !== "droppable"
    ) {
      const column = getColumn(source.droppableId);
      const screens = column && "screens" in column ? column.screens : [];
      const reorderedItems = reorderArray(
        screens,
        source.index,
        destination.index
      );

      flowDispatch({
        type: "UPDATE_SECTION",
        payload: { column, reorderedItems },
      });
      flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    } else if (
      source?.droppableId === destination.droppableId &&
      source?.droppableId === "droppable"
    ) {
      const reorderedItems = reorderArray(
        sections,
        source.index,
        destination.index
      );

      flowDispatch({
        type: "UPDATE_SECTIONS",
        payload: { reorderedItems },
      });
      flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    } else {
      /**  If dragged within different columns */
      const sourceColumn = getColumn(source.droppableId);
      const destColumn = getColumn(destination.droppableId);
      const movedItems = move(source, destination);

      flowDispatch({
        type: "UPDATE_DUAL_SECTION",
        payload: {
          sourceColumn,
          updatedSourceItems: movedItems.updatedSourceItems,
          destColumn,
          updatedDestItems: movedItems.updatedDestItems,
        },
      });
      flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    }
  };

  const openAddSectionModal = () => {
    flowDispatch({
      type: "OPEN_FLOW_MODAL",
      payload: {
        modalTitle: "Add section",
        children: <AddSection isEdit={false} />,
        size: "xl",
        step: 1,
        type: "addSection",
      },
    });
  };

  function getStyle(style, snapshot) {
    if (!snapshot.isDropAnimating) {
      return style;
    }
    const { moveTo, curve, duration } = snapshot.dropAnimation;
    /** move to the right spot */
    const translate = `translate(${moveTo.x}px, ${moveTo.y}px)`;
    return {
      ...style,
      transform: `${translate}`,
      /** slowing down the drop */
      transition: `all ${curve} ${duration + 1}s`,
    };
  }
  return (
    <DragDropContext onDragEnd={handleDragEnd}>
      <Droppable droppableId="droppable" direction="horizontal">
        {(provided, snapshot) => (
          <div
            ref={provided.innerRef}
            style={getListStyle(snapshot.isDraggingOver)}
            {...provided.droppableProps}
            className="kanban-container p-0"
          >
            {sections?.map((section, index) => (
              <Draggable
                key={section?.key}
                draggableId={section?.key}
                index={index}
              >
                {(_provided, _snapshot) => (
                  <div
                    ref={_provided.innerRef}
                    {..._provided.draggableProps}
                    {..._provided.dragHandleProps}
                    style={getStyle(_provided.draggableProps.style, _snapshot)}
                  >
                    <Section section={section} />
                  </div>
                )}
              </Draggable>
            ))}

            <FlowModal />
            {provided.placeholder}
            <div className="kanban-column">
              <IconButton
                variant="secondary"
                className="d-block w-100 border-400 bg-400 kanban-primary-button"
                icon={faPlus}
                iconClassName="me-1"
                onClick={openAddSectionModal}
              >
                <FormattedMessage id={`${intlPrefix}.addAnotherSection`} />
              </IconButton>
            </div>
          </div>
        )}
      </Droppable>
    </DragDropContext>
  );
};

export default FlowContainer;
