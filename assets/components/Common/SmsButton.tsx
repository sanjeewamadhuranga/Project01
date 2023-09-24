import React from "react";
import { OverlayTrigger, Tooltip } from "react-bootstrap";
import { useIntl } from "react-intl";

interface Props {
  sub: string;
}

const intlPrefix = "merchant.merchants.user.action";

const SmsButton = (props: Props) => {
  const intl = useIntl();
  return (
    <OverlayTrigger
      overlay={
        <Tooltip>
          {intl.formatMessage({
            id: `${intlPrefix}.sms`,
            defaultMessage: "Send sms",
          })}
        </Tooltip>
      }
    >
      <a className={"btn btn-sm"} href={props.sub}>
        <span className="fas fa-sms" />
      </a>
    </OverlayTrigger>
  );
};

export default SmsButton;
