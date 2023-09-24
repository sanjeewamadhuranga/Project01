import React, { PropsWithChildren } from "react";
import { ButtonGroup } from "react-bootstrap";
import SmsButton from "./SmsButton";
import PushNotificationButton from "./PushNotificationButton";
import EmailNotificationButton from "./EmailNotificationButton";

interface Props {
  smsUrl: string;
  pushNotificationUrl: string;
  emailNotificationUrl: string;
}

const NotificationAction = (props: PropsWithChildren<Props>) => (
  <ButtonGroup>
    <SmsButton sub={props.smsUrl} />
    <PushNotificationButton url={props.pushNotificationUrl} />
    <EmailNotificationButton url={props.emailNotificationUrl} />
  </ButtonGroup>
);

export default NotificationAction;
