import React, { useContext } from "react";
import { Dropdown } from "react-bootstrap";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { FlowContext } from "../../context/Context";
import { faEllipsisH, faGripVertical } from "@fortawesome/free-solid-svg-icons";
import AddSection from "./Modals/AddSection";
import AddScreen from "./Modals/AddScreen";
import DeleteButton from "../Modals/DeleteButton";
import { FormattedMessage } from "react-intl";

const intlPrefix = "onboarding.flows.sectionHeader";

type Props = {
  sectionKey: string;
  icon?: string;
  title?: string;
  position?: number;
  description?: string;
  itemCount?: number;
  titleTranslations?: Record<string, string>;
  descriptionTranslations?: Record<string, string>;
};

const SectionHeader = ({
  sectionKey,
  icon,
  title,
  description,
  position,
  itemCount,
  titleTranslations,
  descriptionTranslations,
}: Props) => {
  const { flowDispatch: flowDispatch } = useContext(FlowContext);

  const handleRemoveColumn = () => {
    flowDispatch({
      type: "REMOVE_SECTION",
      payload: { key: sectionKey },
    });
    return true;
  };

  const handleEditSectionModal = () => {
    flowDispatch({
      type: "OPEN_FLOW_MODAL",
      payload: {
        isSteppable: false,
        modalTitle: "Edit section",
        children: (
          <AddSection
            baseSection={{
              key: sectionKey,
              icon,
              title,
              description,
              position,
              titleTranslations,
              descriptionTranslations,
            }}
            isEdit={true}
          />
        ),
        step: 2,
        type: "editSection",
        size: "xl",
      },
    });
  };

  const handleAddScreen = () => {
    flowDispatch({
      type: "OPEN_FLOW_MODAL",
      payload: {
        modalTitle: "Add screen",
        children: <AddScreen sectionKey={sectionKey} />,
        size: "xl",
        step: 1,
        type: "addScreen",
      },
    });
  };

  return (
    <div>
      <div className="kanban-column-header">
        <h5 className="fs-0 mb-0">
          <span>
            <FontAwesomeIcon icon={faGripVertical} className="text-500" />
          </span>{" "}
          {title}{" "}
          {!!itemCount && <span className="text-500">({itemCount})</span>}
        </h5>
        <Dropdown align="end" className="font-sans-serif btn-reveal-trigger">
          <Dropdown.Toggle variant="reveal" size="sm" className="py-0 px-2">
            <FontAwesomeIcon icon={faEllipsisH} />
          </Dropdown.Toggle>

          <Dropdown.Menu className="py-0">
            <Dropdown.Item onClick={handleEditSectionModal}>
              <FormattedMessage id={`${intlPrefix}.editSection`} />
            </Dropdown.Item>
            <Dropdown.Item onClick={handleAddScreen}>
              <FormattedMessage id={`${intlPrefix}.addScreen`} />
            </Dropdown.Item>
            <Dropdown.Item className="text-danger">
              <DeleteButton
                deleteUrl=""
                title="Section"
                btnClass="flow-section-dropdown-item"
                handleDeleteExternally={handleRemoveColumn}
              >
                <FormattedMessage id={`${intlPrefix}.deleteSection`} />
              </DeleteButton>
            </Dropdown.Item>
          </Dropdown.Menu>
        </Dropdown>
      </div>
      <div className=" kanban-column-header kanban-sub-header">
        {description}
      </div>
      <div />
    </div>
  );
};

export default SectionHeader;
