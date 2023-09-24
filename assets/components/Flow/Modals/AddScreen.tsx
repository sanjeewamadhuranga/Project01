import React, { useContext, useState } from "react";
import { Card, Col, Row } from "react-bootstrap";
import { FlowContext } from "../../../context/Context";
import ModalSidebar from "../ModalSidebar";
import { screensList } from "../../../data/flowData";
import AddScreenForm from "./AddScreenForm";
import { FormattedMessage } from "react-intl";
import { getAllScreenObjects } from "../../../utils/arrays";
import { Screen } from "../../../models/onboarding";
import { BusinessCountryNames } from "../../../constants/general";

const newCard: Screen = {
  title: "New Screen",
  description: (
    <FormattedMessage id="onboarding.flows.screens.createNewScreen.description" />
  ),
  key: "NEW",
};

const getCardBodyStyle = (key, isScreenUsed) => {
  const commonClass = "align-content-between flex-wrap ";
  if (key === "NEW") return `align-items-center`;
  return isScreenUsed
    ? `${commonClass} inactive-card-with-color`
    : `${commonClass} `;
};

const ScreenCard = (
  index: number,
  handleSelectScreen: (screen: Screen) => void,
  screen: Screen,
  isScreenUsed?: boolean
): JSX.Element => {
  return (
    <Card
      key={index}
      className={`kanban-section-type-card-add-screen hover-actions-trigger ${
        isScreenUsed ? "inactive-card" : ""
      }`}
      onClick={() => handleSelectScreen(screen)}
    >
      <Card.Body
        className={`d-flex kanban-screen-type-cards ${getCardBodyStyle(
          screen?.key,
          isScreenUsed
        )}`}
      >
        <span>
          <p className="mb-0 fw-medium font-sans-serif stretched-link fs--1">
            {screen?.title}
          </p>
          <div className="mt-0 kanban-screen-desc">
            {screen?.key !== "NEW" && <div>Key: {screen?.key}</div>}
            <div>{screen?.description}</div>
          </div>
        </span>

        {screen?.countries &&
          screen?.countries?.map((item: string, index) => (
            <>
              <span
                className={`mt-3 badge bg-light text-dark country-label-${item?.toLowerCase()}`}
                key={index}
              >
                {BusinessCountryNames[item]}
              </span>
            </>
          ))}
        {screen?.key === "NEW" && (
          <span>
            <i className="fas fa-chevron-right mx-2"></i>
          </span>
        )}
      </Card.Body>
    </Card>
  );
};

/** //WIP Dynamic screenList handling 
const filterScreenList = (screensList: Array<AddScreenListType>) => {
  const list = _.filter(screensList, (item) => {
    return _.filter(item.screens, (screen) => {
      return (
        screen.tenant_keys?.includes(window?.APP_SETTINGS?.branding?.theme) ||
        screen.tenant_keys?.length === 0
      );
    });
  });

  console.log("list: #####: ", list);

  return list;
};
*/

interface Prop {
  sectionKey: string;
}

/** Modal to Add screen */
const AddScreen = ({ sectionKey }: Prop): JSX.Element => {
  /** //WIP Dynamic screenList handling
  const [filteredScreenList, setFilteredScreenList] = useState([]);

  useEffect(() => {
    const list = filterScreenList(screensList);
    // setFilteredScreenList(list);
  }, []);
  */

  const {
    flowState: {
      sections,
      flowModal: { addScreen, step },
    },
    flowDispatch,
  } = useContext(FlowContext);

  const [selectedScreenTemplate, setSelectedScreenTemplate] = useState<Screen>({
    key: "",
    description: undefined,
    title: undefined,
  });

  const handleSelectScreen = (screen) => {
    if (isScreenUsed(screen)) {
      return;
    }
    setSelectedScreenTemplate(screen);
    flowDispatch({
      type: "CHANGE_STEP_FLOW_MODAL",
      payload: 2,
    });
  };

  const isScreenUsed = (newScreen: Screen) => {
    return getAllScreenObjects(sections).find(
      (screen) => screen?.key === newScreen?.key
    );
  };

  return (
    <div>
      {step == 1 ? (
        <Row className="w-100 m-0">
          <Col md={4} sm={4} className="p-3 background-light">
            <ModalSidebar />
          </Col>
          <Col md={8} sm={8} className="p-3 bg-white min-vh-100">
            <Row className="m-3">
              <div className="modal-sub-title">
                <FormattedMessage id="onboarding.flows.screens.createNewScreen" />
              </div>
              <div className="d-flex flex-wrap p-0">
                {ScreenCard(1, handleSelectScreen, newCard)}
              </div>
            </Row>
            <Row className="m-3">
              <div className="modal-sub-title">
                <FormattedMessage id="onboarding.flows.screens.selectAScreen" />
              </div>
              <div className="d-flex flex-wrap p-0">
                {screensList?.map((item) => {
                  if (
                    addScreen?.selectedCategory === "ALL" ||
                    item?.category_key === addScreen?.selectedCategory
                  ) {
                    return item?.screens?.map((screen, index) => {
                      /* //Commenting out since we need to switch off filtering for testing 
                      if (!isScreenBelongToTenant(screen)) return; 
                      */
                      if (
                        screen.description
                          ?.toString()
                          .toLowerCase()
                          .indexOf(addScreen?.searchInput ?? "") > -1
                      ) {
                        return ScreenCard(
                          index,
                          handleSelectScreen,
                          screen,
                          isScreenUsed(screen)
                        );
                      }
                    });
                  }
                })}
              </div>
            </Row>
          </Col>
        </Row>
      ) : (
        <AddScreenForm
          isScreenUsed={isScreenUsed(selectedScreenTemplate)}
          selectedScreenTemplate={selectedScreenTemplate}
          sectionKey={sectionKey}
        />
      )}
    </div>
  );
};

export default AddScreen;
