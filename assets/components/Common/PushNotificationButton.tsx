import React from "react";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import { useIntl } from "react-intl";

interface Props {
  url: string;
}

const intlPrefix = "merchant.merchants.user.action";

const PushNotificationButton = (props: Props) => {
  const intl = useIntl();
  return (
    <OverlayTrigger
      overlay={
        <Tooltip>
          {intl.formatMessage({
            id: `${intlPrefix}.pushNotification`,
            defaultMessage: "Send push notification",
          })}
        </Tooltip>
      }
    >
      <a className={"btn btn-sm"} href={props.url}>
        <span className="fas fa-comment-alt" />
      </a>
    </OverlayTrigger>
  );
};

export default PushNotificationButton;
