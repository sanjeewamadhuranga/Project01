import React, { useContext } from "react";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import { useIntl } from "react-intl";
import axios from "axios";
import { toast } from "react-toastify";
import TableContext from "../Table/TableContext";

export interface Props {
  routePrefix: string;
  id: string;
  status?: boolean;
}

const intlPrefix = "onboarding.tipAndTricks";

const ToggleStatusButton = (props: Props) => {
  const intl = useIntl();
  const table = useContext(TableContext);

  const makeToggleRequest = (): void => {
    axios
      .put(`${props.routePrefix}/${props.id}/status/${Number(!props.status)}`)
      .then(() =>
        toast.success(
          intl.formatMessage({
            id: props.status
              ? `${intlPrefix}.successfullyDeactivated`
              : `${intlPrefix}.successfullyActivated`,
          })
        )
      )
      .catch(() =>
        toast.error(intl.formatMessage({ id: `${intlPrefix}.toggleError` }))
      )
      .finally(table.reload);
  };

  return (
    <OverlayTrigger
      overlay={
        <Tooltip>
          {props.status
            ? intl.formatMessage({
                id: `${intlPrefix}.deactivate`,
                defaultMessage: "Deactivate",
              })
            : intl.formatMessage({
                id: `${intlPrefix}.activate`,
                defaultMessage: "Activate now",
              })}
        </Tooltip>
      }
    >
      <a role="button" className={"btn btn-sm"} onClick={makeToggleRequest}>
        <span
          className={props.status ? "fas fa-toggle-off" : "fas fa-toggle-on"}
        />
      </a>
    </OverlayTrigger>
  );
};

export default ToggleStatusButton;
