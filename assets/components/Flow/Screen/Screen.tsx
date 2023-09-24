import React, { useContext } from "react";
import { Card, OverlayTrigger, Tooltip } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { Draggable } from "react-beautiful-dnd";
import { FlowContext } from "../../../context/Context";
import { faCodeBranch, faTrashCan } from "@fortawesome/free-solid-svg-icons";
import EditScreen from "../Modals/EditScreen";
import { Screen as IScreen } from "../../../models/onboarding";
import DeleteButton from "../../Modals/DeleteButton";
import { FormattedMessage } from "react-intl";
import { DEFAULT_LOCALE } from "../../../constants/general";

const intlPrefix = "onboarding.flows.screens";

type Props = {
  screen: IScreen;
  index: number | string;
  sectionKey: string;
};

const Screen = ({
  screen: {
    title,
    key,
    description,
    dependencies,
    titleTranslations,
    descriptionTranslations,
  },
  index,
  sectionKey,
}: Props) => {
  const { flowDispatch } = useContext(FlowContext);

  const handleModalOpen = () => {
    flowDispatch({
      type: "OPEN_FLOW_MODAL",
      payload: {
        modalTitle: "Edit",
        children: (
          <EditScreen
            sectionKey={sectionKey}
            screenKey={key}
            description={description}
            title={title}
            dependencies={dependencies ?? []}
            titleTranslations={titleTranslations ?? {}}
            descriptionTranslations={descriptionTranslations ?? {}}
          />
        ),
        size: "xl",
        type: "editScreen",
      },
    });
  };

  // styles we need to apply on draggables
  const getItemStyle = (isDragging: boolean) => ({
    cursor: isDragging ? "grabbing" : "pointer",
  });

  const getStyle = (style, snapshot) => {
    if (!snapshot.isDropAnimating) {
      return style;
    }
    const { moveTo, curve, duration } = snapshot.dropAnimation;
    // move to the right spot
    const translate = `translate(${moveTo.x}px, ${moveTo.y}px)`;

    // patching the existing style
    return {
      ...style,
      transform: `${translate}`,
      transition: `all ${curve} ${duration + 0.3}s`,
    };
  };

  const handleScreenDelete = (_key) => {
    flowDispatch({
      type: "REMOVE_SCREEN",
      payload: { key: _key },
    });
    return true;
  };

  return (
    <Draggable draggableId={`screen-${key}`} index={index}>
      {(provided, snapshot) => (
        <div
          ref={provided.innerRef}
          {...provided.draggableProps}
          {...provided.dragHandleProps}
          style={getStyle(provided.draggableProps.style, snapshot)}
          className="kanban-item"
        >
          <Card
            style={getItemStyle(snapshot.isDragging)}
            className="kanban-item-card hover-actions-trigger"
            onClick={handleModalOpen}
          >
            <Card.Body>
              <p className="fw-bold font-sans-serif stretched-link">
                {(titleTranslations && titleTranslations[DEFAULT_LOCALE]) ??
                  title}
              </p>
              <div className="fw-light mt-0 mb-4 kanban-screen-description">
                {(descriptionTranslations &&
                  descriptionTranslations[DEFAULT_LOCALE]) ??
                  description}
              </div>
              <div className="d-flex align-items-center mt-4 mb-0">
                <span className="text-break p-0 flex-fill">{key}</span>
                {dependencies && dependencies[0]?.value?.length > 0 && (
                  <span className="align-items-end kanban-screen-icon-default">
                    <div className="d-flex p-3">
                      <span className="p-1">
                        <FontAwesomeIcon color="#2A7BE4" icon={faCodeBranch} />
                      </span>
                      <span className="p-1 kanban-screen-dependenciesCount">
                        {typeof dependencies[0]?.value === "string"
                          ? "1"
                          : dependencies[0]?.value?.length}
                      </span>
                    </div>
                  </span>
                )}
                <OverlayTrigger
                  placement="top"
                  overlay={
                    <Tooltip>
                      <FormattedMessage id={`${intlPrefix}.removeScreen`} />
                    </Tooltip>
                  }
                >
                  <span
                    className="align-items-end kanban-screen-icon z-index-2 btn-reveal-trigger"
                    onClick={(e) => e.stopPropagation()}
                  >
                    <DeleteButton
                      deleteUrl=""
                      title="Screen"
                      handleDeleteExternally={() => handleScreenDelete(key)}
                    >
                      <FontAwesomeIcon color="#e03024" icon={faTrashCan} />
                    </DeleteButton>
                  </span>
                </OverlayTrigger>
              </div>
            </Card.Body>
          </Card>
        </div>
      )}
    </Draggable>
  );
};

export default Screen;
