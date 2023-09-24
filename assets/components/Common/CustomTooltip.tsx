import React from "react";
import { faCircleQuestion } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { OverlayTrigger } from "react-bootstrap";
import Tooltip from "react-bootstrap/Tooltip";
import { useIntl } from "react-intl";
import { Placement } from "react-bootstrap/esm/types";

export interface Props {
  tooltip: string;
  placement?: Placement;
}

const CustomTooltip = (props: Props) => {
  const intl = useIntl();
  return (
    <>
      <OverlayTrigger
        trigger={"click"}
        placement={props?.placement ?? "bottom"}
        overlay={
          <Tooltip className="custom-tooltip">
            {intl.formatMessage({ id: props?.tooltip })}
          </Tooltip>
        }
        rootClose
      >
        <FontAwesomeIcon icon={faCircleQuestion} color="#2A7BE4" />
      </OverlayTrigger>
    </>
  );
};
export default CustomTooltip;
