import React, { useContext } from "react";
import { Button, Col, Form, Row } from "react-bootstrap";
import { FlowContext } from "../../../context/Context";
import { useInput } from "./../../../hooks/formInputHook";
import { toast } from "react-toastify";
import { FormattedMessage, useIntl } from "react-intl";

const intlPrefix = "onboarding.flows.modals";

/** Dispath the action to update the screen values */

/** Modal to Edit screen */
const CreateFlow = ({ flowName, flowKey }: any) => {
  const { flowDispatch } = useContext(FlowContext);

  const { value: name, bind: bindName } = useInput(flowName);
  const { value: key, bind: bindKey } = useInput(flowKey);
  const intl = useIntl();

  const handleCreateFlow = (e) => {
    flowDispatch({ type: "FLOW_NOT_PUBLISHED" });
    flowDispatch({
      type: "TOGGLE_FLOW_MODAL",
    });
    toast.success(intl.formatMessage({ id: "common.successCreated" }));
  };

  return (
    <>
      <div className="bg-light rounded-top-lg px-4 py-3">
        <Row>
          <Form>
            <Form.Group className="mb-3" controlId="formBasicEmail">
              <Form.Label>Name*</Form.Label>
              <Form.Control
                {...bindName}
                defaultValue={name}
                type="text"
                placeholder="Enter the key"
                required
              />
            </Form.Group>

            <Form.Group className="mb-3" controlId="formBasicEmail">
              <Form.Label>Key*</Form.Label>
              <Form.Control
                {...bindKey}
                defaultValue={key}
                type="text"
                placeholder="Enter the title"
                required
              />
            </Form.Group>
          </Form>
        </Row>
      </div>
      <div className="rounded-top-lg px-4 py-3 float-end">
        <Row>
          <Col lg={9}>
            <Button
              size="sm"
              className="kanban-primary-button"
              type="submit"
              onClick={handleCreateFlow}
            >
              <FormattedMessage id={`${intlPrefix}.createFlow`} />
            </Button>
          </Col>
        </Row>
      </div>
    </>
  );
};

export default CreateFlow;
