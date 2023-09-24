import React, { PropsWithChildren, useState } from "react";
import { Button } from "react-bootstrap";
import axios, { AxiosResponse } from "axios";
import { toast } from "react-toastify";
import { FormattedMessage } from "react-intl";
import * as qs from "qs";
import { ReportModule } from "../../models/reports";

export interface Props {
  module: ReportModule;
  className?: string;
  urlParams?: Record<string, unknown>;
}

interface ReportRequestResponse {
  header: string;
  reason: string;
  isQueue: boolean;
}

const RequestReportButton = (props: PropsWithChildren<Props>) => {
  const [isLoading, setIsLoading] = useState(false);

  const makeReportRequest = (module: string): void => {
    setIsLoading(true);
    axios
      .post<null, AxiosResponse<ReportRequestResponse>>(
        `/report/request/${module}`,
        null,
        {
          params: props.urlParams,
          paramsSerializer: (params) => qs.stringify(params),
        }
      )
      .then(() => {
        setIsLoading(false);
        toast.success(
          <>
            <span className="fs-1">
              <FormattedMessage id="reports.request.success_header" />
            </span>
            <FormattedMessage
              id="reports.request.success_message"
              tagName="p"
            />
          </>
        );
      })
      .catch(() => {
        setIsLoading(false);
        toast.error(
          <>
            <span className="fs-1">
              <FormattedMessage id="reports.request.failure_header" />
            </span>
            <FormattedMessage
              id="reports.request.failure_message"
              tagName="p"
            />
          </>
        );
      });
  };

  return (
    <Button
      className={props.className}
      variant="falcon-default"
      size="sm"
      onClick={() => makeReportRequest(props.module)}
      disabled={isLoading}
    >
      {props.children}
    </Button>
  );
};

export default RequestReportButton;
