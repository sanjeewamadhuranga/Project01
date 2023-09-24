import React, { useContext } from "react";
import { Nav } from "react-bootstrap";
import { screensList } from "../../data/flowData";
import { FlowContext } from "../../context/Context";
import { getAllScreenLength } from "../../utils/arrays";
import { FormattedMessage } from "react-intl";

const intlPrefix = "onboarding.flows.modals.sidebar";

const ModalSidebar = () => {
  const { flowDispatch } = useContext(FlowContext);

  const handleCategorySelection = (categoryKey: string | undefined) => {
    flowDispatch({
      type: "ADDSCREEN_SELECT_CATEGORY",
      payload: { categoryKey },
    });
  };

  return (
    <div className="p-1 position-sticky top-0">
      <h6 className="p-0 modal-sub-title">Screens</h6>
      <div className="modal-sidebar-menu clear-row">
        <Nav className="row flex-lg-column fs--1">
          <Nav.Item
            className="me-2 me-lg-0"
            onClick={() => handleCategorySelection("ALL")}
          >
            <Nav.Link className="nav-link-card-details p-0 modal-sidebar-menu-item background-light">
              <span>
                <FormattedMessage id={`${intlPrefix}.allScreens`} />
              </span>
              <span className="modal-sidebar-menu-item-count">
                {getAllScreenLength(screensList)}
              </span>
            </Nav.Link>
          </Nav.Item>
        </Nav>
        {screensList?.map((item) => (
          <Nav key={item?.category} className="row flex-lg-column fs--1">
            <Nav.Item
              className="me-2 me-lg-0"
              onClick={() => handleCategorySelection(item?.category_key)}
            >
              <Nav.Link className="nav-link-card-details modal-sidebar-menu-item background-light">
                <span>{item?.category}</span>
                <span className="modal-sidebar-menu-item-count justify-content-end">
                  {item?.screens?.length}
                </span>
              </Nav.Link>
            </Nav.Item>
          </Nav>
        ))}
      </div>
    </div>
  );
};

export default ModalSidebar;
