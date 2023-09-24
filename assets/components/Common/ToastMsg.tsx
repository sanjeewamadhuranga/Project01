import React from "react";
import { FormattedMessage } from "react-intl";

interface Props {
  title?: string;
  msg?: string;
  labelId?: string;
  msgId?: string;
}

const ToastMsg = ({ labelId, title, msgId, msg }: Props) => (
  <div style={{ whiteSpace: "pre-line" }}>
    <h5 className="text-white mb-0 font-weight-bold">
      {title ? title : <FormattedMessage id={labelId} />}
    </h5>
    {msg ? msg : <FormattedMessage id={msgId} />}
  </div>
);

export default ToastMsg;
