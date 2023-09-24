import React from "react";
import { Button } from "react-bootstrap";
import { FormattedMessage } from "react-intl";
import Routing from "../../services/routing";

interface Props {
  routeNamePrefix?: string;
  title?: string;
  id?: string;
}

const CreateButton = ({
  routeNamePrefix,
  title = "common.actions.create",
  ...props
}: Props) => (
  <Button
    href={Routing.generate(`${routeNamePrefix}_create`, {
      ...props,
    })}
    className="withsearch-button"
    variant="falcon-default"
    size="sm"
  >
    <i className="fas fa-plus" /> <FormattedMessage id={title} />
  </Button>
);

export default CreateButton;
