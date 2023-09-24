import { createContext } from "react";
import { INITIAL_STATE } from "../components/Flow/Flow";
import { FlowContextType } from "../components/Flow/FlowProvider";
import { settings } from "../config/general";

const AppContext: any = createContext(settings);

const noop = (): void => {}; // eslint-disable-line @typescript-eslint/no-empty-function

export const FlowContext = createContext<FlowContextType>({
  flowState: INITIAL_STATE,
  flowDispatch: noop,
});

export default AppContext;
