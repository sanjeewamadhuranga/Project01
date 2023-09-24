import React, { useContext, useEffect, useRef, useState } from "react";
import SectionHeader from "./SectionHeader";
import Screen from "./Screen/Screen";
import AddAnotherForm from "./AddAnotherForm";
import { Droppable } from "react-beautiful-dnd";
import IconButton from "../Common/IconButton";
import classNames from "classnames";
import { FlowContext } from "../../context/Context";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import AddScreen from "./Modals/AddScreen";
import { Section as ISection, SectionType } from "../../models/onboarding";
import { FormattedMessage } from "react-intl";

const intlPrefix = "onboarding.flows.sections";

type Props = {
  section: ISection;
};

const Section = ({ section }: Props) => {
  const {
    key,
    icon,
    title,
    screens,
    position,
    description,
    titleTranslations,
    descriptionTranslations,
  } = section;
  const [showForm, setShowForm] = useState(false);
  const formViewRef = useRef<null | HTMLDivElement>(null);
  const { flowDispatch } = useContext(FlowContext);

  const handleSubmit = (screenData) => {
    const isEmpty = !Object.keys(screenData).length;

    if (!isEmpty) {
      const newScreen = {
        title: screenData?.title,
        key: screenData?.key,
      };

      flowDispatch({
        type: "ADD_SCREEN",
        payload: { newScreen, sectionKey: key },
      });
      setShowForm(false);
    }
  };

  const handleAddScreen = () => {
    flowDispatch({
      type: "OPEN_FLOW_MODAL",
      payload: {
        modalTitle: "Add screen",
        children: <AddScreen sectionKey={key} />,
        size: "xl",
        step: 1,
        type: "addScreen",
      },
    });
  };

  useEffect(() => {
    const timeout = setTimeout(() => {
      formViewRef?.current?.scrollIntoView({ behavior: "smooth" });
    }, 500);

    return clearTimeout(timeout);
  }, [showForm]);

  const isAddScreenAllowed = (sectionKey) => {
    return (
      sectionKey !== SectionType.CONFIRM_IDENTITY &&
      sectionKey !== SectionType.BANK_ACCOUNT
    );
  };

  return (
    <div className={classNames("kanban-column", { "form-added": showForm })}>
      <SectionHeader
        sectionKey={key}
        icon={icon}
        title={title}
        description={description}
        position={position}
        itemCount={screens?.length}
        titleTranslations={titleTranslations}
        descriptionTranslations={descriptionTranslations}
      />
      <Droppable
        isDropDisabled={!isAddScreenAllowed(key)}
        droppableId={`${key}`}
        type="KANBAN"
      >
        {(provided) => (
          <>
            <div
              ref={provided.innerRef}
              {...provided.droppableProps}
              id={`container-${key}`}
              className="kanban-items-container scrollbar"
            >
              {screens &&
                screens.map((screen, index) => (
                  <Screen
                    sectionKey={key}
                    key={screen.key}
                    index={index}
                    screen={screen}
                  />
                ))}
              {
                <AddAnotherForm
                  onSubmit={handleSubmit}
                  type="card"
                  showForm={showForm}
                  setShowForm={setShowForm}
                />
              }
              {provided.placeholder}
              <div ref={formViewRef} />
            </div>
            {!showForm && isAddScreenAllowed(section?.key) && (
              <div
                className="kanban-column-footer"
                data-testid="add-screen-button"
              >
                <IconButton
                  size="sm"
                  variant="link"
                  className="d-block w-100 btn-add-card text-decoration-none text-600"
                  icon={faPlus}
                  iconClassName="me-2"
                  onClick={handleAddScreen}
                >
                  <FormattedMessage id={`${intlPrefix}.addAnotherScreen`} />
                </IconButton>
              </div>
            )}
          </>
        )}
      </Droppable>
    </div>
  );
};

export default Section;
