import React, { useEffect, useState } from "react";
import FlowHeader from "./FlowHeader";
import FlowContainer from "./FlowContainer";
import FlowProvider from "./FlowProvider";
import { Request } from "../../services/request";
import { FlowState } from "../../reducers/types";
import { Spinner } from "react-bootstrap";
export const INITIAL_STATE: FlowState = {
  isPublished: true,
  sections: [],
  flowModal: {
    modalContent: {
      modalTitle: "",
    },
    show: false,
    size: "xl",
    step: 1,
    isSteppable: true,
    maxSteps: undefined,
    addScreen: {
      selectedCategory: "ALL",
    },
  },
  key: "",
  name: "",
  locales: [...(window?.APP_SETTINGS?.enabledLanguages ?? [])],
};

interface Prop {
  flowid: string;
}

const Flow = (id: Prop) => {
  const [flowData, setFlowData] = useState<FlowState>();
  useEffect(() => {
    Request.fetchData(`/onboarding/flows/${id?.flowid}`).then((res: any) => {
      setFlowData({ ...INITIAL_STATE, ...res });
    });
  }, [id?.flowid]);

  return (
    <>
      {!!flowData ? (
        <FlowProvider flowData={flowData}>
          <FlowHeader />
          <FlowContainer />
        </FlowProvider>
      ) : (
        <div className="d-flex flex-column align-content-center align-items-center mt-6">
          <Spinner animation="grow" />
        </div>
      )}
    </>
  );
};

export default Flow;
