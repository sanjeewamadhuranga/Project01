import React, { useReducer } from "react";
import { FlowContext } from "../../context/Context";
import { flowReducer } from "../../reducers/flowReducer";
import { FlowAction, FlowState } from "../../reducers/types";

interface Props {
  children: React.ReactNode;
  flowData?: FlowState;
}

export type FlowContextType = {
  flowState: FlowState;
  flowDispatch: React.Dispatch<FlowAction>;
};

const FlowProvider = ({ flowData, children }: Props) => {
  const [flowState, flowDispatch] = useReducer(flowReducer, flowData);

  return (
    <FlowContext.Provider
      value={{ flowState: flowState, flowDispatch: flowDispatch }}
    >
      {children}
    </FlowContext.Provider>
  );
};

export default FlowProvider;
