import React, { useEffect, useRef, useState } from "react";
import PropTypes from "prop-types";
import { Col, Form, Row, Button } from "react-bootstrap";
import classNames from "classnames";
import { FormattedMessage } from "react-intl";

const intlPrefix = "onboarding.common.actions";

const AddAnotherForm = ({
  onSubmit: handleSubmit,
  type,
  showForm,
  setShowForm,
}) => {
  const [formData, setFormData] = useState({});
  const inputRef = useRef<null | any>(null);

  useEffect(() => {
    if (showForm) {
      inputRef?.current?.focus();
    }
  }, [showForm]);

  return (
    <>
      {showForm && (
        <div
          className={classNames("rounded-3 transition-none", {
            "bg-100 p-card": type === "list",
            "p-3 border bg-white mt-3": type === "card",
          })}
        >
          <Form
            onSubmit={(e) => {
              e.preventDefault();
              return handleSubmit(formData);
            }}
          >
            <Form.Control
              as="textarea"
              rows={2}
              className="mb-2"
              ref={inputRef}
              onChange={({ target }) =>
                setFormData({ ...formData, title: target.value })
              }
              placeholder={
                type === "list"
                  ? "Enter list title..."
                  : "Enter a title for this card..."
              }
            />
            <Row className="gx-2">
              <Col>
                <Button
                  variant="primary"
                  size="sm"
                  className="d-block w-100"
                  type="submit"
                >
                  <FormattedMessage id={`${intlPrefix}.add`} />
                </Button>
              </Col>
              <Col>
                <Button
                  variant="outline-secondary"
                  size="sm"
                  className="d-block w-100 border-400"
                  type="button"
                  onClick={() => setShowForm(false)}
                >
                  <FormattedMessage id={`${intlPrefix}.cancel`} />
                </Button>
              </Col>
            </Row>
          </Form>
        </div>
      )}
    </>
  );
};

AddAnotherForm.propTypes = {
  onSubmit: PropTypes.func,
  type: PropTypes.string,
  showForm: PropTypes.bool,
  setShowForm: PropTypes.func,
};

export default AddAnotherForm;
