import React from "react";
import { Badge } from "react-bootstrap";
import { useIntl } from "react-intl";
import { DisputeState } from "./DisputeList";

interface Props {
  state: DisputeState;
}

const DisputeStateLabel = (props: Props): JSX.Element => {
  const intl = useIntl();
  const options = {
    [DisputeState.new]: {
      label: intl.formatMessage({ id: "compliance.disputes.new" }),
      className: "badge-soft-secondary",
    },
    [DisputeState.processing]: {
      label: intl.formatMessage({ id: "compliance.disputes.processing" }),
      className: "badge-soft-info",
    },
    [DisputeState.closed]: {
      label: intl.formatMessage({ id: "compliance.disputes.closed" }),
      className: "badge-soft-success",
    },
  };
  return (
    <Badge
      pill
      className={options[props.state].className}
      bg=""
      data-testid="state"
    >
      {options[props.state].label}
    </Badge>
  );
};

export default DisputeStateLabel;
