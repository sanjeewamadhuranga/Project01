import React, { useContext } from "react";
import { Row, Col, Button, Badge } from "react-bootstrap";
import IconButton from "../Common/IconButton";
import Flex from "../Common/Flex";
import { faPlus, faPencil, faCircle } from "@fortawesome/free-solid-svg-icons";
import { FlowContext } from "../../context/Context";
import EditFlow from "./Modals/EditFlow";
import AddSection from "./Modals/AddSection";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import Routing from "../../services/routing";
import axios from "axios";
import { toast } from "react-toastify";
import ToastMsg from "../Common/ToastMsg";
import { FormattedMessage } from "react-intl";

const intlPrefix = "onboarding.flows.header";

const FlowHeader = () => {
  const {
    flowState: {
      name,
      key,
      id,
      default: isDefault,
      isPublished,
      sections,
      locales,
    },
    flowDispatch,
  } = useContext(FlowContext);

  const handleFlowEdit = () => {
    flowDispatch({
      type: "OPEN_FLOW_MODAL",
      payload: {
        modalTitle: "Edit",
        children: (
          <EditFlow
            flowKey={key}
            name={name}
            isDefault={isDefault}
            locales={locales}
          />
        ),
        type: "editFlow",
        size: "xl",
      },
    });
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

  const handlePublish = async () => {
    try {
      const suffix = !!id ? `/${id}` : "";
      const data = {
        key,
        name,
        default: isDefault,
        sections,
        locales: locales,
      };
      await axios({
        url: `/onboarding/flows${suffix}`,
        method: !!id ? "PUT" : "POST",
        data,
      });

      toast.success(`Successfully ${!!id ? "updated" : "created"}!`);
      window.location.href = "/onboarding/flows";
    } catch (e: any) {
      let { title, detail } = e.response.data;
      if (!title || !detail) {
        title = "Error occured";
        detail = "Please check if all fields are filled";
      }
      toast.error(<ToastMsg title={title} msg={detail} />);
    }
  };

  return (
    <Row className="gx-0 kanban-header rounded-2 px-card py-2 mt-2 mb-3">
      <Col className="d-flex align-items-center">
        <a
          className="pe-2 text-900"
          href={Routing.generate("onboarding_flows_index")}
        >
          <div className="fas fa-chevron-left" />
        </a>
        <h5 className="mb-0">{name}</h5>
        {isDefault ? (
          <Badge className="mx-3 kanban-flow-lable" bg="secondary">
            <FormattedMessage id={`${intlPrefix}.defaultLableText`} />
          </Badge>
        ) : null}
      </Col>
      <Col xs="auto" as={Flex} alignItems="center">
        <div className="mx-3">
          <div className="kanban-flow-status-text">
            <FormattedMessage id={`${intlPrefix}.statusText`} />
          </div>
          <div className="fw-bold kanban-flow-status-indicator">
            <FontAwesomeIcon
              size="sm"
              color={isPublished ? "green" : "#f68f57"}
              icon={faCircle}
            />{" "}
            {isPublished ? "Published" : "Unpublished"}
          </div>
        </div>
        <div onClick={handleFlowEdit}>
          <IconButton
            variant="falcon-default"
            size="sm"
            icon={faPencil}
            iconClassName="me-2"
            className="me-2 d-none d-md-block"
          >
            <FormattedMessage id={`${intlPrefix}.editButtonText`} />
          </IconButton>
        </div>

        <IconButton
          variant="falcon-default"
          size="sm"
          icon={faPlus}
          iconClassName="me-2"
          className="me-2 d-none d-md-block"
          onClick={openAddSectionModal}
        >
          <FormattedMessage id={`${intlPrefix}.addSection`} />
        </IconButton>
        <Button
          size="sm"
          className="me-2 d-none d-md-block kanban-primary-button"
          onClick={handlePublish}
        >
          <FormattedMessage id={`${intlPrefix}.publish`} />
        </Button>
      </Col>
    </Row>
  );
};

export default FlowHeader;
