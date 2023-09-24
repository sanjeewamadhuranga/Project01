import React from "react";
import { Badge } from "react-bootstrap";
import { FormattedMessage } from "react-intl";

const DefaultLabel = (): JSX.Element => (
  <Badge pill bg="" className={"badge-soft-success"}>
    <FormattedMessage id="common.default" />
  </Badge>
);

export default DefaultLabel;
