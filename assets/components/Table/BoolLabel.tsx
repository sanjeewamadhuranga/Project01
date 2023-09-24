import React from "react";
import { Badge } from "react-bootstrap";
import { FormattedMessage } from "react-intl";

interface Props {
  value: boolean;
}

const BoolLabel = (props: Props): JSX.Element => (
  <Badge
    pill
    bg=""
    className={props.value ? "badge-soft-success" : "badge-soft-danger"}
  >
    {props.value ? (
      <FormattedMessage id="common.answer.yes" />
    ) : (
      <FormattedMessage id="common.answer.no" />
    )}
  </Badge>
);

export default BoolLabel;
