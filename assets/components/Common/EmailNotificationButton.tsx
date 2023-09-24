import React from "react";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import { useIntl } from "react-intl";

interface Props {
  url: string;
}

const intlPrefix = "merchant.merchants.user.action";

const EmailNotificationButton = (props: Props) => {
  const intl = useIntl();
  return (
    <OverlayTrigger
      overlay={
        <Tooltip>
          {intl.formatMessage({
            id: `${intlPrefix}.emailNotification`,
            defaultMessage: "Send email notification",
          })}
        </Tooltip>
      }
    >
      <a className={"btn btn-sm"} href={props.url}>
        <span className="fas fa-envelope-open-text" />
      </a>
    </OverlayTrigger>
  );
};

export default EmailNotificationButton;
