import React, { useState } from "react";
import { Button, Modal } from "react-bootstrap";
import GeneralModal from "./../../Modals/GeneralModal";
import axios from "axios";
import { FormattedMessage } from "react-intl";

export interface CloseDisputeButtonProps {
  id: string;
  onConfirmationClose: () => void;
}

const CloseDisputeButton = (props: CloseDisputeButtonProps) => {
  const [confirmationOpened, setConfirmationOpened] = useState<boolean>(false);
  const [waitingForResponse, setWaitingForResponse] = useState<boolean>(false);
  const closeDispute = () => {
    setWaitingForResponse(true);
    axios.post(`/compliance/disputes/${props.id}/close_dispute`).then(() => {
      setWaitingForResponse(false);
      setConfirmationOpened(false);
      props.onConfirmationClose();
      window.location.reload();
    });
  };

  return (
    <>
      <Button
        className={"btn btn-danger"}
        role={"button"}
        onClick={() => setConfirmationOpened(true)}
      >
        <FormattedMessage id="compliance.disputes.closeDispute" />
      </Button>
      <GeneralModal
        size="lg"
        show={confirmationOpened}
        hideModal={() => setConfirmationOpened(false)}
        header={"Confirm this action"}
      >
        <Modal.Body className={"text-center padding-5-em"}>
          <h2>
            <FormattedMessage id="compliance.disputes.closeDispute" />
          </h2>
          <div className="mb-4">
            <FormattedMessage
              id="compliance.disputes.ensureCloseDispute"
              values={{ br: <br /> }}
            />
          </div>
          <div>
            <Button
              className="btn btn-sm mx-3"
              onClick={() => closeDispute()}
              disabled={waitingForResponse}
            >
              <FormattedMessage id="compliance.disputes.yesClose" />
            </Button>
            <button
              className="btn btn-primary btn-sm mx-3"
              onClick={() => setConfirmationOpened(false)}
            >
              <FormattedMessage id="compliance.disputes.noGoBack" />
            </button>
          </div>
        </Modal.Body>
      </GeneralModal>
    </>
  );
};

export default CloseDisputeButton;
