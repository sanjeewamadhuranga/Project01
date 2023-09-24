import React, { useCallback, useContext, useMemo, useState } from "react";
import { CloseButton, InputGroup, Modal } from "react-bootstrap";
import { FormattedMessage, useIntl } from "react-intl";
import { FlowContext } from "../../../context/Context";
import { DebounceInput } from "react-debounce-input";
import {
  clearSearchValues,
  updateSearchValues,
} from "../../../services/search";

const intlPrefix = "onboarding.flows.modals";

const FlowModal = () => {
  const intl = useIntl();
  const [search, setSearch] = useState<string>("");
  const {
    flowState: { flowModal },
    flowDispatch: flowDispatch,
  } = useContext(FlowContext);

  const { show, size, step, type, isSteppable, modalContent } = flowModal;

  const handleClose = () => {
    flowDispatch({ type: "TOGGLE_FLOW_MODAL" });
  };

  const handleGoBack = useCallback(() => {
    flowDispatch({
      type: "CHANGE_STEP_FLOW_MODAL",
      payload: step && step > 1 ? step - 1 : 1,
    });
  }, [flowDispatch, step]);

  const handleSearchInput = (searchValue) => {
    setSearch(searchValue);
    flowDispatch({
      type: "UPDATE_SEARCH_INPUT",
      payload: { search: searchValue },
    });
  };

  const headerTitle = useMemo(() => {
    if (!step) {
      return modalContent.modalTitle;
    }

    if ((step === 1 && !!type) || type === "editScreen") {
      return <FormattedMessage id={`onboarding.flows.modals.${type}`} />;
    } else if (step > 1 && isSteppable) {
      return (
        <div onClick={handleGoBack}>
          <i className="fas fa-chevron-left me-2"></i>
          <FormattedMessage id={`onboarding.flows.modals.${type}`} />
        </div>
      );
    }

    return modalContent.modalTitle;
  }, [step, type, isSteppable, handleGoBack]);

  return (
    <Modal
      show={show}
      size={size ?? "lg"}
      onHide={handleClose}
      contentClassName="border-0 overflow-hidden"
      aria-labelledby="contained-modal-title-vcenter"
      className="kanban-modal"
    >
      {/* Modal Header */}
      <Modal.Header className="font-weight-bold border-bottom-0">
        <div>{headerTitle}</div>
        <div className="position-sticky top-0 end-0 mt-3 me-3 z-index-1 d-flex align-items-center">
          {step && step === 1 && type === "addScreen" ? (
            <InputGroup size="sm" className="mx-4 kanban-modal-search">
              <InputGroup.Text className="bg-transparent">
                <span className="fa fa-search text-600" />
              </InputGroup.Text>
              <DebounceInput
                placeholder={intl.formatMessage({
                  id: `${intlPrefix}.addScreen.search.placeHolder`,
                  defaultMessage: "Search for a Screen",
                })}
                className="form-control"
                debounceTimeout={0}
                value={search}
                forceNotifyByEnter={true}
                forceNotifyOnBlur={true}
                minLength={0}
                onChange={(e) => {
                  if (!e.target.value) {
                    clearSearchValues();
                  } else {
                    updateSearchValues({ search: e.target.value }, "filters");
                  }
                  handleSearchInput(e.target.value);
                }}
              />
            </InputGroup>
          ) : null}
          <CloseButton className="m-0 p-1" onClick={handleClose} />
        </div>
      </Modal.Header>

      {/* Modal Body */}
      <Modal.Body className="bg-light p-0 overflow-scroll">
        <div className="rounded-top-lg p-0">
          {flowModal?.modalContent?.children}
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default FlowModal;
