import { setItemToStore } from "../helpers/utils";

export const configReducer = (state, action) => {
  const { type, payload } = action;
  switch (type) {
    case "SET_CONFIG":
      if (payload.setInStore) {
        setItemToStore(payload.key, payload.value);
      }
      return {
        ...state,
        [payload.key]: payload.value,
      };
    case "RESET":
      return {
        ...state,
      };
    default:
      return state;
  }
};
