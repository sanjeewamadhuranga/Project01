import { Dependency } from "../reducers/types";

//get all screen objects
export const getAllScreenObjects = (screenList) => {
  const screens = screenList.map((item) => item?.screens);
  return Array.prototype.concat(...screens);
};

//get all screen object length
export const getAllScreenLength = (screenList) => {
  return getAllScreenObjects(screenList).length;
};

export const getReactSelectCompatibleList = (array: Array<string>) => {
  return array.map((item) => ({
    value: item,
    label: item,
  }));
};

/** Get the business type from the dependencies values saved in the db */
export const getBusinessType = (dependencies: Array<Dependency>) => {
  return dependencies[0]?.field;
};

/** Get the business types from the dependencies values saved in the db */
export const getSelectedBusinessTypes = (
  dependencies: Array<Dependency>
): Array<string> => {
  return dependencies[0]?.value;
};
