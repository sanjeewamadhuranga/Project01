import React from "react";
import { Button } from "react-bootstrap";
import Routing from "../../services/routing";
import { FormattedMessage } from "react-intl";

interface RouteProps {
  id: string;
  routeNamePrefix: string;
}

interface UrlProps {
  url: string;
}

const DetailsButton = (props: RouteProps | UrlProps) => (
  <Button
    href={
      "url" in props
        ? props.url
        : Routing.generate(`${props.routeNamePrefix}_show`, {
            id: props.id,
          })
    }
    variant="falcon-default"
    size="sm"
  >
    <FormattedMessage id="common.actions.details" />
  </Button>
);

export default DetailsButton;
